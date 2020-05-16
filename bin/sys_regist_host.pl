#!/usr/bin/perl

use haruca;
use DBI;
use strict;

my ($key,$value,$id ,$categorycode ,$officecode ,$dbh ,$sth ,$sql ,$code ,$target,$type);
my @registed_hosts;
my @cacti_hosts;

haruca::get_args();

foreach(@main::opts){
  if(($_ eq "h")||($_ eq "help")){
    print "usage : $0 --id=ID --categorycode=categorycode  --officecode=officecode \n";
    print "usage : $0 \n";
    print "\n";
    print_no_regist_hosts();
    exit;
  }
}

if($#ARGV == -1){
  #引数なしの場合は全部を対象にする
  $dbh = haruca::connect_db();
  $sql  = "select id from plugin_haruca_host order by id";
  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($code));
  while($sth->fetch){
    push(@registed_hosts,$code);
  }
  $sth->finish;
  
  $sql  = "select id from host order by id";
  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($code));
  while($sth->fetch){
    push(@cacti_hosts,$code);
  }
  $sth->finish;

  # plugin_haruca_host にあって cacti に登録されていないホストは削除する
  foreach $target (@registed_hosts){
    if(!grep(/$target/,@cacti_hosts)){
      $sql = "delete from plugin_haruca_host where id=$target";
      $dbh->do($sql);
      print "DELETE : id=$target\n";
    }
  }

  # cacti にあって plugin_haruca_host にないホストはデフォルトのcategory/officeで登録する。
  foreach $target (@cacti_hosts){
    if(!grep(/$target/,@registed_hosts)){
      regist_host($target,"categorycode",1);
      regist_host($target,"officecode",1);
    }
  }


  $dbh->disconnect;


}else{
  #引数ありの場合はIDとcategorycodeとofficecodeを反映させる
  foreach(@main::opts){
    $key   = (split(/=/,$_))[0];
    $value = (split(/=/,$_))[1];
    if($key eq "id"){
      $id = $value;
    }
  }

  foreach(@main::opts){
    $key   = (split(/=/,$_))[0];
    $code = (split(/=/,$_))[1];

    if($key eq "categorycode"){
      $type = "categorycode";
      regist_host($id,$type,$code);
    }elsif($key eq "officecode"){
      $type = "officecode";
      regist_host($id,$type,$code);
    }

  }

  #regist_host($id,$categorycode,$officecode);

}



exit;

sub regist_host{
  my $id =shift;
  my $type = shift;
  my $code = shift;

  # 各パラメータの数値チェック
  if(($id.$officecode) !~ /^\d+$/){
    print "Invalid code\n";
    return 0;
  }

  print "$type : $code\n";

  my ($sql,$check,$buf,$str_db);

  $str_db = $type;
  $str_db =~ s/code//g;

  my $dbh = haruca::connect_db();

  # cacti hostデータベースの存在チェック
  $sql  = "select concat(description,'(',hostname,')') from host where id = '$id'";
  $check = $dbh->selectrow_array($sql);
  if(!$check){
    print "ERROR : NO ID : id $id was not registed on host database.\n";
    $dbh->disconnect;
    return 0;
  }else{
    $buf .= "$check ";
  }
  
  # カテゴリの存在チェック
  $sql  = "select $type from plugin_haruca_".$str_db." where $type = $code";
  $check = $dbh->selectrow_array($sql);
  if(!$check){
    print "ERROR : NO $type CODE : $type $code was not registed on category database.\n";
    $dbh->disconnect;
    return 0;
  }else{
    $buf .= "$check ";
  }
  
  # idの重複チェック
  $sql  = "select id from plugin_haruca_host where id = '$id' ";
  if($dbh->selectrow_array($sql)){
    $sql  = "update plugin_haruca_host set ";
    $sql .= "$type=$code where id=$id";
    print "UPDATE : id=$id\n";
  }else{
    $sql  = "insert into plugin_haruca_host ";
    $sql .= "(id,$type) values ($id,$code)";
    print "INSERT : id=$id\n";
  }

  ### 登録 ###
  $dbh->do($sql);
  $dbh->disconnect;

  return;
  

}


sub regist_host_old{

  my $id           = $_[0];
  my $categorycode = $_[1];
  my $officecode   = $_[2];

  # 各パラメータの数値チェック
  if(($id.$categorycode.$officecode) !~ /^\d+$/){
    print "Invalid code\n";
    return 0;
  }
  
  my $dbh = haruca::connect_db();
  my $sql;
  my $check;
  my $buf;


  # cacti hostデータベースの存在チェック
  $sql  = "select concat(description,'(',hostname,')') from host where id = '$id'";
  $check = $dbh->selectrow_array($sql);
  if(!$check){
    print "ERROR : NO ID : id $id was not registed on host database.\n";
    $dbh->disconnect;
    return 0;
  }else{
    $buf .= "$check ";
  }
  
  # カテゴリの存在チェック
  $sql  = "select categoryname from plugin_haruca_category where categorycode = '$categorycode'";
  $check = $dbh->selectrow_array($sql);
  if(!$check){
    print "ERROR : NO CATEGORY CODE : categorycode $categorycode was not registed on category database.\n";
    $dbh->disconnect;
    return 0;
  }else{
    $buf .= "$check ";
  }
  
  # 拠点情報の存在チェック
  $sql  = "select officename from plugin_haruca_office where officecode = '$officecode'";
  $check = $dbh->selectrow_array($sql);
  if(!$check){
    print "ERROR : NO OFFICE CODE : officecode $officecode was not registed on office database.\n";
    $dbh->disconnect;
    return 0;
  }else{
    $buf .= "$check ";
  }
  
  # idの重複チェック
  $sql  = "select id from plugin_haruca_host where id = '$id' ";
  if($dbh->selectrow_array($sql)){
    $sql  = "update plugin_haruca_host set ";
    $sql .= "categorycode=$categorycode , officecode=$officecode where id=$id";
    print "UPDATE : id=$id\n";
  }else{
    $sql  = "insert into plugin_haruca_host ";
    $sql .= "(id,categorycode,officecode) values ($id,$categorycode,$officecode)";
    print "INSERT : id=$id\n";
  }

  ### 登録 ###
  $dbh->do($sql);
  $dbh->disconnect;

  return;
  
}


sub print_no_regist_hosts{
  my $dbh = haruca::connect_db();
  my $sql;
  my $sth;
  my ($id,$hostname,$description,$categoryname,$officename,$categorycode,$officecode);

  # 未登録ホストの一覧表示
  $sql  = "select host.id,hostname,description,";
  $sql .= " plugin_haruca_category.categoryname,plugin_haruca_office.officename from host ";
  $sql .= " left join plugin_haruca_host     on host.id = plugin_haruca_host.id ";
  $sql .= " left join plugin_haruca_category on plugin_haruca_host.categorycode = plugin_haruca_category.categorycode ";
  $sql .= " left join plugin_haruca_office   on plugin_haruca_host.officecode = plugin_haruca_office.officecode";

  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($id,$hostname,$description,$categoryname,$officename));

  print "Not Registration Hosts are : \n";
  while($sth->fetch){
    if(($categoryname eq "")&&($officename eq "")){
      print "  $id $description ($hostname) $categoryname $officename\n";
    }
  }
  print "\n";
  # 未登録ホストの一覧表示

  # カテゴリ一覧表示
  $sql  = "select categorycode,categoryname from plugin_haruca_category";
  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($categorycode,$categoryname));
  print "Categorys are : \n";
  while($sth->fetch){
    if($categorycode ne 0){
      print "  $categorycode $categoryname\n";
    }
  } 
  print "\n";
  # カテゴリ一覧表示

  # 拠点一覧表示
  $sql  = "select officecode,officename from plugin_haruca_office";
  $sth = $dbh->prepare($sql);
  $sth->execute;
  $sth->bind_columns(undef,\($officecode,$officename));
  print "offices are : \n";
  while($sth->fetch){
    if($officecode ne 0){
      print "  $officecode $officename\n";
    }
  } 
  print "\n";
  # 拠点一覧表示

  $dbh->disconnect;
}
