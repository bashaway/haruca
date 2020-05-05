#!/usr/bin/perl

use haruca;
use DBI;
use strict;
use threads;
use threads::shared;
use Thread::Semaphore;

my ($result,$hostcode);

## 定型ログ書き出し（標準出力）
#haruca::prt_logdate("SYS_GET_RTT_ALL START");

my @hosts = split(/\n/,`${main::config_haruca{'binpath'}}search . -c`);

# 100個で１スレッドを消費
my $div = 100;

my $buf;
my @hosts_th;
my @thrs = ();
my ($i,$j);

our %rtt_host :shared;

for($i=0;$i<@hosts;){
 $buf = "";
 for($j=0;$j<$div;$j++){
   $buf .= "$hosts[$i]\n";
   $i++;
 }
 push(@hosts_th,$buf);
}



$j = 0;
foreach(@hosts_th){
 $thrs[$j] = threads->new(\&get_rtt, $_);
 $j++;
}

for($j=0; $j < @thrs; $j++){
 $thrs[$j]->join;
}


## 定型ログ書き出し
#haruca::prt_logdate("SYS_GET_RTT_ALL END");

update_db();

exit;

sub get_rtt{

 my $list = shift;
 foreach(split(/\n/,$list)){
   get_rtt_core($_);
 }
 threads->yield();


}


sub get_rtt_core{

  my $hostcode = shift;
  my $rtt;

  $rtt = haruca::getrtt(haruca::hostcode_to_hostname($hostcode));
  if(($rtt =~ /$main::config_haruca{'ping_fail_str'}/)||($rtt eq "")){ $rtt = -1; }

  $rtt_host{$hostcode}=$rtt;

  return;
}


sub update_db{
  my ($prev_rtt ,$prev_trap ,$result ,$rtt ,$sql,$hostname);

  my $dbh = haruca::connect_db();

  foreach $hostcode (keys %rtt_host){
    $rtt = $rtt_host{$hostcode};
    $hostname = haruca::hostcode_to_hostname($hostcode);
    $hostcode =~ s/\ +//g;
    #print "$hostname($hostcode) : $rtt\n";

    $sql = "select value from plugin_haruca_rtt where hostcode = $hostcode order by gettime desc";
    $prev_rtt = $dbh->selectrow_array($sql);

    $sql = "select oidstring from plugin_haruca_traplog where oidstring like 'ping%' and hostcode = $hostcode order by gettime desc";
    $prev_trap = $dbh->selectrow_array($sql);

    if($prev_rtt eq ""){
      $sql = "insert into plugin_haruca_rtt (hostcode,value) values ($hostcode,$rtt)";
    }else{
      $sql = "update plugin_haruca_rtt set value=$rtt , gettime=now() where hostcode=$hostcode";
    }
    $dbh->do("$sql");

    if($rtt == -1){
      #前回のrttが-1でかつ最新のトラップにpingfailがなければpingfail擬似トラップ発生

      if(($prev_rtt == -1)||($prev_rtt eq "")){
        if($prev_trap ne "pingfail"){
          #print "pingfail : $hostname $hostcode\n";
          $sql = "insert into plugin_haruca_traplog (hostcode,oidstring,gettime) values ($hostcode,'pingfail',now())";
          $dbh->do("$sql");
  
          if($dbh->selectrow_array("select alertmail from plugin_haruca_traptype where oidstring = 'pingfail'")){
            haruca::send_alert($hostname,"","pingfail","","","");
          }
  
        }
      }
    }else{
      #前回のrttが-1以外でかつ最新のトラップにpingfailがあればpingsuccessトラップ発生
      #if($prev_rtt != -1){
        if($prev_trap eq "pingfail"){
          #print "pingsuccess : $hostname $hostcode\n";
          $sql = "insert into plugin_haruca_traplog (hostcode,oidstring,gettime) values ($hostcode,'pingsuccess',now())";
          $dbh->do("$sql");
  
          if($dbh->selectrow_array("select alertmail from plugin_haruca_traptype where oidstring = 'pingsuccess'")){
            haruca::send_alert($hostname,"","pingsuccess","","","");
          }
  
        }
      #}
    }
  }

  $dbh->disconnect;

}


