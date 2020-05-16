<?php

include_once("../../include/auth.php");
include_once("../../include/config.php");
include_once("./haruca_functions.php");

global $config_haruca;

# 読み込まれるたびにharuca用のDBを最新化しとかないとまずいのです
# cacti側でデバイスを追加しただけでは、harucaのhostテーブルにhost_idとかがないので、不整合になる。
exec("{$config_haruca['perlpath']} {$config_haruca['binpath']}/sys_regist_host.pl ");

# 別窓でコンフィグを表示させる
if(isset($_REQUEST['action']) && ($_REQUEST['action'] === "show_logs_execute")){
  show_logs_execute();
  exit;
}



set_default_action();
general_header('');

$ret = page_default();

if($ret['status'] != "OK"){
  print $ret['msg'];
  html_end_box();
  bottom_footer();
  exit;
}


haruca_tabs();


switch(get_request_var('action')) {
  case "show_statuscheck":
    show_statuscheck();
    break;
  case "show_statuscheckold":
    show_statuscheckold();
    break;
  case "show_traps":
    show_traps();
    break;
  case "show_logs":
    show_logs();
    break;
  case "show_hosts":
    show_hosts();
    break;

  default:
    print $ret['msg'];
    break;
}

haruca_footer();
html_end_box();
bottom_footer();


########################################################
# statuscheck page
########################################################
function show_statuscheck(){
  global $config_haruca;

  if(empty($_REQUEST['date'])){
    print "<center>\n";
    print "	<H3>StatusCheck</H3>\n";
    print "	<HR>\n";
    print "</center>\n";
    print `{$config_haruca['perlpath']} {$config_haruca['binpath']}sys_statuscheck.pl `;
  }else{
    #$str_buf = file_get_contents("{$config_haruca['datoldpath']}{$_REQUEST['date']}");
    #print $str_buf;

    $sql = "select dayname({$_REQUEST['date']}) as dayname";
    #$result = mysql_query($sql) or die (mysql_error());
    #$rows = mysql_fetch_assoc($result);
    #$rows = db_fetch_assoc($sql);
    $rows = db_fetch_row($sql);

    $dayname = substr($rows['dayname'] , 0 , 3);
    if($dayname === "Sat"){
      $dayname = "<font color=blue>$dayname</font>";
    }elseif($dayname === "Sun"){
      $dayname = "<font color=red>$dayname</font>";
    }

    //圧縮ファイルを開く
    $zd = gzopen("{$config_haruca['datoldpath']}{$_REQUEST['date']}.gz", "r");
    $str_buf = gzread($zd, 1000000);
    gzclose($zd);

    print "<center>\n";
    print "	<H3>StatusCheck {$_REQUEST['date']} ($dayname)</H3>\n";
    print "	<HR>\n";
    print "</center>\n";
    print $str_buf;

  }
}




########################################################
# statuscheck page
########################################################
function show_statuscheckold(){
  global $config_haruca;

  #ファイルリスト取得
  $files = array();
  $dir = opendir("{$config_haruca['datoldpath']}");
  while(($file = readdir($dir)) !== FALSE){
    #if(($file != ".")&&($file != "..")){
    if(preg_match("/\d{8}\.gz/",$file)){
      $files[] = $file;
    }
  }
  closedir($dir);
  rsort($files);


  print "<center>\n";
  print "	<H3>StatusCheck (Old Log)</H3>\n";
  print "	<HR>\n";
  print "</center>\n";

  if(count($files) === 0){
    print "NO OLD LOGS<BR>\n";
  }else{

    print "";
    print "<script type=\"text/javascript\"><!--\n";
    print "function collapse(index) {\n";
    print "  var objID=document.getElementById(index);\n";
    print "  if(objID.style.display=='block') {\n";
    print "    objID.style.display='none';\n";
    print "  }else{\n";
    print "    objID.style.display='block';\n";
    print "  }\n";
    print "}\n";
    print "//--></script>\n";
  
  
    foreach($files as $key => $value){
      $date    = preg_replace("/(\d{8})\.gz/","$1",$value);
  
      $month    = substr($date,0,6);
      $months[] = $month;
  
      $info[$month][] = $date;
  
    }
  
    $flg_first = 0;
    foreach(array_unique($months) as $month){
      print "  &nbsp;&nbsp;&nbsp;<a href=\"javascript:void(0);\" onclick=\"collapse('$month');\">$month</a><BR>\n";
      if($flg_first === 0){
        print "  <div id=\"$month\" style=\"\">\n";
        $flg_first = 1;
      }else{
        print "  <div id=\"$month\" style=\"display:none\">\n";
      }
      print "    <ul>\n";
  
      foreach($info[$month] as $date){
        $sql ="select dayname($date) as dayname";
        #$result = mysql_query($sql);
        #$rows = mysql_fetch_assoc($result);
        #$rows = db_fetch_assoc($sql);
        $rows = db_fetch_row($sql);
        $dayname = substr($rows['dayname'] , 0 , 3);
        if($dayname === "Sat"){
          $dayname = "<font color=blue>$dayname</font>";
        }elseif($dayname === "Sun"){
          $dayname = "<font color=red>$dayname</font>";
        }
        print "<a href='./haruca_show.php?action=show_statuscheck&date={$date}'>{$date}</a> ($dayname)<BR>\n";
      }
  
      print "    </ul>\n";
      print "  </div>\n";
  
    }
  
  }  
  
}






########################################################
# show traps
########################################################
function show_traps(){

  if(empty($_REQUEST['date'])){
    $sql = "select date(now()) as today";
    #$result = mysql_query($sql) or die (mysql_error());
    #$row = mysql_fetch_assoc($result);
    #$rows = db_fetch_assoc($sql);
    $rows = db_fetch_row($sql);
    $date = $rows['today'];
  }else{
    $date = $_REQUEST['date'];
  }

  # 更新間隔の取得
  $sql = "select value from plugin_haruca_settings where item = 'reload_period'";
  #$result = mysql_query($sql) or die (mysql_error());
  #$rows = mysql_fetch_assoc($result);
  #$rows = db_fetch_assoc($sql);
  $rows = db_fetch_row($sql);

?>
								<center>
								<H3>ShowTraps</H3>
								<HR>
								</center>
								<table width="100%" >
									<tr>
										<td width="50%" align=center > <?php print trap_calender($date); ?> </td>
										<td width="50%" align=center > <?php print trap_ping($date); ?> </td>
									</tr>
									<tr>
										<td colspan="2" width="100%" align=center> <?php print trap_main($date); ?> </td>
									</tr>
								</table>
<?php
print "<center>\n";
print "<form method=post action=./haruca_show.php?action=show_traps&date=".$date.">\n";
print "auto update every ";

$selected = "";
$refresh_periods[] = 0;
$refresh_periods[] = 5;
$refresh_periods[] = 10;
$refresh_periods[] = 30;
$refresh_periods[] = 60;
$refresh_periods[] = 180;
$refresh_periods[] = 300;

print "<select name=reload_period_new >\n";

foreach($refresh_periods as $value){
  if($value === intval($rows['value'])){
    $selected = "selected";
  }else{
    $selected = "";
  }
  print "  <option value='".$value."'   $selected >".$value."</option>\n";
}

print "</select>\n";
print " seconds. -> ";

print "<button class=\"submit_button\" value=\"change\"  >change</button>\n";
print "</form>\n";

print "<BR>\n";
print "refresh remain ";
print "<span id=\"count\">".($rows['value'])."</span>\n";
print " second.\n";
print "<script type=\"text/javascript\"><!--\n";
print "var count=document.getElementById(\"count\")\n";
print "setInterval(function(){if(!count.innerHTML.match(/^0\$/)){count.innerHTML--;}},1000)\n";
print "setTimeout(function(){location.refresh(true);},".($rows['value']*1000).")\n";
print "//--></script>\n";

print "</center>\n";
}



############################3
# show logs
############################3
function show_logs(){
  print_page("ShowLogs","logs_header","logs_main");
}

############################3
# show hosts
############################3
function show_hosts(){
  print_page("ShowHosts","hosts_main");
}





########################################
# SHOW TRAP FUNCTION
########################################
function trap_calender($date_selected){
  list($cal_y,$cal_m) = explode("-",$date_selected);

  $sql = "select date_sub(\"$date_selected\", interval 1 month ) as last , date_add(\"$date_selected\", interval 1 month ) as next";
  #$result = mysql_query($sql);
  #$row = mysql_fetch_assoc($result);
  #$rows = db_fetch_assoc($sql);
  $rows = db_fetch_row($sql);
  $next = $rows['next'];
  $last = $rows['last'];
  
  
  $dn['0'] = "Monday";
  $dn['1'] = "Tuesday";
  $dn['2'] = "Wednesday";
  $dn['3'] = "Thursday";
  $dn['4'] = "Friday";
  $dn['5'] = "Saturday";
  $dn['6'] = "Sunday";
  
  $nd['Monday']    = 0;
  $nd['Tuesday']   = 1;
  $nd['Wednesday'] = 2;
  $nd['Thursday']  = 3;
  $nd['Friday']    = 4;
  $nd['Saturday']  = 5;
  $nd['Sunday']    = 6;
  
  $sql = " select date(now()) as today , dayname(\"${cal_y}-${cal_m}-01\") as dayname , day(date_sub(date_add(\"${cal_y}-${cal_m}-01\",interval 1 month),interval 1 day)) as end ";
  
  #$result = mysql_query($sql);
  #$row = mysql_fetch_assoc($result);
  #$rows = db_fetch_assoc($sql);
  $rows = db_fetch_row($sql);
  $today = $rows['today'];
  $end   = $rows['end'];
  $dayname = $rows['dayname'];

  $buf = "";
  $buf .= "<BR>\n";
  $buf .= "{$cal_y}/{$cal_m}<BR>\n";
  $buf .= "<BR>\n";
  $buf .= "<-\n";
  $buf .= "<a href=haruca_show.php?action=show_traps&date={$last}>Previous</a>\n";
  $buf .= "<-\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "<a href=haruca_show.php?action=show_traps>ThisMonth</a>\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "->\n";
  $buf .= "<a href=haruca_show.php?action=show_traps&date={$next}>Next</a>\n";
  $buf .= "->\n";
  $buf .= "<BR>\n";
  $buf .= "<BR>\n";
  
  $buf .= "<table border=1 cellspacing=0 cellpadding=2>\n";
  $buf .= "  <tr>\n";
  $buf .= "    <th width=20 align=center>Mon</th>\n";
  $buf .= "    <th width=20 align=center>Tue</th>\n";
  $buf .= "    <th width=20 align=center>Wed</th>\n";
  $buf .= "    <th width=20 align=center>Thu</th>\n";
  $buf .= "    <th width=20 align=center>Fri</th>\n";
  $buf .= "    <th width=20 align=center><font color=blue>Sat</font></th>\n";
  $buf .= "    <th width=20 align=center><font color=red>Sun</font></th>\n";
  $buf .= "  </tr>\n";
  
  if($nd{$dayname}!=0){
    $buf .= "  <tr>\n";
    $buf .= str_repeat("<td>&nbsp;</td>\n",$nd{$dayname});
  }
  
  $num = $nd{$dayname};
  for($start=1 ; $start <= $end ; $start++){
    $date  = sprintf("%4d-%02d-%02d",$cal_y,$cal_m,$start);
    $check = sprintf("%4d-%02d-%02d",$cal_y,$cal_m,$start);


    if($today === $check){
      $bg = "bgcolor=\"#FFFF00\"";
    }elseif($date_selected === $check){
      $bg = "bgcolor=\"#00FFFF\"";
    }else{
      $bg = "";
    }

  
    if(($num === 0)||($num === 7)){
      $num = 0;
      $buf .= "  <tr>\n";
      $buf .= "    <td align=center $bg>                 <a href=?action=show_traps&date=$date >$start</a></td>\n";
    }else if($num === 5){
      $buf .= "    <td align=center $bg><font color=blue><a href=?action=show_traps&date=$date >$start</a></font></td>\n";
    }else if($num === 6){
      $buf .= "    <td align=center $bg><font color=red> <a href=?action=show_traps&date=$date >$start</a></font></td>\n";
      $buf .= "  </tr>\n";
    }else{
      $buf .= "    <td align=center $bg>                 <a href=?action=show_traps&date=$date >$start</a></td>\n";
    }
    $num++;
  }
  
  $buf .= str_repeat("<td>&nbsp;</td>\n",7-$num);
  
  $buf .= "  </tr>\n";
  $buf .= "</table>\n<BR>\n";
  
  $buf .= "<table>\n";
  $buf .= "  <tr>\n";
  $buf .= "    <td bgcolor=#FFFF00 width=20> </td>\n";
  $buf .= "    <td >today</td>\n";
  $buf .= "  </tr>\n";
  $buf .= "  <tr>\n";
  $buf .= "    <td bgcolor=#00FFFF width=20> </td>\n";
  $buf .= "    <td >selected date</td>\n";
  $buf .= "  </tr>\n";
  $buf .= "</table>\n";

  return $buf;

}

function trap_ping($date){
  $sql  = "select host.description as hostname,oidstring,gettime,plugin_haruca_office.officename as officename";
  $sql .= " from plugin_haruca_traplog inner join host on host.id = plugin_haruca_traplog.hostcode ";
  $sql .= " inner join plugin_haruca_host on host.id = plugin_haruca_host.id ";
  $sql .= " inner join plugin_haruca_office on plugin_haruca_host.officecode = plugin_haruca_office.officecode ";
  $sql .= " where (oidstring = 'pingfail' or oidstring = 'pingsuccess') and host.disabled != 'on' ";
  $sql .= " order by gettime asc , hostname asc , oidstring asc";

  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $fail_host{$row['hostname']} = $row['oidstring'];
    $fail_time{$row['hostname']} = $row['gettime'];
    $fail_office{$row['hostname']} = $row['officename'];
  }
  
  $list = "";
  if(isset($fail_host)){
    arsort($fail_time);
    foreach($fail_time as $key => $value){
      if($fail_host{$key} === "pingfail"){
        $list .="<tr>\n";
        $list .="<td>".$fail_time{$key}."</td>\n";
        $list .="<td>".$key."</td>\n";
        $list .="<td>".$fail_office{$key}."</td>\n";
        $list .="</tr>\n";
      }
    }
  }
  
  $buf = "";
  $buf .= "Ping Fail List<BR>\n";
  $buf .= "<BR>\n";
  
  if(empty($list)){
    $buf .= "No Ping Fail Host.<BR>\n";
  }else{
    $buf .= "<table border=1 cellspacing=0 cellpadding=2>\n";
    $buf .= "  <tr>\n";
    $buf .= "    <th>last modified</th>\n";
    $buf .= "    <th>hostname</th>\n";
    $buf .= "    <th>officename</th>\n";
    $buf .= "  </tr>\n";
    $buf .=  $list;
    $buf .=  "</table>\n";
  }
  
  return $buf;
}

function trap_main($date){
  list($cal_y,$cal_m) = explode("-",$date);

  $list = "";

  $sql = "select date(date_sub(\"$date\", interval 1 day)) as prev , date(date_add(\"$date\", interval 1 day) ) as next";
  #$result = mysql_query($sql);
  #$row = mysql_fetch_assoc($result);
  #$rows = db_fetch_assoc($sql);
  $rows = db_fetch_row($sql);
  $prev = $rows['prev'];
  $next = $rows['next'];
  
  $sql  = "select host.description as hostname , host.hostname as host_adrs ,plugin_haruca_traptype.trapname as trapname,gettime,";
  $sql .= " plugin_haruca_traplog.target as target,plugin_haruca_traplog.summary as summary ,plugin_haruca_traplog.description as trapdescription ,";
  $sql .= " plugin_haruca_traplog.address as address ,plugin_haruca_office.officename as officename from plugin_haruca_traplog";
  $sql .= " inner join host on host.id = plugin_haruca_traplog.hostcode ";
  $sql .= " inner join plugin_haruca_traptype on plugin_haruca_traptype.oidstring = plugin_haruca_traplog.oidstring ";
  $sql .= " inner join plugin_haruca_host     on host.id= plugin_haruca_host.id ";
  $sql .= " inner join plugin_haruca_office   on plugin_haruca_host.officecode = plugin_haruca_office.officecode ";
  $sql .= " where date(gettime) = '$date' ";
  $sql .= " and plugin_haruca_traptype.available = 1 ";
  $sql .= " order by gettime desc";
  
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
  
    $time = explode(" ",$row['gettime']);
    $time = $time[1];
  
    $color = "";
    if(($row['trapname'] === "PingSuccess")||($row['trapname'] === "LinkUp"  )){ $color = "bgcolor=#ccffff"; }
    if(($row['trapname'] === "PingFail"   )||($row['trapname'] === "LinkDown")||($row['trapname'] === "ColdStart")||($row['trapname'] === "WarmStart")){ $color = "bgcolor=#ffccff"; }
  
    if(empty($row['address'])){
      $row['address']     = "-";
    }
  
    if(empty($row['trapdescription'])){
      $row['trapdescription'] = "-";
    }
  
    if(empty($row['target'])){
      $row['target'] = "-";
    }
  
    if(empty($row['address'])){
      $row['address'] = "-";
    }
  
    if(empty($row['summary'])){
      $row['summary'] = "-";
    }
  
    $list .= "<tr>\n";
    $list .= "<td>$date</td>\n";
    $list .= "<td>$time</td>\n";
    $list .= "<td>{$row['officename']}</td>\n";
    $list .= "<td><a href=telnet:{$row['host_adrs']}>{$row['hostname']}</a></td>\n";
    $list .= "<td>{$row['host_adrs']}</td>\n";
    $list .= "<td>{$row['target']}</td>\n";
    $list .= "<td>{$row['trapdescription']}</td>\n";
    $list .= "<td>{$row['address']}</td>\n";
    $list .= "<td $color>{$row['trapname']}</td>\n";
    $list .= "<td $color width=200>{$row['summary']}</td>\n";
    $list .= "</tr>\n";
  }

  $buf  = "<hr>\n";
  $buf .= "<BR>\n";
  $buf .= $date."<BR>\n";
  $buf .= "<BR>\n";
  $buf .= "<-\n";
  $buf .= "<a href=./haruca_show.php?action=show_traps&date=".$prev.">Previous</a>\n";
  $buf .= "<-\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "<a href=./haruca_show.php?action=show_traps>Today Traps</a>\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "->\n";
  $buf .= "<a href=./haruca_show.php?action=show_traps&date=".$next.">Next</a>\n";
  $buf .= "->\n";
  $buf .= "<BR>\n";
  $buf .= "<BR>\n";
  
  $buf .= "<table border=1 cellspacing=0 cellpadding=2>\n";
  $buf .= "  <tr>\n";
  $buf .= "    <th>date</th>\n";
  $buf .= "    <th>time</th>\n";
  $buf .= "    <th>office</th>\n";
  $buf .= "    <th>hostname</th>\n";
  $buf .= "    <th>hostaddress</th>\n";
  $buf .= "    <th>interface</th>\n";
  $buf .= "    <th>description</th>\n";
  $buf .= "    <th>address</th>\n";
  $buf .= "    <th>trap</th>\n";
  $buf .= "    <th>summary</th>\n";
  $buf .= "  </tr>\n";
  
  if(empty($list)){
    $buf .= "  <tr><td colspan=10 align=center >No any traps.</td></tr>\n";
  }else{
    $buf .= $list;
  }
  $buf .= "</table>\n";
  $buf .= "<BR>\n";

  return $buf;
}



##################################
# SHOW LOG FUNCTIONS
##################################
function logs_main(){
  global $config_haruca;

  ## no input string
  if(empty($_POST['keyword'])){
    $keyword = "please input hostname";
    return $keyword;
  }else{
    $keyword = $_POST['keyword'];
  }

  $buf = "";
  ## 対象のホストを整理
  foreach(explode("\n",`{$config_haruca['binpath']}search $keyword -hc `) as $key => $value){
    if($value != ""){
      $tmp = preg_split("/\ +/",$value);
      $hostnames{$tmp[1]} = $tmp[0];
    }
  }

  if(empty($hostnames)){
    $keyword = "no matches";
    return $keyword;
  }

  # cycleの最大値を繰り返し回数を取得
  $sql = "select count(cycle) as count ,max(cycle) as max from plugin_haruca_logtype";
  #$result = mysql_query($sql) or die (mysql_error());
  #$row = mysql_fetch_assoc($result);
  #$rows = db_fetch_assoc($sql);
  $rows = db_fetch_row($sql);
  $cycle_max = $rows['max'];
  $cycle_cnt = $rows['count'];

  # 取得するログの種別を取得
  $sql ="select logtypecode,logname,cycle,diffcheck from plugin_haruca_logtype";
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $lognames{$row['logname']} = $row['diffcheck'];
  }

  ## クイックジャンプのリンク作成
  $buf .= "<a name=top>QuickJump</a><BR>\n";
  foreach($hostnames as $value){
    $buf .= "<a href=#{$value}>{$value}</a><BR>\n";
  }
  $buf .= "<BR><BR>\n";

  ## ホストごとのログ一覧表 ###
  $buf .= "<center>\n";

  $condition = " where ";
  foreach($hostnames as $hostcode => $hostname){
    $condition .= " plugin_haruca_host.id = $hostcode or";
  }
  $condition = preg_replace("/or$/","",$condition);


  # ユーザ名とパスワードの有無を確認
  $sql  = "select ";
  $sql .= " host.description                    as hostname, ";
  $sql .= " plugin_haruca_host.id               as hostcode, ";
  $sql .= " plugin_haruca_category.vtypass      as vtypass  ";
  $sql .= " from host ";
  $sql .= " inner join plugin_haruca_host     on plugin_haruca_host.id = host.id ";
  $sql .= " inner join plugin_haruca_category on plugin_haruca_category.categorycode = plugin_haruca_host.categorycode ";
  $sql .= " order by host.description asc ";

  #print $sql."<BR>\n";

  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $vtypass[$row['hostname']] = $row['vtypass'];
  }

  # ログの存在確認（最新）
  $sql  = "select ";
  $sql .= " host.description                as hostname, ";
  $sql .= " plugin_haruca_logtype.logtypecode   as logtypecode, ";
  $sql .= " plugin_haruca_logtype.logname       as logname, ";
  $sql .= " plugin_haruca_log.gettime       as gettime  ";
  $sql .= " from plugin_haruca_log  ";
  $sql .= " inner join host                   on host.id                             = plugin_haruca_log.hostcode ";
  $sql .= " inner join plugin_haruca_host     on plugin_haruca_host.id               = host.id ";
  $sql .= " inner join plugin_haruca_logtype  on plugin_haruca_logtype.logtypecode   = plugin_haruca_log.logtypecode ";
  $sql .= " inner join plugin_haruca_category on plugin_haruca_category.categorycode = plugin_haruca_host.categorycode ";
  $sql .= $condition ;
  $sql .= " order by host.description asc , plugin_haruca_log.logtypecode asc , plugin_haruca_log.gettime desc";

  #print $sql."<BR>\n";
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $logs[$row['hostname']][$row['logname']][] = $row['gettime'];
    $logs_flg[$row['hostname']][$row['logname']] = 1;
  }


  # ログの存在確認（過去ログ）
  $sql  = "select ";
  $sql .= " host.description                as hostname, ";
  $sql .= " plugin_haruca_logtype.logtypecode   as logtypecode, ";
  $sql .= " plugin_haruca_logtype.logname       as logname, ";
  $sql .= " plugin_haruca_logold.gettime       as gettime  ";
  $sql .= " from plugin_haruca_logold  ";
  $sql .= " inner join host                   on host.id                             = plugin_haruca_logold.hostcode ";
  $sql .= " inner join plugin_haruca_host     on plugin_haruca_host.id               = host.id ";
  $sql .= " inner join plugin_haruca_logtype  on plugin_haruca_logtype.logtypecode   = plugin_haruca_logold.logtypecode ";
  $sql .= " inner join plugin_haruca_category on plugin_haruca_category.categorycode = plugin_haruca_host.categorycode ";
  $sql .= $condition ;
  $sql .= " order by host.description asc , plugin_haruca_logold.logtypecode asc , plugin_haruca_logold.gettime desc";

  #print $sql."<BR>\n";
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $logs[$row['hostname']][$row['logname']][] = $row['gettime'];
  }



  foreach($hostnames as $hostcode => $hostname){
    ## 対象ホストのヘッダ表示
    $buf .= "<a name=".$hostname.">".$hostname."</a>\n";
    $buf .= "<a href=#top>(return to top)</a><BR>\n";


    if(!empty($vtypass[$hostname])){
      ## 項目タイトル ###
      $buf .= "<table border=1 cellspacing=0 cellpadding=2>\n";
      $buf .= "  <tr>\n";
      #foreach($lognames as $logname => $diffcheck){
      foreach($logs_flg[$hostname] as $logname => $flg){
        $link_to = "'haruca_show.php?action=show_logs_execute&host=".$hostname."&type=".$logname."&date=now','print_log','scrollbars=yes,resizable=yes'";
        $buf .= "    <th >".$logname." <a href=\"javascript:void(0);\" OnClick=\"logwin=window.open(".$link_to.");\">(NOW)</a></th>\n";
      }
      $buf .= "  </tr>\n";

      for($num = 0 ; $num < $cycle_max ; $num++){
        $buf .= "  <tr>\n";


        foreach($lognames as $logname => $diffcheck){
          if(isset($logs_flg[$hostname][$logname])){

          $buf .= "    <td>";
          if(isset($logs[$hostname][$logname][$num])){
            $logdate = preg_replace("/[-:\ ]/","",$logs[$hostname][$logname][$num]);

            $link_to = "'haruca_show.php?action=show_logs_execute&host=".$hostname."&type=".$logname."&date=".$logdate."','print_log','scrollbars=yes,resizable=yes'";
            $buf .= "      <a href=\"javascript:void(0);\" onclick=\"logwin=window.open(".$link_to.");\">".$logs[$hostname][$logname][$num]."</a>";
    
            if($diffcheck){
              $link_to = "'haruca_show.php?action=show_logs_execute&host=".$hostname."&type=".$logname."&date=".$logdate."&diff=on','print_log','scrollbars=yes,resizable=yes'";
              $buf .= "      <BR><center><a href=\"javascript:void(0);\" onclick=\"logwin=window.open(".$link_to.");\">diff</a></center>";
            }else{
              $buf .= "      <BR><center>&nbsp;</center>";
            }

          }else{
            $buf .= "<center> - </center>\n";
          }
          $buf .= "    </td>\n";

          }

        }



        $buf .= "  </tr>\n";
      }
      
    $buf .= "</table>\n";
    }else{
      $buf .= "NOT SET VTY PASSWORD\n";

    }


    $buf .= "<BR><BR>\n";


  }

  $buf .= "</center>\n";

  return $buf;
}

function logs_header(){
  if(empty($_POST['keyword'])){
     $keyword = "";
  }else{
     $keyword = $_POST['keyword'];
  }
  $buf  = "";
  $buf .= "<center>\n";
  $buf .= "<form method=post name=hostname action=./haruca_show.php>\n";
  $buf .= "hostname &nbsp;<input name=keyword type=text value={$keyword}>\n";
  $buf .= "<input type=hidden name=action value=show_logs >\n";
  $buf .= "<input type=submit value='OK'>\n";
  $buf .= "<input type=reset  value='RESET'>\n";
  $buf .= "</form>\n";
  $buf .= "</center>\n";

  return $buf;

}


############################3
# SHOW HOSTS FUNCTION
############################3
function hosts_main(){
  $buf = "";

  $sql  = "select categorycode,categoryname from plugin_haruca_category order by categorycode asc ";
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $categories{$row['categorycode']} = $row['categoryname'];
  }

  $sql  = "select officecode,officename from plugin_haruca_office order by officecode asc ";
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $offices{$row['officecode']} = $row['officename'];
  }

  $buf .= "<center>\n";
  $buf .= "<form method=post action=./haruca_show.php>\n";
  $buf .= "\n";
  $buf .= "<table cellspacing=0 cellpadding=0 border=1>\n";
  $buf .= "<tr>\n";
  $buf .= " <th width=50><a href=./haruca_show.php?action=show_hosts&sort=id>ID</a></th>\n";
  $buf .= " <th><a href=./haruca_show.php?action=show_hosts&sort=hostname>hostname</a></th>\n";
  $buf .= " <th><a href=./haruca_show.php?action=show_hosts&sort=address>Address</a></th>\n";
  $buf .= " <th><a href=./haruca_show.php?action=show_hosts&sort=category>Category</a></th>\n";
  $buf .= " <th><a href=./haruca_show.php?action=show_hosts&sort=office>Office</a></th>\n";
  $buf .= " <th><a href=./haruca_show.php?action=show_hosts&sort=model>Model</a></th>\n";
  $buf .= " <th><a href=./haruca_show.php?action=show_hosts&sort=version>Version</a></th>\n";
  $buf .= " <th><a href=./haruca_show.php?action=show_hosts&sort=serial>Serial</a></th>\n";
  $buf .= "</tr>\n";

  $sql  = "select host.id as id,plugin_haruca_host.id as id_haruca,host.description as hostname ,host.hostname as address , ";
  $sql .= " plugin_haruca_category.categoryname as categoryname, ";
  $sql .= " plugin_haruca_office.officename as officename,plugin_haruca_category.categorycode as categorycode , ";
  $sql .= " plugin_haruca_office.officecode as officecode, ";
  $sql .= " plugin_haruca_host.model as model ,plugin_haruca_host.version as version,plugin_haruca_host.serial as serial ,";
  $sql .= " plugin_haruca_category.vtypass as vtypass from host ";
  $sql .= " left join plugin_haruca_host     on plugin_haruca_host.id           = host.id ";
  $sql .= " left join plugin_haruca_category on plugin_haruca_host.categorycode = plugin_haruca_category.categorycode ";
  $sql .= " left join plugin_haruca_office   on plugin_haruca_office.officecode = plugin_haruca_host.officecode ";
  $sql .= " where host.disabled != 'on' ";
  if(isset($_REQUEST['sort'])){
    if($_REQUEST['sort'] === "id"){
      $sql .= " order by host.id asc ";
    }else if($_REQUEST['sort'] === "hostname"){
      $sql .= " order by host.description asc ";
    }else if($_REQUEST['sort'] === "address"){
      $sql .= " order by INET_ATON(host.hostname) asc , host.description asc";
    }else if($_REQUEST['sort'] === "category"){
      $sql .= " order by plugin_haruca_host.categorycode asc , host.description asc";
    }else if($_REQUEST['sort'] === "office"){
      $sql .= " order by plugin_haruca_office.officecode asc , host.description asc";
    }else if($_REQUEST['sort'] === "model"){
      $sql .= " order by plugin_haruca_host.model asc , host.description asc";
    }else if($_REQUEST['sort'] === "version"){
      $sql .= " order by plugin_haruca_host.version asc , host.description asc";
    }else if($_REQUEST['sort'] === "serial"){
      $sql .= " order by plugin_haruca_host.serial asc , host.description asc";
    }
  }else{
    $sql .= " order by host.description asc ";
  }

  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {

    if(empty($row['model'])){$row['model']="-";}
    if(empty($row['version'])){$row['version']="-";}
    if(empty($row['serial'])){$row['serial']="-";}
    $buf .= "<tr>\n";
    $buf .= " <td align=center>&nbsp;{$row['id']}&nbsp;</td>\n";
    $buf .= " <td align=left>&nbsp;{$row['hostname']}&nbsp;</td>\n";
    $buf .= " <td align=left>&nbsp;{$row['address']}&nbsp;</td>\n";
    $buf .= " <td align=left>&nbsp;{$categories{$row['categorycode']}}&nbsp;</td>\n";
    $buf .= " <td align=left>&nbsp;{$offices{$row['officecode']}}&nbsp;</td>\n";
    $buf .= " <td align=left>&nbsp;{$row['model']}&nbsp;</td>\n";
    $buf .= " <td align=left>&nbsp;{$row['version']}&nbsp;</td>\n";
    $buf .= " <td align=left>&nbsp;{$row['serial']}&nbsp;</td>\n";
    $buf .= "</tr>\n";
  
  }

  $buf .= "</table>\n";
  $buf .= "<BR>\n";
  $buf .= "</center>\n";
  
  return $buf;

}


########################################################
# user functions
########################################################
function show_logs_execute(){
  global $config_haruca;


  if(isset($_REQUEST['date']) && isset($_REQUEST['host']) && isset($_REQUEST['type'])){
    if(isset($_REQUEST['diff'])){

      $cmd = "{$config_haruca['binpath']}show ".$_REQUEST['type']." ".$_REQUEST['host']." 2000 | grep ^[0-9] | sed \"s/[-:\ ]//g\"";
      foreach(explode("\n",chop(`$cmd`)) as $date){
        if(isset($target_date)){
          $prev_date =  $date;
          break;
        }
        if($date === $_REQUEST['date']){
          $target_date = $date;
        }
      }

      if(isset($prev_date)){
        $sql = "select ignore_str from plugin_haruca_logtype where logname=\"".$_REQUEST['type']."\"";

        #$result = mysql_query($sql) or die (mysql_error());
        #$row = mysql_fetch_assoc($result);
        #$rows = db_fetch_assoc($sql);
        $rows = db_fetch_row($sql);
        $ignore_str = $rows['ignore_str'];

        if($ignore_str){
          $ignore_str = "|egrep -v '(<<>>|$ignore_str)' ";
        }else{
          $ignore_str = "|egrep -v '(<<>>)' ";
        }

        $file_target = "{$config_haruca['tmppath']}target_".$_REQUEST['type'];
        $file_prev   = "{$config_haruca['tmppath']}prev_".$_REQUEST['type'];
        system("{$config_haruca['binpath']}show ".$_REQUEST['type']." ".$_REQUEST['host']." $target_date   $ignore_str  > $file_target");
        system("{$config_haruca['binpath']}show ".$_REQUEST['type']." ".$_REQUEST['host']." $prev_date   $ignore_str  > $file_prev");
        $result  = `diff $file_target $file_prev`;
        $result .= "\n\n(Exclude : {$rows['ignore_str']})\n";
        system("rm -f  $file_target $file_prev");
      }else{
        $result = "No exist diff target.\n";
      }

    }else{
      if($_REQUEST['date'] === "now"){
        $sql = "select loggetcmd from plugin_haruca_logtype where logname=\"".$_REQUEST['type']."\"";
        #$result = mysql_query($sql) or die (mysql_error());
        #$row = mysql_fetch_assoc($result);
        #$rows = db_fetch_assoc($sql);
        $rows = db_fetch_row($sql);
        $cmd = $rows['loggetcmd'];
        $result = `{$config_haruca['binpath']}router {$_REQUEST['host']} $cmd `;
      }else{
        $cmd = "{$config_haruca['binpath']}show ".$_REQUEST['type']." ".$_REQUEST['host']." ".$_REQUEST['date'];
        $result = `$cmd`;
      }
    }

  }else{
    $result = "Can't find log.<BR>\n";
  }
   
  $buf  = "";
  $buf .= "<html>\n";
  $buf .= "<head>\n";
  $buf .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
  $buf .= "<title></title>\n";
  $buf .= "</head>\n";
  $buf .= "<body>\n";
  $buf .= "<PRE>\n";
  $buf .= "$result\n";
  $buf .= "</PRE>\n";
  $buf .= "</body>\n";
  $buf .= "</html>\n";

  print $buf;
}


?>

