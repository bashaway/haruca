#!/usr/bin/perl

use haruca;
use DBI;
use threads;
use Thread::Semaphore;

use strict;

my ($hostname,$hostcode,$i,$j);



my $dbh = haruca::connect_db();
my $thread_number = $dbh->selectrow_array("select value from plugin_haruca_settings where item = 'proc_per_thread'");

if(($thread_number !~ /^\d+$/)||($thread_number == 0)){
  $thread_number = 10;
}
$dbh->disconnect;

haruca::prt_logdate("SYS_GET_LOG_ALL START");
my @hosts = split(/\n/,`${main::binpath}search . -hc`);

my $div = ( (@hosts/$thread_number) == int(@hosts/$thread_number) ?  (@hosts/$thread_number) : int((@hosts/$thread_number) + 1) );

my $cnt;
my $buf;
my @hosts_th;
my @thrs = ();

$cnt=0;
for($i=0;$i<@hosts;){
 $buf = "";
 for($j=0;$j<$div;$j++){
   $buf .= "$hosts[$i]\n";
   $i++;
 }
 push(@hosts_th,$buf);
 $cnt++;
}

$j = 0;
foreach(@hosts_th){
 $thrs[$j] = threads->new(\&get_log, $_);
 $j++
}


for($j=0; $j< @thrs; $j++){
 $thrs[$j]->join;
}

haruca::prt_logdate("SYS_GET_LOG_ALL END");

exit;


sub get_log{
 my $list = shift;
 foreach(split(/\n/,$list)){
   $hostname = (split(/\ +/,$_))[0];
   $hostcode = (split(/\ +/,$_))[1];
   if(haruca::pingcheck($hostname)){
     get_log_core($hostname,$hostcode,"success");
   }else{
     get_log_core($hostname,$hostcode,"fail");
   }

 }
 threads->yield();
}


sub get_log_core{
  my $hostname  = shift;
  my $hostcode  = shift;
  my $status    = shift;
  my $file_tmp = `${main::binpath}mkpasswd 10 alnum file`;

  my $dbh = haruca::connect_db();

  my ($sql, $sth, $cmd, $cmd_diff, $tmp, $cmds_ref, $logtypecode, $ref_save_dates,$file_new,$file_old);
  my ( $new_values_all, $new_log_value, $new_log_value_q, $old_log_value, $old_log_value_q, $old_log_value_comp_q);
  my ($ignore_str,$diffcheck,$oldtime, $cycle, $ite, $i);
  my ( @cmds, @results, @save_dates);
  my %new_values;

  $dbh->{AutoCommit}=1;


  # 実施コマンド抽出
  $sql  = "select plugin_haruca_logtype.loggetcmd from plugin_haruca_host ";
  $sql .= " inner join plugin_haruca_cat_get_log on plugin_haruca_host.categorycode = plugin_haruca_cat_get_log.categorycode ";
  $sql .= " inner join plugin_haruca_logtype on plugin_haruca_logtype.logtypecode = plugin_haruca_cat_get_log.logtypecode ";
  $sql.= " where plugin_haruca_host.id = $hostcode";
  $cmds_ref = $dbh->selectcol_arrayref($sql);
  foreach(@$cmds_ref){
    $cmd .= "$_,";
    push(@cmds,$_);
  }
  chop($cmd);

  if($cmd eq ""){
    $dbh->disconnect;
    return 0;
  }


  # 新ログ取得
  if($status eq "fail"){
    #pingが通っていなかった場合はすべての取得ログをping_failとする。
    foreach(@cmds){ $new_values{$_} = $main::ping_fail_str; }
    haruca::prt_logdate("SYS_GET_LOG_FAIL $hostname");
  }else{

    # ログ取得を試行し、失敗の場合は３回まで繰り返し試行する
    $new_values_all = "";
    foreach( 1 .. 3 ){
      $new_values_all = `${main::binpath}router '$hostname' $cmd 2> /dev/null`;
      if($new_values_all){
        last;
      }
    }


    if($new_values_all){
      @results = split(/${main::delim_line}/,$new_values_all);
      for($i=1;$i<$#results;$i=$i+2){
        $cmd = $results[$i];
        chomp($cmd);
        $cmd =~ s/\ +$//g;
        $new_values{$cmd} = $results[$i+1];
      }
      #print "$new_values{'show tech'}\n";
    }else{
      #なんらかのエラーで返り血がなかったばあいはすべての取得ログをcan't get infoにする
      foreach(@cmds){ $new_values{$_} = "$main::unknown_error_str"; }
      haruca::prt_logdate("SYS_GET_LOG_FAIL $hostname");
    }
  }

  # 新ログをDBに放り込んでいく
  foreach(keys(%new_values)){
    chomp($_);

    # logtypecode 判断
    $logtypecode   = $dbh->selectrow_array("select logtypecode from plugin_haruca_logtype where loggetcmd = \"$_\"");


    # 新ログの取得
    $new_log_value = $new_values{$_};
    $new_log_value_q = $dbh->quote($new_log_value);

    # 旧ログの取得
    $sql  = "select gettime,value from plugin_haruca_log ";
    $sql .= " where hostcode = $hostcode and logtypecode = $logtypecode order by gettime desc ";

    $sth = $dbh->prepare($sql);
    $sth->execute;
    $sth->bind_columns(undef,\($oldtime,$old_log_value));
    $sth->fetch;
    $sth->finish;

    $old_log_value = haruca::unquote($old_log_value);
    $old_log_value_q = $dbh->quote($old_log_value);
    $old_log_value_comp_q = $dbh->quote(compress_text($old_log_value));

    #cycleに関係なく、旧ログに何もない場合（初回起動時）は
    #旧ログに保存する。
    if(!$old_log_value){
      # 旧ログがなかった場合は旧ログDBに現在ログを保存
      $sql  = "insert into plugin_haruca_log (hostcode,logtypecode,value) ";
      $sql .= " values ($hostcode,$logtypecode,$new_log_value_q)";
      $dbh->do($sql);
      next;
    }

    # diffcheckが1なら差分保存
    # diffcheckが0なら世代保存
    $sql = "select cycle from plugin_haruca_logtype where logtypecode = \"$logtypecode\"";
    $cycle = $dbh->selectrow_array($sql);

    $sql = "select diffcheck from plugin_haruca_logtype where logtypecode = \"$logtypecode\"";
    $diffcheck = $dbh->selectrow_array($sql);

    if($diffcheck == 0){
      # 世代保存の場合はエラーで取得できなくても、取得結果を保存し
      # 圧縮ログへもエラーのまま残すことにする。

      # 新ログがどんな値であろうと旧ログとして更新しておく
      # 前は取得指定なったログをとるようになると、エントリが無いため、UPDATEができないので、そのときはINSERTにする。
      if($dbh->selectrow_array("select hostcode from plugin_haruca_log where hostcode = $hostcode and logtypecode = $logtypecode")){
        $sql  = "update plugin_haruca_log set gettime = now() ,value = $new_log_value_q ";
        $sql .= " where hostcode = $hostcode and logtypecode = $logtypecode";
      }else{
        $sql  = "insert into plugin_haruca_log (gettime,value,hostcode,logtypecode) ";
        $sql .= " values (now(),$new_log_value_q,$hostcode,$logtypecode)";
      }
      $dbh->do($sql);


      # 旧ログを圧縮ログにコピー
      $sql  = "insert into plugin_haruca_logold (hostcode,gettime,logtypecode,value) ";
      $sql .= " values ($hostcode,\"$oldtime\",$logtypecode,$old_log_value_comp_q)";
      $dbh->do($sql);

      # 増えすぎた圧縮ログDBは削除（cycleを越えたログは古いものから削除）
      $sql  = "select gettime from plugin_haruca_logold ";
      $sql .= " where hostcode = $hostcode and logtypecode = $logtypecode order by gettime asc";
      $ref_save_dates = $dbh->selectcol_arrayref($sql);
      @save_dates = @$ref_save_dates;
      $ite = $#save_dates - $cycle;

      for($i=0;$i<=$ite;$i++){
        $sql  = "delete from plugin_haruca_logold where ";
        $sql .= " hostcode = $hostcode and logtypecode = $logtypecode and gettime = \"$save_dates[$i]\"";
        $dbh->do($sql);
      }
      # ここまで：圧縮ログルーチン
    }else{
      # ここから：差分更新の処理
      # 旧ログおよび圧縮ログにはpingfailのものは入れてはいけない！！

      # 新ログがpingfailの場合はなにもしない。
      # → 常に最新のデータが参照できるようにしなければならないので
      if( ($new_log_value =~ /$main::ping_fail_str/)||
          ($new_log_value =~ /$main::unknown_error_str/)){
        next;
      }

      # 新ログと旧ログを比較し、差異があれば旧ログを圧縮ログにコピーする
      # 新ログが正常に取得できていれば、旧ログとして更新する。
      if($dbh->selectrow_array("select hostcode from plugin_haruca_log where hostcode = $hostcode and logtypecode = $logtypecode")){
        $sql  = "update plugin_haruca_log set gettime = now() ,value = $new_log_value_q ";
        $sql .= " where hostcode = $hostcode and logtypecode = $logtypecode";
      }else{
        $sql  = "insert into plugin_haruca_log (gettime,value,hostcode,logtypecode) ";
        $sql .= " values (now(),$new_log_value_q,$hostcode,$logtypecode)";
      }
      $dbh->do($sql);


      # 新ログを一時ファイルに書き出し
      $file_new = `${main::binpath}mkpasswd 10 alnum file`;
      open(FILE,"> ${main::tmppath}$file_new");
      print FILE $new_log_value;
      close(FILE);

      # 旧ログを一時ファイルに書き出し
      $file_old = `${main::binpath}mkpasswd 10 alnum file`;
      open(FILE,"> ${main::tmppath}$file_old");
      print FILE $old_log_value;
      close(FILE);

      # diffはモジュールをつかいたくないので、システムのdiffを利用する

      # diffコマンドを用意
      # とりあえずstartupコンフィグ用のdiff文字列
      $cmd_diff  = " diff ${main::tmppath}$file_old ${main::tmppath}$file_new ";
      $cmd_diff .= " | egrep '(^<|^>)' | sed \"s/^[<>]\ *//g\" ";


      $ignore_str   = $dbh->selectrow_array("select ignore_str from plugin_haruca_logtype where logtypecode = $logtypecode");
      if($ignore_str){
        $cmd_diff .= "|egrep -v '(<<>>|$ignore_str)' ";
      }else{
        $cmd_diff .= "|egrep -v '(<<>>)' ";
      }


      if(`$cmd_diff`){
        $sql  = "insert into plugin_haruca_logold (hostcode,gettime,logtypecode,value) ";
        $sql .= " values ($hostcode,\"$oldtime\",$logtypecode,$old_log_value_comp_q)";
        $dbh->do($sql);
      }

      system("rm -f ${main::tmppath}$file_old ${main::tmppath}$file_new");
      # ここまで：差分保存ルーチン

    }
  }

  $dbh->disconnect;

}


sub compress_text{
  # $TEXT = compress_text($text);
  # $text : 圧縮前の文字列
  # $TEXT : 圧縮後の文字列
  my $text = $_[0];
  my $file_tmp = `${main::binpath}mkpasswd 10 alnum file`;
  my $result;

  open(FILE,"> ${main::tmppath}$file_tmp");
  print FILE $text;
  close(FILE);
  $result = `cat ${main::tmppath}$file_tmp | gzip - -c`;
  system("rm -f ${main::tmppath}$file_tmp");

  return $result;
}
