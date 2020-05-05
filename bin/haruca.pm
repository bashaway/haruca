#!/usr/bin/perl

use DBI;
use Encode;
use strict;
use Net::SMTP;


#  undef @main::opts;
#  undef @main::stdin;
#  undef @main::args;
#  undef $main::pos;



my $conffile = "/usr/share/cacti/plugins/haruca/bin/conffile";

my ($line,$key,$val);

open(FILE, $conffile);
my @def = <FILE>;
close(FILE);

foreach $line (@def){
  chomp($line);
  $key = ((split(/\ *=\ */,$line))[0]);
  $val = ((split(/\ *=\ */,$line))[-1]);
  $main::config_haruca{$key} =  $val;
}

#foreach ( keys %main::config_haruca ){
#  print "$_ : $main::config_haruca{$_}\n";
#}


sub get_param{
  my $line = $_[0];
  my $param;
  $param = (split(/\ *=\ */,$line))[-1];
  $param =~ s/"//g;
  $param =~ s/'//g;
  $param =~ s/;//g;
  chomp($param);
  return $param;
}


package haruca;

my $CONF_DB =
    {host =>    $main::config_haruca{'database_hostname'},
     port=>     $main::config_haruca{'database_port'},
     db_name=>  $main::config_haruca{'database_default'},
     db_user=>  $main::config_haruca{'database_username'},
     db_pass=>  $main::config_haruca{'database_password'},
     db_opt=>{
              AutoCommit=>0,
              RaiseError=>1,
              mysql_enable_utf8=>1,
              on_connect_do => [
                                "SET NAMES 'utf8'",
                                "SET CHARACTER SET 'utf8'"
                               ],
             }
    };

sub pmcheck{
  return "OK";
}


sub connect_db {
    my $db  = "DBI:mysql:database=$CONF_DB->{db_name};host=$CONF_DB->{host}";
    my $dbh = DBI->connect($db, $CONF_DB->{db_user}, $CONF_DB->{db_pass}, $CONF_DB->{db_opt}); 
    $dbh->do("set names utf8");
    return $dbh;
}


sub get_localtime{
  my($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime;
  my $buf;
  $buf = sprintf("%04d-%02d-%02d-%02d-%02d-%02d",$year+1900,$mon+1,$mday,$hour,$min,$sec);
  return $buf;
}

sub prt_logdate{
  my $str = $_[0];
  my $date;
  chomp($date = `date \'+%Y-%m-%d %H:%M:%S\'`);

  open(FILE,">> ${main::config_haruca{'logpath'}}");
  #print FILE get_localtime()." $str\n";
  print FILE "$date $str\n";
  close(FILE);

}


sub hostcode_to_hostname{
  my $dbh = connect_db();
  my $id = $_[0];

  my $sql = "select description from host where id = $id";
  my $sth;
  my $hostname;

  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($hostname));
  $sth->fetch;
  $sth->finish;
  $dbh->disconnect;
  return $hostname;
}

sub hostname_to_snmp_community{
  my $dbh = connect_db();
  my $hostname = $dbh->quote($_[0]);

  my $sql = "select snmp_community from host where description = $hostname";
  my $sth;
  my $snmp_community;

  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($snmp_community));
  $sth->fetch;
  $sth->finish;
  $dbh->disconnect;
  return $snmp_community;
}

sub hostname_to_hostcode{
  my $dbh = connect_db();
  my $hostname = $dbh->quote($_[0]);

  my $sql = "select id from host where description = $hostname";
  my $sth;
  my $id;

  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($id));
  $sth->fetch;
  $sth->finish;
  $dbh->disconnect;
  return $id;
}

sub hostname_to_adrs{
  my $dbh = connect_db();
  my $hostname = $dbh->quote($_[0]);

  my $sql = "select hostname from host where description = $hostname";
  my $sth;
  my $adrs;

  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($adrs));
  $sth->fetch;
  $sth->finish;
  $dbh->disconnect;
  return $adrs;
}


sub ifsort{
  my @ar = @_;
  my $buf;
  my @new;
  my @tmp;
  my @ret;
  my $ifsub_check;
  my $ifsub;
  my $ifmain;
  my $ifname;
  my $ifnum;
  my @ifport;
  my $line;

  foreach $line (sort @ar){

    $ifname = (split(/[0-9]/,$line))[0];
    $ifnum  = (split(/[a-zA-Z]/,$line))[-1];

    $ifnum  = $line;
    $ifnum =~ s/^[a-zA-Z-]+//g;


    if($line =~ /\./){
      $ifmain = (split(/\./,$ifnum))[0];
      $ifsub  = (split(/\./,$ifnum))[-1];
      $ifsub_check = "period";
    }elsif($line =~ /:/){
      $ifmain = (split(/:/,$ifnum))[0];
      $ifsub  = (split(/:/,$ifnum))[-1];
      $ifsub_check = "colon";
    }else{
      $ifmain = (split(/\./,$ifnum))[0];
      $ifsub  = (split(/\./,$ifnum))[-1];
      $ifsub_check = "";
    }

    if($ifmain eq $ifsub){$ifsub="";}

    @ifport = split(/\//,$ifmain);

    $buf = $ifname;
    foreach(@ifport){
      $buf .= sprintf("%05d/",$_);
    }
    chop($buf);

    if($ifsub ne ""){
      if($ifsub_check eq "period"){
        $buf .= sprintf(".%05d",$ifsub);
      }else{
        $buf .= sprintf(":%05d",$ifsub);
      }
    }
    push(@tmp,$buf);
  }


  foreach(sort @tmp){
    if($_ =~ /loopback/i){
      push(@new,$_);
    }
  }
  foreach(sort @tmp){
    if($_ !~ /loopback/i){
      push(@new,$_);
    }
  }

  foreach $line (@new){
    $ifname = (split(/[0-9]/,$line))[0];
    $ifnum  = (split(/[a-zA-Z]/,$line))[-1];
    if($line =~ /\./){
      $ifmain = (split(/\./,$ifnum))[0];
      $ifsub  = (split(/\./,$ifnum))[-1];
    }else{
      $ifmain = (split(/\:/,$ifnum))[0];
      $ifsub  = (split(/\:/,$ifnum))[-1];
    }
    if($ifmain eq $ifsub){$ifsub="";}
    @ifport = split(/\//,$ifmain);
    $buf = $ifname;
    foreach(@ifport){
      $buf .= (0+$_)."/";
    }
    chop($buf);
    if($ifsub ne ""){
      if($line =~ /\./){
        $buf .= ".".($ifsub+0);
      }else{
        $buf .= ":".($ifsub+0);
      }
    }
    push(@ret,$buf);
  }

  return @ret;
}

sub unquote{
  my $string = $_[0];
  $string =~ s/^'//;
  $string =~ s/'$//;
  return $string;
}

sub getrtt_rapid{
  my $dbh = connect_db();
  my $hostname = $dbh->quote($_[0]);
  my $value;
  my $sql;
  my $sth;

  $sql  = "select plugin_haruca_rtt.value from plugin_haruca_rtt inner join host on host.id = plugin_haruca_rtt.hostcode ";
  $sql .= " where host.description = $hostname order by gettime desc;";

  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($value));
  $sth->fetch;
  if(!$value){
    $value = getrtt($_[0]);
  }

  if($value == -1){$value = $main::config_haruca{'ping_fail_str'};}
  $sth->finish;
  $dbh->disconnect;
  return $value;
}

sub pingcheck_rapid{
  my $dbh = connect_db();
  my $hostname = $dbh->quote($_[0]);
  my $value;
  my $sql;
  my $sth;

  $sql  = "select plugin_haruca_rtt.value from plugin_haruca_rtt inner join host on host.id = plugin_haruca_rtt.hostcode ";
  $sql .= " where host.description = $hostname order by gettime desc;";

  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($value));
  $sth->fetch;
  if(!$value){
    $value = getrtt($_[0]);
  }


  $sth->finish;
  $dbh->disconnect;

  if(($value == -1)||($value =~ /$main::config_haruca{'ping_fail_str'}/)){
    return 0;
  }else{
    return 1;
  }
}


sub getrtt{
  my $hostname = $_[0];
  my $result;
  my $ret;

  my $adrs = hostname_to_adrs($hostname);

  $result = `$main::config_haruca{'pingpath1'} $adrs`;
  if($result =~ /100% packet loss/){
    $result = `$main::config_haruca{'pingpath2'} $adrs`;
    if($result =~ /100% packet loss/){
      $ret = $main::config_haruca{'ping_fail_str'};
    }else{

      $result = (split(/\n/,$result))[-1];
      $result = (split(/=/,$result))[-1];
      $result = (split(/\//,$result))[1];
      $result = (split(/\./,$result))[0];

      $ret = $result;
    }
  }else{
    $result = (split(/\n/,$result))[-1];
    $result = (split(/=/,$result))[-1];
    $result = (split(/\//,$result))[1];
    $result = (split(/\./,$result))[0];
    $ret = $result;
  }
  if($ret eq "0"){ $ret = 1; }
  return $ret;

}

sub getrtt_ip{
  my $adrs = $_[0];
  my $result;
  my $ret;

  $result = `$main::config_haruca{'pingpath1'} $adrs`;
  if($result =~ /100% packet loss/){
    $result = `$main::config_haruca{'pingpath2'} $adrs`;
    if($result =~ /100% packet loss/){
      $ret = $main::config_haruca{'ping_fail_str'};
    }else{

      $result = (split(/\n/,$result))[1];
      $result = (split(/=/,$result))[-1];
      $result = (split(/\//,$result))[1];
      $result = (split(/\./,$result))[0];

      $ret = $result;
    }
  }else{
    $result = (split(/\n/,$result))[-1];
    $result = (split(/=/,$result))[-1];
    $result = (split(/\//,$result))[1];
    $result = (split(/\./,$result))[0];
    $ret = $result;
  }
  if($ret eq "0"){ $ret = 1; }
  return $ret;

}

sub pingcheck{
  my $hostname = $_[0];
  my $result;
  my $ret;
  my $cmd;

  my $adrs = hostname_to_adrs($hostname);

  $result = `$main::config_haruca{'pingpath1'} $adrs`;
  if($result =~ /100% packet loss/){
    $result = `$main::config_haruca{'pingpath2'} $adrs`;
    if($result =~ /100% packet loss/){
      $ret = 0;
    }else{
      $ret = 1;
    }
  }else{
    $ret = 1;
  }

  return $ret;

}



sub get_max_len{
  my $max;
  $max = 0;
  foreach(@_){

    #print "$_ : ".length(Encode::encode('euc-jp',Encode::encode('utf8',$_)))."\n";
    #if($max < length(Encode::encode('euc-jp',Encode::encode('utf8',$_)))){
    #  $max = length(Encode::encode('euc-jp',Encode::encode('utf8',$_)));
    #}

    #print "$_ : ".length(Encode::encode('euc-jp',$_))."\n";
    if($max < length(Encode::encode('euc-jp',$_))){
      $max = length(Encode::encode('euc-jp',$_));
    }

  }
  return $max;
}

sub len_form{
  my $str;
  my $max;
  my $name;

  $str = $_[0];
  $max = $_[1];

  #$str =~ s/\ /_/g;
  $str =~ s/\ /_/g;

  #if( $_[2] eq "right"){
  #  $name = $str . " " x ($max - length(Encode::encode('euc-jp',Encode::decode('utf8',$str)))); 
  #}else{
  #  $name = " " x ($max - length(Encode::encode('euc-jp',Encode::decode('utf8',$str)))) . $str;
  #}

  #if( $_[2] eq "right"){
  #  $name = $str . " " x ($max - length(Encode::encode('utf8',$str))); 
  #}else{
  #  $name = " " x ($max - length(Encode::encode('utf8',$str))) . $str;
  #}

  if( $_[2] eq "right"){
    $name = $str . " " x ($max - length(Encode::encode('euc-jp',$str))); 
  }else{
    $name = " " x ($max - length(Encode::encode('euc-jp',$str))) . $str;
  }

  #if( $_[2] eq "right"){
  #  $name = $str . " " x ($max - length($str)); 
  #}else{
  #  $name = " " x ($max - length($str)) . $str;
  #}


  return $name;

}

sub get_args{
  my $one;
  my $file;
  my $stdio_flg;

  undef @main::opts;
  undef @main::stdin;
  undef @main::args;
  undef $main::pos;

  foreach $one (@ARGV){
    if($one =~ /^-/){
      if($one eq "-"){$stdio_flg=1; $one="--stdin";}
      if($one eq "--stdin"){$stdio_flg=1;}
      $one =~ s/^-+//g;
      if($one ne ""){
        push(@main::opts,$one);
      }
    }elsif($one =~ /^\+/){
      $one =~ s/^\+//g;
      $main::pos = $one+0;
    }elsif(($one =~ /^[\.\/]/)&&(($one !~ /^\.+$/)&&(-e $one))){
      $file = $one;
    }else{
      push(@main::args,$one);
    }
  }

  if($stdio_flg == 1){
    while(<STDIN>){
      chomp($_);
      $_ =~ s/\ +$//g;
      push(@main::stdin,$_);
    }
  }

  return $file;

}

sub send_alert{
  my $hostname = $_[0];
  my $address  = $_[1];
  my $trapoid  = $_[2];
  my $target   = $_[3];
  my $summary  = $_[4];
  my $description = $_[5];
  my $tmp;
  my $hostaddress;

  my $sql;
  my $body;
  my $date;
  my $dbh;

  $hostaddress = "unknown";
  $tmp = `${main::config_haruca{'binpath'}}search $hostname -ih`;
  foreach(split(/\n/,$tmp)){
    if($hostname eq (split(/\ +/,$_))[0]){
      $hostaddress = (split(/\ +/,$_))[1];
    }
  }

  $dbh = haruca::connect_db();
  $sql = "select trapname from plugin_haruca_traptype where oidstring = '$trapoid'";
  my $trapname = $dbh->selectrow_array($sql);
  $dbh->disconnect;

  my $title = "$hostname $trapname";

  if($address ne ""){ $address = "( $address )";}
  if($summary ne ""){ $summary = "( $summary )";}
  $date = get_localtime();
  $date =~ s/(\d+)-(\d+)-(\d+)-(\d+)-(\d+)-(\d+)/$1\/$2\/$3\ $4:$5:$6/g;
  $body  = "DATE information\n";
  $body .= " $date\n\n";
  $body .= "HOST information\n";
  $body .= " $hostname ( $hostaddress )\n\n";
  $body .= "TRAP information\n";
  $body .= " $trapname $summary\n\n";
  if(($target!="")&&($address!="")&&($description!="")){
    $body .= "OTHER information\n";
    $body .= " $target $address \n";
    $body .= " $description\n";
    $body .= "\n";
  }

  send_alert_mail($title,$body);
  send_alert_ipmsg($body);

}

sub send_alert_mail{
  my $dbh = haruca::connect_db();

  my $sql = "select value from plugin_haruca_settings where item = 'alert_email_from_name'";
  my $alert_email_from_name = $dbh->selectrow_array($sql);

  $sql = "select value from plugin_haruca_settings where item = 'alert_email_from_address'";
  my $alert_email_from_address = $dbh->selectrow_array($sql);

  $sql = "select value from plugin_haruca_settings where item = 'alert_email_to_address'";
  my $mail_to_address = $dbh->selectrow_array($sql);

  $sql = "select value from plugin_haruca_settings where item = 'alert_smtp_server'";
  my $alert_smtp_server = $dbh->selectrow_array($sql);

  $dbh->disconnect;


  my $header  = "";
  if($alert_email_from_name eq ""){
    $header .= "From: ${alert_email_from_address}\n";
  }else{
    $header .= "From: ${alert_email_from_name}<${alert_email_from_address}>\n";
  }
  $header .= "To: $mail_to_address\n";
  $header .= "Subject: haruca alert > $_[0]\n";
  $header .= "Content-Transfer-Encoding: 7bit\n";
  $header .= "Content-Type: text/plain;\n\n";

  my $smtp = Net::SMTP->new($alert_smtp_server);
  $smtp->mail($alert_email_from_address);
  $smtp->to($mail_to_address);
  $smtp->data();
  $smtp->datasend($header);
  $smtp->datasend($_[1]);
  $smtp->dataend();
  $smtp->quit;

}

sub send_alert_ipmsg{
  my $dbh = haruca::connect_db();

  my $sql = "select value from plugin_haruca_settings where item = 'alert_ipmsg_port'";
  my $alert_ipmsg_port = $dbh->selectrow_array($sql);

  $sql = "select value from plugin_haruca_settings where item = 'alert_ipmsg_address'";
  my $alert_ipmsg_address = $dbh->selectrow_array($sql);

  $sql = "select value from plugin_haruca_settings where item = 'alert_email_from_name'";
  my $alert_email_from_name = $dbh->selectrow_array($sql);

  foreach(split(/,/,$alert_ipmsg_address)){
    if($_ eq ""){next;}
    send_ipmsg_core($alert_email_from_name,$_,$alert_ipmsg_port,$_[0]);
  }

  $dbh->disconnect;
}


sub send_ipmsg_core{
  use Socket;
  use Sys::Hostname;

  my $from;
  if($_[0] eq ""){
    $from = "haruca";
  }else{
    $from = $_[0];
  }

  my $buf = sprintf( '1:%d:%s:%s:32:%s', int(rand(100))+1, $from,"alert", $_[3] );

  socket( SOCKET, PF_INET, SOCK_DGRAM, getprotobyname('udp'));

  my $myhost    = gethostbyname(hostname());
  my $myipaddr   = sockaddr_in( 0,      $myhost  );
  bind( SOCKET, $myipaddr);

  my $tohost   = gethostbyname( $_[1] );
  my $toaddr = sockaddr_in( $_[2], $tohost );

  send( SOCKET, $buf, 0, $toaddr);

}
1;
