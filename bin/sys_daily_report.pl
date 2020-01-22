#!/usr/bin/perl

use DBI;
use haruca;
use strict;

my ($dbh, $sth, $sql, $date, $id, $cmd, $buf, $hostname);
my($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst);
my @daily_checks;

$dbh = haruca::connect_db();

## host.id と plugin_haruca_host.id の整合をとる
system("${main::perlpath} ${main::binpath}sys_regist_host.pl");


## ログ取得
system("${main::perlpath} ${main::binpath}sys_get_log.pl");

## 独自ログなどの取得処理
## execute haruca/dat/sys_* scripts
chdir("${main::datpath}");
@daily_checks = glob "sys_*";
foreach(@daily_checks){
  system("${main::perlpath} ${main::datpath}$_");
}

## lastupdate 対象があれば cli/poller_reindex_hosts.php を実施
my @list;
my @list_prev;
chdir("${main::datpath}LastUpdate");

# 当日分のファイル取得
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time);
$date = sprintf("%04d-%02d-%02d",$year+1900,$mon+1,$mday);
@list = glob "*{$date}*";

# ログ取得が日にちをまたがってしまったときの対策
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time-(24*60*60));
$date = sprintf("%04d-%02d-%02d",$year+1900,$mon+1,$mday);
@list_prev = glob "*{$date}*";

push(@list,@list_prev);

foreach(@list){
  $hostname = (split(/-\d{4}-\d{2}-\d{2}/,$_))[0];
  $id = haruca::hostname_to_hostcode($hostname);
  haruca::prt_logdate("POLLER_REINDEX $id");
  $cmd = "php ${main::dir_base_cacti}cli/poller_reindex_hosts.php --id=$id > /dev/null";
  system($cmd);

  #そのほかにもコンフィグ更新をトリガにして実施する処理があれば追加する

}


#全部終わったらログとして保存しておく
my ($savefile,$status,$file,$result);
$status = `perl ${main::binpath}sys_statuscheck.pl`;

$file = "statcheck";
open(FILE , ">${main::tmppath}$file");
print FILE $status;
close(FILE);

$result = `cat ${main::tmppath}$file | gzip - -c`;

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time);
$savefile = sprintf("%04d%02d%02d.gz",$year+1900,$mon+1,$mday);

open(FILE , ">${main::datoldpath}$savefile");
print FILE $result;
close(FILE);

system("rm -f ${main::tmppath}$file");

$dbh->disconnect;

exit;

