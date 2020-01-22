<?php

include_once("./config.php");
include_once("./haruca_functions.php");
chdir('../../');
include_once("./include/auth.php");
include_once("./include/config.php");

# ファイルダウンロードする
if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "loggetter_execute")){
  tool_loggetter_execute();
  exit;
}

# ファイルダウンロードする
if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "export")){
  manage_export_execute();
  exit;
}

# 別窓でコンフィグを表示させる
if(isset($_REQUEST['funcs']) && ($_REQUEST['funcs'] === "show_logs_execute")){
  show_logs_execute();
  exit;
}

# 読み込まれるたびにharuca用のDBを最新化しとかないとまずいのです
exec("{$perlpath} ./plugins/haruca/bin/sys_regist_host.pl ");

if( isset($_REQUEST['funcs']) && (($_REQUEST['funcs'] !== "tool_configchanger_execute"))){
  foreach($_REQUEST as $key => $value){
    $_REQUEST[$key] = preg_replace('/;.*/',"",$_REQUEST[$key]);
    $_REQUEST[$key] = preg_replace('/<.*/',"",$_REQUEST[$key]);
    $_REQUEST[$key] = preg_replace('/>.*/',"",$_REQUEST[$key]);
  }
}

$date = db_fetch_cell("select now() as today");

set_default_action('show');
general_header();

$_SESSION['sess_nav_level_cache'] = '';

?>
<!-- HARUCA -->
      <table class="cactiTable">
        <tr>
          <td width="150pt" class="textAreaNotes top left">

            <div id='menu'>
              <ul id='nav' role='menu'>
                <li class='menuitem' role='menuitem' aria-haspopup='true' id='menu_show'>
                  <a class='menu_parent active' href='#'><i class="menu_glyph fa fa-home"></i><span>Show</span></a>
                  <ul role='menu' id='menu_show_div' style='display:block;'>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=show_statuscheck'>StatusCheck</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=show_statuscheckold'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+---(old)</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=show_traps'>Traps</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=show_logs'>Logs</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=show_hosts'>Hosts</a></li>
                  </ul>
                </li>
                <li class='menuitem' role='menuitem' aria-haspopup='true' id='menu_tool'>
                  <a class='menu_parent active' href='#'><i class="menu_glyph fa fa-sliders"></i><span>Tool</span></a>
                  <ul role='menu' id='menu_tool_div' style='display:block;'>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=tool_shell'>EasyShell</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=tool_configchanger'>ConfigChanger</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=tool_loggetter'>LogGetter</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=tool_bwcalc'>BandwidthCalc</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=tool_wcmcalc'>WildcardMaskCalc</a></li>
                  </ul>
                </li>
                <li class='menuitem' role='menuitem' aria-haspopup='true' id='menu_manage'>
                  <a class='menu_parent active' href='#'><i class="menu_glyph fa fa-cogs"></i><span>Manage</span></a>
                  <ul role='menu' id='menu_manage_div' style='display:block;'>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=manage_host'>Host</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=manage_category'>Category</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=manage_office'>Office</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=manage_logtype'>LogType</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=manage_traptype'>TrapType</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=manage_config'>OtherConfigurations</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=manage_export'>Export/Import</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=reset_config'>ResetConfigurations</a></li>
                  </ul>
                </li>
                <li class='menuitem' role='menuitem' aria-haspopup='true' id='menu_manual'>
                  <a class='menu_parent active' href='#'><i class="menu_glyph fa fa-archive"></i><span>Manual</span></a>
                  <ul role='menu' id='menu_manual_div' style='display:block;'>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=manual_setup'>Setup</a></li>
                    <li><a role='menuitem' class='pic' href='./haruca.php?funcs=manual_command'>CLI Scripts</a></li>
                  </ul>
                </li>
              </ul>
            </div>

          </td>

          <td class="textAreaNotes top left">

<?php

if(empty($_REQUEST['funcs'])){
  page_default();
}else{

  switch($_REQUEST['funcs']){
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
    case "tool_configchanger":
      tool_configchanger();
      break;
    case "tool_configchanger_execute":
      tool_configchanger_execute();
      break;
    case "tool_shell":
      tool_shell();
      break;
    case "tool_loggetter":
      tool_loggetter();
      break;
    case "tool_bwcalc":
      tool_bwcalc();
      break;
    case "tool_wcmcalc":
      tool_wcmcalc();
      break;
    case "manage_host":
      manage_host();
      break;
    case "manage_category":
      manage_category();
      break;
    case "manage_office":
      manage_office();
      break;
    case "manage_logtype":
      manage_logtype();
      break;
    case "manage_traptype":
      manage_traptype();
      break;
    case "reset_config":
      reset_config();
      break;
    case "manual_setup":
      manual_setup();
      break;
    case "manual_command":
      manual_command();
      break;
    case "manage_config":
      manage_config();
      break;
    case "manage_export":
      manage_export();
      break;
    default:
      page_default();
      break;
  }
}  

?>
          </td>
        </tr>
      </table>

    <hr>
Half Ability Router Utility and Config Archiver <BR>
(<?php print $date; ?>) <BR>
</div> </div> </div> </div>

<?php

bottom_footer();


############################3
# default page
############################3
function page_default(){
  global $binpath;
  if(`{$binpath}pmcheck.pl` != "OK"){
    print "<H1>SETUP FAILED</H1><BR>\n";
    print "##### SET symbolic link haruca.pm TO perl @INC DIRECTORY #####<BR>\n";
    print "[user@cacti haruca]$ perl -E 'say for @INC'<BR>\n";
    print "/usr/local/lib64/perl5<BR>\n";
    print "/usr/local/share/perl5<BR>\n";
    print "/usr/lib64/perl5/vendor_perl<BR>\n";
    print "/usr/share/perl5/vendor_perl<BR>\n";
    print "/usr/lib64/perl5<BR>\n";
    print "/usr/share/perl5<BR>\n";
    print "<BR>\n";
    print "[user@cacti haruca]$ sudo ln -s /usr/share/cacti/plugins/haruca/bin/haruca.pm /usr/lib64/perl5/<BR>\n";
    print "<BR>\n";
    print "##### INSTALL require modules #####<BR>\n";
    print "[user@cacti]$ sudo yum install perl-CPAN perl-YAML perl-DBI perl-DBD-MySQL perl-Net-Telnet perl-Net-SSH perl-Expect <BR>\n";
    print "[user@cacti]$ sudo perl -MCPAN -e 'install Test::More' <BR>\n";
    print "[user@cacti]$ sudo perl -MCPAN -e 'install Net::SSH::Expect' <BR>\n";
  }else{
    print "<center>\n";
    print "  <H3>haruca</H3>\n";
    print "  Please select item from list.\n";
    print "</center>\n";
  }

  # データベースに不整合があった場合に新規追加／削除する処理を追加したい

}


########################################################
# statuscheck page
########################################################
function show_statuscheck(){
  global $binpath;
  global $perlpath;
  global $datpath;

  if(empty($_REQUEST['date'])){
    print "<center>\n";
    print "	<H3>StatusCheck</H3>\n";
    print "	<HR>\n";
    print "</center>\n";
    print `{$perlpath} {$binpath}sys_statuscheck.pl `;
  }else{
    #$str_buf = file_get_contents("{$datoldpath}{$_REQUEST['date']}");
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
    $zd = gzopen("{$datoldpath}{$_REQUEST['date']}.gz", "r");
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
  global $datpath;

  #ファイルリスト取得
  $files = array();
  $dir = opendir("{$datoldpath}");
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
        print "<a href='./haruca.php?funcs=show_statuscheck&date={$date}'>{$date}</a> ($dayname)<BR>\n";
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
print "<form method=post action=./haruca.php?funcs=show_traps&date=".$date.">\n";
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

########################################################
# ConfigChanger
########################################################
function tool_configchanger(){
  print_page("ConfigChanger","configchanger_header","configchanger_main");
}

############################3
# EasyShell
############################3
function tool_shell(){
  print_page("EasyShell","shell_main");
}

############################3
# LogGetter
############################3
function tool_loggetter(){
  print_page("LogGetter","loggetter_header","loggetter_main");
}

############################3
# bandwidth calc
############################3
function tool_bwcalc(){
  print_page("BandwidthCalculator","bwcalc_main");
}

############################3
# wildcardmask calc
############################3
function tool_wcmcalc(){
  print_page("WildcardMaskCalculator","wcmcalc_main");
}

############################3
# management hosts
############################3
function manage_host(){
  global $perlpath;
  foreach($_REQUEST as $key => $value){
    if($value === "get"){
      `{$perlpath}  ./plugins/haruca/bin/sys_get_cisco_info {$key}`;
    }
  }


  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "update")){
    #print "UPDATE<BR>\n";

    #$sql  = "select id,categorycode ,officecode from plugin_haruca_host";
    #while($row = db_fetch_assoc($sql)){

    #$ids = db_fetch_assoc($sql);
    #if(!empty($ids)){
    #  foreach($ids as $id) {

    $sql  = "select id,categorycode ,officecode from plugin_haruca_host";
    #$result = mysql_query($sql) or die (mysql_error());
    $rows = db_fetch_assoc($sql);
    foreach($rows as $row) {
      $codes[$row['id']]['categorycode'] = $row['categorycode'];
      $codes[$row['id']]['officecode'] = $row['officecode'];
    }

    foreach($_REQUEST as $key => $value){
      if(preg_match("/:/",$key)){
        $codes_new = explode(":",$key);
        $str_code = $codes_new[0];
        $id = $codes_new[1];
        if($codes[$id][$str_code] != $value){
          if(isset($args[$codes_new[1]])){
            $args[$codes_new[1]] .= " --".$codes_new[0]."=".$value;
          }else{
            $args[$codes_new[1]] = " --".$codes_new[0]."=".$value;
          }
        }
      }
    }

    if(isset($args)){
      foreach($args as $key => $value){
        #print "{$perlpath} ./plugins/haruca/bin/sys_regist_host.pl --id=".$key.$value."<BR>\n";
        exec("{$perlpath} ./plugins/haruca/bin/sys_regist_host.pl --id=".$key.$value);
      }
    }
  }elseif(isset($_REQUEST['type']) && ($_REQUEST['type'] === "AllGet")){
      `{$perlpath}  ./plugins/haruca/bin/sys_get_cisco_info --all`;
  }

  print_page("ManagementHost","manage_host_main");
}

############################3
# management categories
############################3
function manage_category(){


  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "update")){

    $sql  = "select categorycode ,categoryname ,login_method ,prepare_cmd ,uid_prompt,";
    $sql .= " username ,pwd_prompt ,vtypass ,enable_cmd ,en_prompt ,enable ";
    $sql .= " from plugin_haruca_category order by categorycode asc";

    #$result = mysql_query($sql) or die (mysql_error());
    #while($row = mysql_fetch_assoc($result)){
    $rows = db_fetch_assoc($sql);
    foreach($rows as $row) {

      #print "UPDATE EXECUTE<BR>\n";
      if($row['categorycode'] != 1){

        $sql  = "update plugin_haruca_category set ";
        $sql .= " categoryname = ".local_quote($_REQUEST['categoryname:'.$row['categorycode']] )." , ";
        $sql .= " login_method = ".local_quote($_REQUEST['login_method:'.$row['categorycode']] )." , ";
        $sql .= " prepare_cmd  = ".local_quote($_REQUEST['prepare_cmd:' .$row['categorycode']] )." , ";
        $sql .= " uid_prompt   = ".local_quote($_REQUEST['uid_prompt:'  .$row['categorycode']] )." , ";
        $sql .= " username     = ".local_quote($_REQUEST['username:'    .$row['categorycode']] )." , ";
        $sql .= " pwd_prompt   = ".local_quote($_REQUEST['pwd_prompt:'  .$row['categorycode']] )." , ";
        $sql .= " vtypass      = ".local_quote($_REQUEST['vtypass:'     .$row['categorycode']] )." , ";
        $sql .= " enable_cmd   = ".local_quote($_REQUEST['enable_cmd:'  .$row['categorycode']] )." , ";
        $sql .= " en_prompt    = ".local_quote($_REQUEST['en_prompt:'   .$row['categorycode']] )." , ";
        $sql .= " enable       = ".local_quote($_REQUEST['enable:'      .$row['categorycode']] )      ;
        $sql .= " where categorycode = ".$row['categorycode'];

        #print $sql."<BR>\n";
        #mysql_query($sql) or die (mysql_error());
        db_execute($sql);

      }
    }

  }else if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "remove")){

    #print "REMOVE\n";
    if($_REQUEST['removecode'] != 1){
      # remove category 
      $sql = "delete from plugin_haruca_category where categorycode = ".$_REQUEST['removecode'];
      #mysql_query($sql) or die (mysql_error());
      db_execute($sql);

      # remove category log
      $sql  = "delete from  plugin_haruca_cat_get_log where categorycode = ".$_REQUEST['removecode'];
      #mysql_query($sql) or die (mysql_error());
      db_execute($sql);
    }

  }else if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "insert")){
    $sql  = "insert into plugin_haruca_category set ";
    $sql .= " categoryname = ".local_quote($_REQUEST['categoryname:new'] )." , ";
    $sql .= " login_method = ".local_quote($_REQUEST['login_method:new'] )." , ";

    $sql .= " prepare_cmd  = ".local_quote($_REQUEST['prepare_cmd:new']  )." , ";
    $sql .= " uid_prompt   = ".local_quote($_REQUEST['uid_prompt:new']   )." , ";
    $sql .= " username     = ".local_quote($_REQUEST['username:new']     )." , ";
    $sql .= " pwd_prompt   = ".local_quote($_REQUEST['pwd_prompt:new']   )." , ";
    $sql .= " vtypass      = ".local_quote($_REQUEST['vtypass:new']      )." , ";
    $sql .= " enable_cmd   = ".local_quote($_REQUEST['enable_cmd:new']   )." , ";
    $sql .= " en_prompt    = ".local_quote($_REQUEST['en_prompt:new']    )." , ";
    $sql .= " enable       = ".local_quote($_REQUEST['enable:new']       )      ;

    #print "INSERT<BR>\n";
    #print $sql."<BR>\n";
    #mysql_query($sql) or die (mysql_error());
    db_execute($sql);

    #$sql = "select categorycode from plugin_haruca_category order by categorycode desc limit 1";
    #$result = mysql_query($sql) or die (mysql_error());
    #$row = mysql_fetch_assoc($result);

  }

  print_page("ManagementCategory","manage_category_main");
}

############################3
# management office
############################3
function manage_office(){
  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "update")){

    $sql  = "select officecode,officename,officeaddress from plugin_haruca_office order by officecode asc";
    #$result = mysql_query($sql) or die (mysql_error());
    #while($row = mysql_fetch_assoc($result)){
    $rows = db_fetch_assoc($sql);
    foreach($rows as $row) {

      #print "UPDATE EXECUTE<BR>\n";
      if($row['officecode'] != 1){
        $sql  = "update plugin_haruca_office set ";
        $sql .= " officename     = ".local_quote($_REQUEST['officename:'   .$row['officecode']])." , ";
        $sql .= " officeaddress  = ".local_quote($_REQUEST['officeaddress:'.$row['officecode']])."   ";
        $sql .= " where officecode = ".$row['officecode'];

        #print $sql."<BR>\n";
        #mysql_query($sql) or die (mysql_error());
        db_execute($sql);
      }

    }

  }else if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "remove")){
    $sql = "delete from plugin_haruca_office where officecode = ".$_REQUEST['removecode'];
    #print "REMOVE\n";
    #print $sql."<BR>\n";
    #mysql_query($sql) or die (mysql_error());
    db_execute($sql);
  }else if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "insert")){

    $sql  = "insert into plugin_haruca_office set ";
    $sql .= " officename     = ".local_quote($_REQUEST['officename:new'   ])." , ";
    $sql .= " officeaddress  = ".local_quote($_REQUEST['officeaddress:new'])."   ";

    #print "NEW RECORD EXIST : <BR>\n";
    #print $sql."<BR>\n";
    #mysql_query($sql) or die (mysql_error());
    db_execute($sql);

  }

  print_page("ManagementOffice","manage_office_main");
}

############################3
# MANAGEMENT OFFICE FUNCTION
############################3
function manage_office_main(){

  $buf = "";

  $buf .= "<form method=post action=./haruca.php>\n";

  $buf .= "<center>\n";
  $buf .= "<table cellspacing=0 cellpadding=0 border=1>\n";
  $buf .= "<tr>\n";
  $buf .= " <th width=100>ID</th>\n";
  $buf .= " <th>Name</th>\n";
  $buf .= " <th>Address</th>\n";
  $buf .= "</tr>\n";

  $sql  = "select officecode,officename,officeaddress from plugin_haruca_office order by officecode asc";
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {

    if($row['officecode'] == 1){
      $disabled = "disabled";
    }else{
      $disabled = "";
      $officecodes[$row['officecode']] = $row['officename'];
    }

    $buf .= "<tr>\n";
    $buf .= " <td align=center width=20>{$row['officecode']}</td>\n";
    $buf .= " <td><input type=text size=20 name=officename:{$row['officecode']}    value=\"{$row['officename']}\"    {$disabled} ></td>\n";
    $buf .= " <td><input type=text size=50 name=officeaddress:{$row['officecode']} value=\"{$row['officeaddress']}\" {$disabled} ></td>\n";
    $buf .= "</tr>\n";
  }


  $buf .= "<tr>\n";
  $buf .= " <td align=center width=20>New Record</td>\n";
  $buf .= " <td><input type=text size=20 name=officename:new value=\"\"></td>\n";
  $buf .= " <td><input type=text size=50 name=officeaddress:new value=\"\"></td>\n";
  $buf .= "</tr>\n";
  $buf .= "</table>\n";
  $buf .= "<BR>\n";
  $buf .= "ID<select name='removecode'>\n";
  $buf .= "  <option value=0>select remove code\n";

  if(isset($officecodes)){
    foreach($officecodes as $key => $value){
      $buf .= "  <option value=$key>$value\n";
    }
  }
  
  $buf .= "</select>\n";

  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"remove\" OnClick=\"return confirm('Remove confirm?')\"; >remove</button>\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"update\" OnClick=\"return confirm('Update confirm?')\"; >update</button>\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"insert\" OnClick=\"return confirm('Insert confirm?')\"; >insert</button>\n";

  $buf .= "<input type=hidden name=funcs value=manage_office >\n";
  $buf .= "</center>\n";
  $buf .= "</form>\n";
  
  
  return $buf;

}


############################3
# management logtype
############################3
function manage_logtype(){
  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "update")){
    $sql  = "select logtypecode,logname,loggetcmd,diffcheck,cycle from plugin_haruca_logtype order by logtypecode asc";
    #$result = mysql_query($sql) or die (mysql_error());
    #while($row = mysql_fetch_assoc($result)){
    $rows = db_fetch_assoc($sql);
    foreach($rows as $row) {

      #print "UPDATE EXECUTE<BR>\n";
      #if($row['logtypecode'] != 1 && $row['logtypecode'] != 2 ){
        $sql  = "update plugin_haruca_logtype set ";
        $sql .= " logname   = ".local_quote($_REQUEST['logname:'  .$row['logtypecode']])." , ";
        $sql .= " loggetcmd = ".local_quote($_REQUEST['loggetcmd:'.$row['logtypecode']])." , ";
        $sql .= " ignore_str= ".local_quote($_REQUEST['ignore_str:'.$row['logtypecode']])." , ";
        if(isset($_REQUEST['diffcheck:'.$row['logtypecode']])){
          $sql .= " diffcheck = 1,";
        }else{
          $sql .= " diffcheck = 0,";
        }
        $sql .= " cycle     = ".            $_REQUEST['cycle:'    .$row['logtypecode']]       ;
        $sql .= " where logtypecode = ".$row['logtypecode'];

        #print $sql."<BR>\n";
        #mysql_query($sql) or die (mysql_error());
        db_execute($sql);
      #}
    }

    $sql = "delete from plugin_haruca_cat_get_log";
    #mysql_query($sql) or die (mysql_error());
    db_execute($sql);

    foreach($_REQUEST as $key => $value){
      if(preg_match("/^avail:/",$key)){
        $codes = explode(":",$key);
        $sql = "insert into plugin_haruca_cat_get_log set categorycode = ".$codes[1]." , logtypecode = ".$codes[2];
        #mysql_query($sql) or die (mysql_error());
        db_execute($sql);
        #print $sql. "<BR>\n";
      }
    }

  }else if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "remove")){
    $sql = "delete from plugin_haruca_logtype where logtypecode = ".$_REQUEST['removecode'];
    #print "REMOVE\n";
    #print $sql."<BR>\n";
    #mysql_query($sql) or die (mysql_error());
    db_execute($sql);
  }else if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "insert")){
    $sql  = "insert into plugin_haruca_logtype set ";
    $sql .= " logname   = ".local_quote($_REQUEST['logname:new']  )." , ";
    $sql .= " loggetcmd = ".local_quote($_REQUEST['loggetcmd:new'])." , ";
    if($_REQUEST['diffcheck:new']){
      $sql .= " diffcheck = 1,";
    }else{
      $sql .= " diffcheck = 0,";
    }
    $sql .= " cycle     = ".            $_REQUEST['cycle:new']           ;
    #print "INSERT\n";
    #print $sql."<BR>\n";
    #mysql_query($sql) or die (mysql_error());
    db_execute($sql);
  }

  print_page("ManagementLogType","manage_logtype_main");
}


############################3
# management traptype
############################3
function manage_traptype(){
  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "update")){

    $sql  = "select oidstring,trapname,target,summary from plugin_haruca_traptype";
    #$result = mysql_query($sql) or die (mysql_error());
    #while($row = mysql_fetch_assoc($result)){
    $rows = db_fetch_assoc($sql);
    foreach($rows as $row) {

      $sql  = "update plugin_haruca_traptype set ";
      $sql .= " trapname  = ".local_quote($_REQUEST['trapname:'.$row['trapname']])." , ";
      $sql .= " oidstring = ".local_quote($_REQUEST['oidstring:'.$row['trapname']])." , ";
      $sql .= " target    = ".local_quote($_REQUEST['target:'   .$row['trapname']])." , ";
      $sql .= " summary   = ".local_quote($_REQUEST['summary:'  .$row['trapname']])." , ";

      if(isset($_REQUEST['available:'.$row['trapname']])){
        $sql .= " available   = 1 ,";
      }else{
        $sql .= " available   = 0 ,";
      }

      if(isset($_REQUEST['alertmail:'.$row['trapname']])){
        $sql .= " alertmail   = 1 ";
      }else{
        $sql .= " alertmail   = 0 ";
      }

      $sql .= " where trapname = ".local_quote($row['trapname']);

      #print "UPDATE EXECUTE<BR>\n";
      #print $sql."<BR>\n";
      #mysql_query($sql) or die (mysql_error());
      db_execute($sql);

    }

  }else if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "remove")){
    $sql = "delete from plugin_haruca_traptype where trapname = ".local_quote($_REQUEST['removecode']);
    #print "REMOVE ".$_REQUEST['removecode']."<BR>\n";
    #print $sql."<BR>\n";
    #mysql_query($sql) or die (mysql_error());
    db_execute($sql);
  }else if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "insert")){

    $sql  = "insert into plugin_haruca_traptype set ";
    $sql .= " oidstring   = ".local_quote($_REQUEST['oidstring:new'])." , ";
    $sql .= " trapname    = ".local_quote($_REQUEST['trapname:new' ])." , ";
    $sql .= " target      = ".local_quote($_REQUEST['target:new'   ])." , ";
    $sql .= " summary     = ".local_quote($_REQUEST['summary:new'  ])."   ";

    #print "NEW RECORD EXIST : <BR>\n";
    #print $sql."<BR>\n";
    #mysql_query($sql) or die (mysql_error());
    db_execute($sql);

  }

  print_page("ManagementTrapType","manage_traptype_main");

}

########################################################
# OTHER CONFIGURETIONS SETTINGS
########################################################
function manage_config(){

  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "update")){
    foreach($_REQUEST as $key => $value){
      if(preg_match("/^:/",$key)){
        $sql = "update plugin_haruca_settings set value = ".local_quote($value) ." where item = ".local_quote(ltrim($key,":")) ;
        #mysql_query($sql) or die (mysql_error());
        db_execute($sql);
      }
    }
  }

  print_page("ManageConfigurations","manage_config_main");

  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "testmail")){
    print "<center><h3>\n";
    $result = send_alert_mail("haruca alert > test mail","Test mail from haruca.");
    print $result."<BR>\n";
    print "</h3></center>\n";
  }

}

########################################################
# MANAGE OTHER CONFIGURATION FUNCTION
########################################################
function manage_config_main(){
  $buf = "";

  $buf .= "<center>\n";
  $buf .= "<form method=post action=./haruca.php>\n";
  $buf .= "<table border=1 >\n";
  $buf .= " <tr>\n";
  $buf .= "  <th >ITEM</th>\n";
  $buf .= "  <th >VALUE</th>\n";
  $buf .= " </tr>\n";

  $sql = "select item,value from plugin_haruca_settings ";

  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $buf .= " <tr>\n";
    $buf .= "  <td>{$row['item']}</td>\n";
    $buf .= "  <td><input type=text name=\":{$row['item']}\" value=\"{$row['value']}\" size=50></td>\n";
    $buf .= " </tr>\n";
  }

  $buf .= "<input type=hidden name=funcs value=manage_config>\n";
  $buf .= "<input type=hidden name=type value=update>\n";
  $buf .= "</table>\n";
  #$buf .= "<input type=submit value=OK OnClick=\"return confirm('Update Configurations ?')\";>\n";
  $buf .= "<button class=\"submit_button\"  value=\"OK\" OnClick=\"return confirm('Update Configurations?')\"; >OK</button>\n";
  $buf .= "<input type=reset  value=RESET>\n";
  $buf .= "</form>\n";

  $buf .= "<BR><BR>\n";

  $buf .= "<form method=post action=./haruca.php>\n";
  $buf .= "<input type=hidden name=type value=testmail>\n";
  $buf .= "<input type=hidden name=funcs value=manage_config>\n";
  #$buf .= "<input type=submit value=\"Send a Test Mail\" OnClick=\"return confirm('Send a test mail ?')\";>\n";
  $buf .= "<button class=\"submit_button\"  value=\"Test\" OnClick=\"return confirm('Send a test mail?')\"; >Test</button>\n";
  $buf .= "</form>\n";

  $buf .= "</center>\n";

  return $buf;
}


########################################################
# EXPORT IMPORT SETTINGS
########################################################
function manage_export(){
  print_page("Export/ImportConfigurations","manage_export_main");
}

########################################################
# MANAGE OTHER CONFIGURATION FUNCTION
########################################################
function manage_export_main(){

  $buf = "";
  $buf .= "<center>\n";

  $buf .= "export haruca configurations to file<BR><BR>\n";
  $buf .= "<form method=post name=export action=./haruca.php>\n";

  $buf .= "<table>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=haruca checked>haruca configuretion</td>";
  $buf .= "<td rowspan=3><button class=\"submit_button\" value=\"export\" >export</button></td></tr>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=rtt>rtt</td></tr>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=traplog>trap</td></tr>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=log>latest config</td></tr>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=logold>old config</td></tr>\n";
  $buf .= "</table>\n";

  $buf .= "<input type=hidden name=funcs value=manage_export >\n";
  $buf .= "<input type=hidden name=type value=export>\n";
  $buf .= "</form>\n";
  $buf .= "<br><BR>\n";

  $buf .= "import haruca configurations from file";
  $buf .= "<form method=post name=import action=./haruca.php enctype=\"multipart/form-data\">\n";
  $buf .= "<input type=hidden name=funcs value=manage_export >\n";
  $buf .= "<input type=hidden name=type value=import>\n";
  $buf .= "<input type=file name=importfile >\n";
  $buf .= "<input type=submit value=import OnClick=\"return confirm('Execute import all haruca configurations ?')\";>\n";
  $buf .= "</form>\n";

  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "import")){
    if(manage_import_execute()){
      $buf .= "<BR><BR><H3>import successfuly.</H3><BR>\n";
    }else{
      $buf .= "<BR><BR><H3>import failed.</H3><BR>\n";
    }
  }


  $buf .= "</center>\n";
  
  return $buf;
}



########################################################
# reset configurations
########################################################
function reset_config(){

  print_page("ResetConfiguration","reset_config_main");
  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "execute")){
    include_once('./plugins/haruca/setup.php');
    haruca_setup_table_new();
    print "<BR>\n";
    print "<center><H2>RESET HARUCA CONFIGURATIONS.</H2></center>\n";
  }
}

########################################################
# RESET CONFIGURATION FUNCTION
########################################################
function reset_config_main(){
  $buf = "";

  $buf .= "<center>\n";
  $buf .= "Please push the lower button to reset configuration.<BR><BR>\n";
  $buf .= "<form method=post action=./haruca.php>\n";
  $buf .= "<input type=hidden name=funcs value=reset_config>\n";
  $buf .= "<input type=hidden name=type value=execute>\n";
  $buf .= "<input type=submit value=OK OnClick=\"return confirm('Execute Reset Config ?')\";>\n";
  $buf .= "</form>\n";
  $buf .= "</center>\n";

  return $buf;
}



########################################################
# manual setup
########################################################
function manual_setup(){
?>
            <center>
              <H3>Initialize Setup</H3>
              <HR>
            </center>
            <table width="100%">
              <tr>
                <td>
                  <PRE>
<?php 
  $buf = file_get_contents("./plugins/haruca/docs/setup");
  print $buf;
?>
                  </PRE>
                </td>
              </tr>
            </table>

<?php
}


########################################################
# manual command
########################################################
function manual_command(){
?>
            <center>
              <H3>Command Line Scripts</H3>
              <HR>
            </center>
            <table width="100%">
              <tr>
                <td>
                  <PRE>
<?php 
  $buf = file_get_contents("./plugins/haruca/docs/command");
  print $buf;
?>
                  </PRE>
                </td>
              </tr>
            </table>

<?php
}


function send_alert_mail($mail_subject,$mail_body){
  if(empty($mail_subject)){return 0;}
  if(empty($mail_body)){return 0;}

  $sql = "select value from plugin_haruca_settings where item = 'alert_email_from_name'";
  #$result = mysql_query($sql);
  #$row = mysql_fetch_assoc($result);
  #$rows = db_fetch_assoc($sql);
  $rows = db_fetch_row($sql);
  $alert_email_from_name = $rows['value'];

  $sql = "select value from plugin_haruca_settings where item = 'alert_email_from_address'";
  #$result = mysql_query($sql);
  #$row = mysql_fetch_assoc($result);
  #$rows = db_fetch_assoc($sql);
  $rows = db_fetch_row($sql);
  $alert_email_from_address = $rows['value'];

  $sql = "select value from plugin_haruca_settings where item = 'alert_email_to_address'";
  #$result = mysql_query($sql);
  #$row = mysql_fetch_assoc($result);
  #$rows = db_fetch_assoc($sql);
  $rows = db_fetch_row($sql);
  $mail_to = $rows['value'];

  $sql = "select value from plugin_haruca_settings where item = 'alert_smtp_server'";
  #$result = mysql_query($sql);
  #$row = mysql_fetch_assoc($result);
  #$rows = db_fetch_assoc($sql);
  $rows = db_fetch_row($sql);
  $server = $rows['value'];

  #メールアドレスは正確な正規表現が不可能なので設定アドレスは
  #数字と英文字のみで構成されていないと弾くことにした。
  #よくある example@example.com ならOK
  if(preg_match("/^[a-zA-Z][0-9a-zA-Z._\-]+@[0-9a-zA-Z]+(\.[0-9a-zA-Z]+)+$/",$mail_to)){
    $res = haruca_send_mail( $mail_to , $mail_subject , $mail_body , $alert_email_from_name,$alert_email_from_address,$server);
  }else{
    $res = "to address illigal\n";
  }

  return $res;

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
  $buf .= "<a href=haruca.php?funcs=show_traps&date={$last}>Previous</a>\n";
  $buf .= "<-\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "<a href=haruca.php?funcs=show_traps>ThisMonth</a>\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "->\n";
  $buf .= "<a href=haruca.php?funcs=show_traps&date={$next}>Next</a>\n";
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
      $buf .= "    <td align=center $bg>                 <a href=?funcs=show_traps&date=$date >$start</a></td>\n";
    }else if($num === 5){
      $buf .= "    <td align=center $bg><font color=blue><a href=?funcs=show_traps&date=$date >$start</a></font></td>\n";
    }else if($num === 6){
      $buf .= "    <td align=center $bg><font color=red> <a href=?funcs=show_traps&date=$date >$start</a></font></td>\n";
      $buf .= "  </tr>\n";
    }else{
      $buf .= "    <td align=center $bg>                 <a href=?funcs=show_traps&date=$date >$start</a></td>\n";
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
  $buf .= "<a href=./haruca.php?funcs=show_traps&date=".$prev.">Previous</a>\n";
  $buf .= "<-\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "<a href=./haruca.php?funcs=show_traps>Today Traps</a>\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
  $buf .= "->\n";
  $buf .= "<a href=./haruca.php?funcs=show_traps&date=".$next.">Next</a>\n";
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
  global $binpath;

  ## no input string
  if(empty($_POST['keyword'])){
    $keyword = "please input hostname";
    return $keyword;
  }else{
    $keyword = $_POST['keyword'];
  }

  $buf = "";
  ## 対象のホストを整理
  foreach(explode("\n",`{$binpath}search $keyword -hc `) as $key => $value){
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
        $link_to = "'haruca.php?funcs=show_logs_execute&host=".$hostname."&type=".$logname."&date=now','print_log','scrollbars=yes,resizable=yes'";
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

            $link_to = "'haruca.php?funcs=show_logs_execute&host=".$hostname."&type=".$logname."&date=".$logdate."','print_log','scrollbars=yes,resizable=yes'";
            $buf .= "      <a href=\"javascript:void(0);\" onclick=\"logwin=window.open(".$link_to.");\">".$logs[$hostname][$logname][$num]."</a>";
    
            if($diffcheck){
              $link_to = "'haruca.php?funcs=show_logs_execute&host=".$hostname."&type=".$logname."&date=".$logdate."&diff=on','print_log','scrollbars=yes,resizable=yes'";
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
  $buf .= "<form method=post name=hostname action=./haruca.php>\n";
  $buf .= "hostname &nbsp;<input name=keyword type=text value={$keyword}>\n";
  $buf .= "<input type=hidden name=funcs value=show_logs >\n";
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
  $buf .= "<form method=post action=./haruca.php>\n";
  $buf .= "\n";
  $buf .= "<table cellspacing=0 cellpadding=0 border=1>\n";
  $buf .= "<tr>\n";
  $buf .= " <th width=50><a href=./haruca.php?funcs=show_hosts&sort=id>ID</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=show_hosts&sort=hostname>hostname</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=show_hosts&sort=address>Address</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=show_hosts&sort=category>Category</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=show_hosts&sort=office>Office</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=show_hosts&sort=model>Model</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=show_hosts&sort=version>Version</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=show_hosts&sort=serial>Serial</a></th>\n";
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
# TOOL CONFIG CHANGER FUNCTIONS
########################################################
function configchanger_header(){
  $buf = "";

  if(empty($_POST['hostname'])){
     $hostname = "";
  }else{
     $hostname = $_POST['hostname'];
  }

  if(empty($_POST['ipaddress'])){
     $ipaddress = "";
  }else{
     $ipaddress = $_POST['ipaddress'];
  }

  ## config change search box ##
  $buf .= "<script type=\"text/javascript\">\n";
  $buf .= "<!--\n";
  $buf .= "function ResetForm(){\n";
  $buf .= "  document.hostname.hostname.value  = \"\";\n";
  $buf .= "  document.hostname.ipaddress.value = \"\";\n";
  $buf .= "  document.hostname.categorycode.selectedIndex = 0;\n";
  $buf .= "}\n";
  $buf .= "// -->\n";
  $buf .= "</script>\n";

  $buf .= "<table width=100%>\n";
  $buf .= "  <tr>\n";
  $buf .= "    <td width=50% align=center>\n";
  $buf .= "      <form method=post name=hostname action=./haruca.php>\n";
  $buf .= "        <input type=hidden name=funcs value=tool_configchanger >\n";
  $buf .= "        <input type=hidden name=type value=config >\n";
  $buf .= "        <H4>Configuration Change</H4>\n";
  $buf .= "        <table>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td>hostname</td>\n";
  $buf .= "            <td> <input name=hostname type=text value=\"{$hostname}\"> </td>\n";
  $buf .= "          </tr>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td>ip address</td>\n";
  $buf .= "            <td> <input name=ipaddress type=text value=\"{$ipaddress}\"> </td>\n";
  $buf .= "          </tr>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td>category</td>\n";
  $buf .= "            <td>\n";
  $buf .= "              <select name=categorycode >\n";
  $buf .= "              <option value='' selected>(select category)</option>\n";

  $sql = "select categorycode,categoryname from plugin_haruca_category where vtypass is not NULL";
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $selected = "";
    if(( isset($_REQUEST['type']) && ($_REQUEST['type'] === "config"))&&(isset($_REQUEST['categorycode']) && ($row['categorycode'] === $_REQUEST['categorycode']))){$selected = "selected";}
    $buf .= "                <option value={$row['categoryname']} $selected>{$row['categoryname']}</option>\n";
  }
  $buf .= "              </select>\n";
  $buf .= "            </td>\n";
  $buf .= "          </tr>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td></td>\n";
  $buf .= "            <td align=center>";
  #$buf .= "<input type=submit name=config value=OK>";
  $buf .= "<button class=\"submit_button\"  name=\"config\" value=\"OK\" >OK</button>\n";
  $buf .= "<input type=button value=RESET onClick=ResetForm();> </td>\n";
  $buf .= "          </tr>\n";
  $buf .= "        </table>\n";
  $buf .= "      </form>\n";
  $buf .= "    </td>\n";

  ## password change search box ##
  $buf .= "    <td width=50% align=center>\n";
  $buf .= "      <form method=post name=password action=./haruca.php>\n";
  $buf .= "        <input type=hidden name=funcs value=tool_configchanger >\n";
  $buf .= "        <input type=hidden name=type value=password >\n";
  $buf .= "        <H4>Password Change</H4>\n";
  $buf .= "        <table>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td>\n";
  $buf .= "              <select name=categorycode >\n";
  $buf .= "                <option value='' selected>(select category)</option>\n";

  $sql = "select categorycode,categoryname from plugin_haruca_category where vtypass is not NULL";
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $selected = "";
    if(( isset($_REQUEST['type']) && ($_REQUEST['type'] === "password"))&&(isset($_REQUEST['categorycode']) && ($row['categorycode'] === $_REQUEST['categorycode']))){$selected = "selected";}
    $buf .= "                <option value={$row['categorycode']} $selected>{$row['categoryname']}</option>\n";
  }
  $buf .= "              </select>\n";
  $buf .= "            </td>\n";
  $buf .= "          </tr>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td align=center>";
  $buf .= "<input type=submit name=password value=OK>";
  $buf .= "<button class=\"submit_button\"  name=\"password\" value=\"OK\" >OK</button>\n";
  $buf .= "</td>\n";
  $buf .= "          </tr>\n";
  $buf .= "        </table>\n";
  $buf .= "      </form>\n";
  $buf .= "    </td>\n";
  $buf .= "  </tr>\n";
  $buf .= "</table>\n";

  return $buf;

}

function configchanger_main(){
  global $binpath;
  $buf = "";

  if( isset($_REQUEST['type']) && ($_REQUEST['type'] === "config")){
    ## config change の場合


    ## 検索文字列などがない場合はエラー表示
    if(empty($_POST['hostname']) && empty($_POST['ipaddress']) && empty($_REQUEST['categorycode'])){
      $buf .= "<center>\n";
      $buf .= "Please input search strings or select category.<BR>\n";
      $buf .= "</center>\n";
      return $buf;
    }

    if(empty($_POST['hostname'])){
      $_POST['hostname'] = ".";
    }

    if(empty($_POST['ipaddress'])){
      $_POST['ipaddress'] = ".";
    }

    if(empty($_REQUEST['categorycode'])){
      $cond_category = "";
    }else{
      $cond_category = "--category={$_REQUEST['categorycode']}";
    }

    $list_hostname  = explode("\n",`${binpath}search {$_POST['hostname']} $cond_category -h | sed s/\ \ *//g`);
    $list_ipaddress = explode("\n",`${binpath}ipsearch {$_POST['ipaddress']} -h| sed s/\ \ *//g`);

    $list = array_filter(array_intersect($list_hostname,$list_ipaddress));


    ## hostlist が空の場合はエラー表示
    if(count($list) === 0){
      $buf .= "<center>\n";
      $buf .= "no match.<BR>\n";
      $buf .= "</center>\n";
      return $buf;
    }

    
    $buf .= "<center>\n";
    $buf .= "<form method=post name=config action=./haruca.php>\n";
    $buf .= "select hosts<BR>\n";

    ## hostlist のtableヘッダ表示
    $buf .= "<table border=1>\n";
    $buf .= "  <tr>\n";
    $buf .= "    <th>hostname</th>\n";
    $buf .= "    <th>address</th>\n";
    $buf .= "    <th>category</th>\n";
    $buf .= "    <th>office</th>\n";
    $buf .= "    <th>model</th>\n";
    $buf .= "    <th>version</th>\n";
    $buf .= "    <th>serial</th>\n";
    $buf .= "    <th>check</th>\n";
    $buf .= "  </tr>\n";
    
    #foreach(explode("\n",$list) as $key => $hostname){
    foreach($list as $key => $hostname){
      ## 最後の要素は中身がないので繰り返しぬける
      if(empty($hostname)){continue;}

      $hostname_js = preg_replace("/-/","_hyphen_",$hostname);

      #enableがなければ対象外
      $sql  = "select host.hostname as ipaddress ,plugin_haruca_category.categoryname as categoryname ,plugin_haruca_office.officename as officename, ";
      $sql .= " plugin_haruca_host.model as model,plugin_haruca_host.version as version ,plugin_haruca_host.serial as serial, ";
      $sql .= " plugin_haruca_category.enable as enable from host ";
      $sql .= " inner join plugin_haruca_host on host.id = plugin_haruca_host.id ";
      $sql .= " inner join plugin_haruca_category on plugin_haruca_host.categorycode=plugin_haruca_category.categorycode ";
      $sql .= " inner join plugin_haruca_office on plugin_haruca_office.officecode=plugin_haruca_host.officecode ";
      $sql .= " where description='{$hostname}' and host.disabled != 'on' ";

      #$result = mysql_query($sql) or die (mysql_error());
      #$row = mysql_fetch_assoc($result);
      #$rows = db_fetch_assoc($sql);
      $rows = db_fetch_row($sql);
      if(empty($rows['enable'])){continue;}

      # ホストの詳細を表示
      $buf .= "  <tr>\n";
      $buf .= "    <td>{$hostname}</td>\n";
      $buf .= "    <td>{$rows['ipaddress']}</td>\n";
      $buf .= "    <td>{$rows['categoryname']}</td>\n";
      $buf .= "    <td>{$rows['officename']}</td>\n";
      $buf .= "    <td>{$rows['model']}</td>\n";
      $buf .= "    <td>{$rows['version']}</td>\n";
      $buf .= "    <td>{$rows['serial']}</td>\n";
      $buf .= "    <td align=center><input type=checkbox name={$hostname_js}></td>\n";
      $buf .= "  </tr>\n";
      $target_hosts[] = $hostname;
    }

    $buf .= "<script type=\"text/javascript\">\n";
    $buf .= "<!--\n";
    $buf .= "function CheckBoxSelect(check){\n";
    $buf .= "  var check;\n";
    $buf .= "  if(document.config.checkall.value == \"select\"){\n";
    $buf .= "    document.config.checkall.value = \"unselect\";\n";
    $buf .= "    check = true;\n";
    $buf .= "  }else{\n";
    $buf .= "    document.config.checkall.value = \"select\";\n";
    $buf .= "    check = false;\n";
    $buf .= "  }\n";
    foreach($target_hosts as  $hostname){
      ## 最後の要素は中身がないので繰り返しぬける
      if(empty($hostname)){continue;}

      $hostname_js = preg_replace("/-/","_hyphen_",$hostname);
      $buf .= "  document.config.{$hostname_js}.checked = check;\n";
    }

    $buf .= "}\n";
    $buf .= "// -->\n";
    $buf .= "</script>\n";
    $buf .= "  <tr>\n";
    $buf .= "    <td colspan=7>&nbsp;</td>\n";
    $buf .= "    <td>";
    $buf .= "<input type=button name=checkall value=select  onClick=\"CheckBoxSelect(true);\">\n";
    $buf .= "</td>\n";
    $buf .= "  </tr>\n";
    $buf .= "</table>\n";
    $buf .= "<BR><BR>\n";

    $buf .= "input config<BR>\n";
    $buf .= "<textarea name=config cols=60 rows=8  style=\"font-size:10pt;\" >\n";
    $buf .= "config terminal\n";
    $buf .= "\n";
    $buf .= "<please input config>\n";
    $buf .= "\n";
    $buf .= "end\n";
    $buf .= "copy run start\n";
    $buf .= "</textarea>\n";
    $buf .= "<BR>\n";

    $buf .= "<input type=hidden name=funcs value=tool_configchanger_execute>\n";
    $buf .= "<input type=submit value=OK OnClick=\"return confirm('Execute Config Change?')\";>\n";
    $buf .= "<input type=reset value=RESET>\n";
    
    $buf .= "</form>\n";
    $buf .= "</center>\n";

  }else if( isset($_REQUEST['type']) && ($_REQUEST['type'] === "password")){
    ## password change の場合
    if(empty($_REQUEST['categorycode'])){
      $buf .=  "<center>\n";
      $buf .=  "Please select category.<BR>\n";
      $buf .=  "</center>\n";
    }else{
      $sql = "select vtypass,enable from plugin_haruca_category where categorycode = '{$_REQUEST['categorycode']}'";
      #$result = mysql_query($sql) or die (mysql_error());
      #$row = mysql_fetch_assoc($result);
      #$rows = db_fetch_assoc($sql);
      $rows = db_fetch_row($sql);

      $buf .= "<center>\n";
      $buf .=  "<form method=post action=./haruca.php>\n";
      $buf .=  "<table>\n";
      $buf .=  " <tr>\n";
      $buf .=  "  <td>Old VTY 0-4</td>\n";
      $buf .=  "  <td><input type=text name=oldvty value='{$rows['vtypass']}' disabled></td>\n";
      $buf .=  " </tr>\n";
      $buf .=  " <tr>\n";
      $buf .=  "  <td>New VTY 0-4</td>\n";
      $buf .=  "  <td><input type=text name=newvty></td>\n";
      $buf .=  " </tr>\n";
      $buf .=  " <tr>\n";
      $buf .=  "  <td>Old Enable Secret</td>\n";
      $buf .=  "  <td><input type=text name=olden value='{$rows['enable']}' disabled></td>\n";
      $buf .=  " </tr>\n";
      $buf .=  " <tr>\n";
      $buf .=  "  <td>New Enable Secret</td>\n";
      $buf .=  "  <td><input type=text name=newen></td>\n";
      $buf .=  " </tr>\n";
      $buf .=  "</table>\n";
      $buf .=  "<input type=submit value='Change Password' OnClick=\"return confirm('Execute Password Change?')\";>\n";
      $buf .=  "<input type=hidden name=categorycode value={$_REQUEST['categorycode']}>\n";
      $buf .=  "<input type=hidden name=funcs value=tool_configchanger_execute>\n";
      $buf .=  "</form>\n";
      $buf .=  "</center>\n";
    }

  }

  return $buf;
}

############################3
# ConfigChanger Execute
############################3
function tool_configchanger_execute(){
  global $binpath;
  global $perlpath;

  ob_flush();
  flush();

  $hostnumber=0;

  if(isset($_POST['categorycode'])){
    if(empty($_POST['newen']) && empty($_POST['newvty'])){
      print "Enter password or enable <BR>\n";
      exit;
    }
  
    # generate config strings.
    $configs[] = "config terminal";
    if(!empty($_POST['newen'])){
      $configs[] = "enable secret ".$_POST['newen'];
    }
    if(!empty($_POST['newvty'])){
      $configs[] = "line vty 0 4";
      $configs[] = "password ".$_POST['newvty'];
    }
    $configs[] = "end";
    #$configs[] = "copy run start , , ";
  
    # host extrfuncs
    $sql  = "select host.description from plugin_haruca_host ";
    $sql .= " inner join plugin_haruca_category on plugin_haruca_category.categorycode = plugin_haruca_host.categorycode";
    $sql .= " inner join host                   on host.id = plugin_haruca_host.id ";
    $sql .= " where plugin_haruca_category.categorycode = '".$_POST['categorycode']."' and host.disabled != 'on'";

    #$result = mysql_query($sql) or die (mysql_error());
    #while($row = mysql_fetch_assoc($result)){
    $rows = db_fetch_assoc($sql);
    foreach($rows as $row) {
      $hosts[] = $row['description'];
      $hostnumber++;
    }
  
  }else{
    $configs = explode("\n",$_POST['config']);
    foreach($_POST as $key => $value){
      #print $key . " -> " . $value ."<BR>\n";
      if($value === "on"){
        $hostname_js = preg_replace("/_hyphen_/","-",$key);
        $hosts[] = $hostname_js;
        $hostnumber++;
      }
    }
  }


  if($hostnumber === 0){
    $buf = "No host election";
  }else{

    $cnt=0;
    foreach($hosts as $key => $hostname){
      $cnt++;
  
      $cmd = "";
      foreach( $configs as $line ){
        $cmd .= chop($line) . ", ";
      }

      $cmd = "{$perlpath} {$binpath}router ".$hostname." --config " . "\"".$cmd . "\"";

      $result = `$cmd`;
      $cmd = preg_replace("/.+--config/","",$cmd);

      if(preg_match("/error/",$result)){
        printf("%4d / %4d  %s : ping error skip ",$cnt,$hostnumber,$hostname);
      }else{
        printf("%4d / %4d  %s : change successful ",$cnt,$hostnumber,$hostname);
      }


      #print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      #print " -> $cmd";
      print "<BR>\n";

      ob_flush();
      flush();
    }

    if(isset($_POST['categorycode'])){
      if(!empty($_POST['newvty'])){
        print "VTY password was updated.<BR>\n";
        $sql = "update plugin_haruca_category set vtypass = '".$_POST['newvty']."' where categorycode=".$_POST['categorycode'];
        #mysql_query($sql) or die (mysql_error());
        db_execute($sql);
      }
      if(!empty($_POST['newen'])){
        print "ENABLE password was updated.<BR>\n";
        $sql = "update plugin_haruca_category set enable = '".$_POST['newen']."' where categorycode=".$_POST['categorycode'];
        #mysql_query($sql) or die (mysql_error());
        db_execute($sql);
      }
    }

    $buf = "Config Change was finished.";
  
  }


?>
                          <table width="100%" >
                                  <tr>
                                          <td><?php print $buf; ?></td>
                                  </tr>
                          </table>
  
<?php
}

###############################
# TOOL SHELL FUNCTIONS
###############################
function shell_main(){
  global $binpath;

  if(empty($_POST['keyword'])){
     $keyword = "";
  }else{
     $keyword = $_POST['keyword'];
  }

  $header = "";
	$header .= "<center>\n";
	$header .= "<form method=post name=hostname action=./haruca.php>\n";
	$header .= "command &nbsp;<input name=keyword type=text value=\"{$keyword}\">\n";
	$header .= "<input type=hidden name=funcs value=tool_shell >\n";
	$header .= "<input type=submit value=OK>\n";
	$header .= "<input type=reset  value=RESET>\n";
	$header .= "</form>\n";
	$header .= "<BR>\n";
	$header .= "Available command list.<BR>\n";
	$header .= "<table border=1>\n";
	$header .= " <tr>\n";
	$header .= "  <th>command</th>\n";
	$header .= "  <th>example</th>\n";
	$header .= "  <th>summary</th>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>search</td>\n";
	$header .= "  <td align=left>search [HOSTNAME] [-hitnlvcs]</td>\n";
	$header .= "  <td align=left>Search host from registed devices.</td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>ipsearch</td>\n";
	$header .= "  <td align=left>ipsearch [STRING] [-nosh | -a ]</td>\n";
	$header .= "  <td align=left>Search ip address from registed devices.</td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>list_logtool</td>\n";
	$header .= "  <td align=left>list_logtool [LOGTYPE] [STRING] < ROUTERLIST </td>\n";
	$header .= "  <td align=left>Search STRING from multiple logs.</td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>list_ping</td>\n";
	$header .= "  <td align=left>list_ping < ROUTERLIST </td>\n";
	$header .= "  <td align=left>Execute ping to LIST</td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>fmt_dcrpt</td>\n";
	$header .= "  <td align=left>fmt_dcrpt < FILE </td>\n";
	$header .= "  <td align=left>Decript strings (only begin 7code) </td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>fmt_inter_config</td>\n";
	$header .= "  <td align=left>fmt_inter_config < CISCO_CONFIGFILE </td>\n";
	$header .= "  <td align=left>PickUp interface config from configfile.</td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>fmt_inter_traffic</td>\n";
	$header .= "  <td align=left>fmt_inter_traffic < CISCO_SHOW_INTERFACE </td>\n";
	$header .= "  <td align=left>Formats result of \"show interfaces\"</td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>fmt_iproute</td>\n";
	$header .= "  <td align=left>fmt_iproute < CISCO_SHOW_IP_ROUTE </td>\n";
	$header .= "  <td align=left>Formats result of \"show ip route\"</td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>router</td>\n";
	$header .= "  <td align=left>router [HOSTNAME] [COMMAND [,COMMAND]]</td>\n";
	$header .= "  <td align=left>Auto login host and execute commands.</td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>portlist</td>\n";
	$header .= "  <td align=left>portlist [HOSTNAME]</td>\n";
	$header .= "  <td align=left>List ports of host.</td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "  <td align=center>show</td>\n";
	$header .= "  <td align=left>show [LOGTYPE] [HOSTNAME]</td>\n";
	$header .= "  <td align=left>Print logs.</td>\n";
	$header .= " </tr>\n";
	$header .= "</table>\n";
	$header .= "<BR>\n";
	$header .= "other command<BR>\n";
	$header .= "&nbsp; egrep,\n";
	$header .= "&nbsp; grep,\n";
	$header .= "&nbsp; head,\n";
	$header .= "&nbsp; tail,\n";
	$header .= "&nbsp; uniq,\n";
	$header .= "&nbsp; sort,\n";
	$header .= "&nbsp; sed,\n";
	$header .= "&nbsp; awk,\n";
	$header .= "&nbsp; cut,\n";
	$header .= "&nbsp; tr,\n";
	$header .= "&nbsp; nl,\n";
	$header .= "<BR>\n";
	$header .= "</center>\n";


  $cmd = $keyword;
  $cmd = preg_replace("/;..*/","",$cmd);
  $cmd = preg_replace("/^\ +/","",$cmd);
  $cmd = preg_replace("/\|\ +/","|",$cmd);

  $str_search = "/^fmt_|^ipsearch|^list_|^mac_to_vender|^portlist|^router|^search|^show/";

  $buf = "";
  if(empty($keyword)){
    $buf = "## Please input command";
  }else if(!preg_match($str_search,$cmd)){
    $buf = "## Not Supported Commnads.";
  }else{
    foreach((preg_split("/\|/",$cmd)) as $cmdset){
      foreach((explode(" ",$cmdset)) as $one){
        if(preg_match($str_search,$one)){
          $buf .= $binpath.$one . " ";
        }else{
          #EasyShellからのコンフィグ変更は禁止
          if(preg_match("/--config/",$one)){$one = "";}
          $buf .= $one . " ";
        }
      }
      $buf .=  "|";
    }

    $cmd = substr($buf,0,-1);
    $buf = "<PRE>[EasyShell]$ ".$keyword."\n".`$cmd`."</PRE>\n";
  }
  return $header.$buf;

}

###############################
# LogGetter FUNCTION
###############################
function loggetter_header(){

  $buf = "";

  if(empty($_POST['hostname'])){
     $hostname = "";
  }else{
     $hostname = $_POST['hostname'];
  }

  if(empty($_POST['ipaddress'])){
     $ipaddress = "";
  }else{
     $ipaddress = $_POST['ipaddress'];
  }

  ## config change search box ##
  $buf .= "<script type=\"text/javascript\">\n";
  $buf .= "<!--\n";
  $buf .= "function ResetForm(){\n";
  $buf .= "  document.hostname.hostname.value  = \"\";\n";
  $buf .= "  document.hostname.ipaddress.value = \"\";\n";
  $buf .= "  document.hostname.categorycode.selectedIndex = 0;\n";
  $buf .= "}\n";
  $buf .= "// -->\n";
  $buf .= "</script>\n";

  $buf .= "<center>\n";
  $buf .= "      <form method=post name=hostname action=./haruca.php>\n";
  $buf .= "        <input type=hidden name=funcs value=tool_loggetter >\n";
  $buf .= "        <input type=hidden name=type value=loggetter >\n";
  $buf .= "        <table>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td>hostname</td>\n";
  $buf .= "            <td> <input name=hostname type=text value=\"{$hostname}\"> </td>\n";
  $buf .= "          </tr>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td>ip address</td>\n";
  $buf .= "            <td> <input name=ipaddress type=text value=\"{$ipaddress}\"> </td>\n";
  $buf .= "          </tr>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td>category</td>\n";
  $buf .= "            <td>\n";
  $buf .= "              <select name=categorycode >\n";
  $buf .= "              <option value='' selected>(select category)</option>\n";

  $sql = "select categorycode,categoryname from plugin_haruca_category where vtypass is not NULL";
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $selected = "";
    if(( isset($_REQUEST['type']) && ($_REQUEST['type'] === "loggetter"))&&(isset($_REQUEST['categorycode']) && ($row['categorycode'] === $_REQUEST['categorycode']))){$selected = "selected";}
    $buf .= "                <option value={$row['categoryname']} $selected>{$row['categoryname']}</option>\n";
  }
  $buf .= "              </select>\n";
  $buf .= "            </td>\n";
  $buf .= "          </tr>\n";
  $buf .= "          <tr>\n";
  $buf .= "            <td></td>\n";
  $buf .= "            <td align=center>";
  #$buf .= "<input type=submit name=loggetter value=OK>";
  $buf .= "<button class=\"submit_button\"  name=\"loggetter\" value=\"OK\" >OK</button>\n";
  $buf .= "<input type=button value=RESET onClick=ResetForm();> </td>\n";
  $buf .= "          </tr>\n";
  $buf .= "        </table>\n";
  $buf .= "      </form>\n";
  $buf .= "</center>\n";


  return $buf;

}

function loggetter_main(){
  global $binpath;
  $buf = "";


  ## 検索文字列などがない場合はエラー表示
  if(empty($_POST['hostname']) && empty($_POST['ipaddress']) && empty($_REQUEST['categorycode'])){
    $buf .= "<center>\n";
    $buf .= "Please input search strings or select category.<BR>\n";
    $buf .= "</center>\n";
    return $buf;
  }

  if(empty($_POST['hostname'])){
    $_POST['hostname'] = ".";
  }

  if(empty($_POST['ipaddress'])){
    $_POST['ipaddress'] = ".";
  }

  if(empty($_REQUEST['categorycode'])){
    $cond_category = "";
  }else{
    $cond_category = "--category={$_REQUEST['categorycode']}";
  }

  $list_hostname  = explode("\n",`${binpath}search {$_POST['hostname']} $cond_category -h | sed s/\ \ *//g`);
  $list_ipaddress = explode("\n",`${binpath}ipsearch {$_POST['ipaddress']} -h| sed s/\ \ *//g`);

  $list = array_filter(array_intersect($list_hostname,$list_ipaddress));


  ## hostlist が空の場合はエラー表示
  if(count($list) === 0){
    $buf .= "<center>\n";
    $buf .= "no match.<BR>\n";
    $buf .= "</center>\n";
    return $buf;
  }

  
  $buf .= "<center>\n";
  $buf .= "<form method=post name=loggetter action=./haruca.php>\n";
  $buf .= "select hosts<BR>\n";

  ## hostlist のtableヘッダ表示
  $buf .= "<table border=1>\n";
  $buf .= "  <tr>\n";
  $buf .= "    <th>hostname</th>\n";
  $buf .= "    <th>address</th>\n";
  $buf .= "    <th>category</th>\n";
  $buf .= "    <th>office</th>\n";
  $buf .= "    <th>model</th>\n";
  $buf .= "    <th>version</th>\n";
  $buf .= "    <th>serial</th>\n";
  $buf .= "    <th>check</th>\n";
  $buf .= "  </tr>\n";
  
  #foreach(explode("\n",$list) as $key => $hostname){
  foreach($list as $key => $hostname){
    ## 最後の要素は中身がないので繰り返しぬける
    if(empty($hostname)){continue;}

    $hostname_js = preg_replace("/-/","_hyphen_",$hostname);

    #vtypassがなければ対象外
    $sql  = "select host.hostname as ipaddress ,plugin_haruca_category.categoryname as categoryname ,plugin_haruca_office.officename as officename, ";
    $sql .= " plugin_haruca_host.model as model,plugin_haruca_host.version as version ,plugin_haruca_host.serial as serial, ";
    $sql .= " plugin_haruca_category.vtypass as vtypass from host ";
    $sql .= " inner join plugin_haruca_host on host.id = plugin_haruca_host.id ";
    $sql .= " inner join plugin_haruca_category on plugin_haruca_host.categorycode=plugin_haruca_category.categorycode ";
    $sql .= " inner join plugin_haruca_office on plugin_haruca_office.officecode=plugin_haruca_host.officecode ";
    $sql .= " where description='{$hostname}' and host.disabled != 'on' ";


    #$result = mysql_query($sql) or die (mysql_error());
    #$row = mysql_fetch_assoc($result);

    #$rows = db_fetch_assoc($sql);
    $rows = db_fetch_row($sql);
    if(empty($rows['vtypass'])){continue;}

  
    # ホストの詳細を表示
    $buf .= "  <tr>\n";
    $buf .= "    <td>{$hostname}</td>\n";
    $buf .= "    <td>{$rows['ipaddress']}</td>\n";
    $buf .= "    <td>{$rows['categoryname']}</td>\n";
    $buf .= "    <td>{$rows['officename']}</td>\n";
    $buf .= "    <td>{$rows['model']}</td>\n";
    $buf .= "    <td>{$rows['version']}</td>\n";
    $buf .= "    <td>{$rows['serial']}</td>\n";
    $buf .= "    <td align=center><input type=checkbox name={$hostname_js}></td>\n";
    $buf .= "  </tr>\n";
    $target_hosts[] = $hostname;
  }



  $buf .= "<script type=\"text/javascript\">\n";
  $buf .= "<!--\n";
  $buf .= "function CheckBoxSelect(check){\n";
  $buf .= "  var check;\n";
  $buf .= "  if(document.loggetter.checkall.value == \"select\"){\n";
  $buf .= "    document.loggetter.checkall.value = \"unselect\";\n";
  $buf .= "    check = true;\n";
  $buf .= "  }else{\n";
  $buf .= "    document.loggetter.checkall.value = \"select\";\n";
  $buf .= "    check = false;\n";
  $buf .= "  }\n";
  foreach($target_hosts as  $hostname){
    ## 最後の要素は中身がないので繰り返しぬける
    if(empty($hostname)){continue;}

    $hostname_js = preg_replace("/-/","_hyphen_",$hostname);
    $buf .= "  document.loggetter.{$hostname_js}.checked = check;\n";
  }

  $buf .= "}\n";

  $buf .= "function execute(){\n";
  $buf .= "  var count;\n";
  $buf .= "  count = 0;\n";

  foreach($target_hosts as  $hostname){
    ## 最後の要素は中身がないので繰り返しぬける
    if(empty($hostname)){continue;}

    $hostname_js = preg_replace("/-/","_hyphen_",$hostname);
    $buf .= "  if(document.loggetter.{$hostname_js}.checked){count++;}\n";
  }

  $buf .= "  if(!document.loggetter.str_log.value){\n";
  $buf .= "    alert(\"No Log Strings\");\n";
  $buf .= "  }else if(count == 0){\n";
  $buf .= "    alert(\"No Selected Host\");\n";
  $buf .= "  }else{\n";
  $buf .= "    if(confirm('Get Logs ?')){\n";
  $buf .= "      document.loggetter.submit();\n";
  $buf .= "    }\n";
  $buf .= "  }\n";



  $buf .= "}\n";
  $buf .= "// -->\n";
  $buf .= "</script>\n";


  $buf .= "  <tr>\n";
  $buf .= "    <td colspan=7>&nbsp;</td>\n";
  $buf .= "    <td>";
  $buf .= "<input type=button name=checkall value=select  onClick=\"CheckBoxSelect(true);\">\n";
  $buf .= "</td>\n";
  $buf .= "  </tr>\n";
  $buf .= "</table>\n";
  $buf .= "<BR><BR>\n";

  $buf .= "input log command<BR>\n";
  $buf .= "<textarea name=str_log cols=50 rows=4  style=\"font-size:10pt;\" >\n";
  $buf .= "</textarea>\n";
  $buf .= "<BR>\n";

  $buf .= "<input type=hidden name=type value=loggetter_execute>\n";
  $buf .= "<input type=button value=OK OnClick=\"execute('Execute Log Getting ?')\";>\n";
  $buf .= "<input type=reset value=RESET>\n";
  
  $buf .= "</form>\n";
  $buf .= "</center>\n";


  return $buf;
}

############################3
# LogGetter Execute
############################3
function tool_loggetter_execute(){
  global $binpath;
  global $tmpdir;
  global $perlpath;
  
  $str_log = str_replace("\r\n","\n",$_REQUEST['str_log']);
  $cmds = explode("\n",$str_log);

  # host 
  foreach($_POST as $key => $value){
    if($value === "on"){
      $hostname_js = preg_replace("/_hyphen_/","-",$key);
      $hosts[] = $hostname_js;
    }
  }

  # get log
  foreach($hosts as $key => $hostname){
    $cmd = "";
    foreach( $cmds as $line ){
      $cmd .= $line . ", ";
    }

    $filename = $tmpdir.$hostname."_".rtrim(`date '+%Y%m%d-%H%M%S'`,"\n").".txt";
    $filenames[] = $filename;
    $result = `{$perlpath} {$binpath}router $hostname $cmd`;
    $result = str_replace("\n","\r\n",$result);
    file_put_contents($filename,$result);
  }

  # zip routine
  $filename_zip = $tmpdir."autolog.zip";
  $cmd = "zip -j $filename_zip ";
  foreach($filenames as $key => $filename){
    $cmd .= $filename . " ";
  }
  `$cmd`;



  $filename_zip_dl = preg_replace("/\/.+\//","",$filename_zip);

  if(0){
    header('Pragma: no-cache');
    header('Cache-Control: no-cache');
  }else if(0){
    header('Pragma: private');
    header('Cache-control: private, must-revalidate');
  }else{
    header('Pragma: cache;');
    header('Cache-Control: public');
  }

  header('Content-Description: File Transfer');
  header('Content-type: application/zip');
  #header("Content-type: application/octet-stream;\n");
  header('Content-Disposition: attachment; filename="'.$filename_zip_dl.'"');
  header('Content-Transfer-Encoding: binary');
  header('Content-Length: '.filesize($filename_zip));
  readfile($filename_zip);


  # delete files
  $filenames[] = $filename_zip;
  $cmd = "rm -f  ";
  foreach($filenames as $key => $filename){
    $cmd .= $filename . " ";
  }
  `$cmd`;

}


###############################
# TOOL BANDWIDTH CALC FUNCTIONS
###############################
function bwcalc_main(){

  $buf = "";

  $buf .= "<script type=\"text/javascript\">\n";
  $buf .= "<!--\n";
  $buf .= "function bwcalc(){\n";
  $buf .= "  var buf;\n";
  $buf .= "  var postfix;\n";
  $buf .= "  if((isNaN(document.hostname.data_vol.value))||(isNaN(document.hostname.bw.value))){\n";
  $buf .= "    alert(\"Not a Number\");\n";
  $buf .= "    clear_form();\n";
  $buf .= "    return false;\n";
  $buf .= "  }\n";
  $buf .= "\n";
  $buf .= "  data = Number(document.hostname.data_vol.value) * Number(document.hostname.prefix.value) * Number(document.hostname.unit_data.value);\n";
  $buf .= "  bw   = Number(document.hostname.bw.value)       * Number(document.hostname.unit_bw.value);\n";
  $buf .= "\n";
  $buf .= "  time = data / bw;\n";
  $buf .= "  if(time >= 3600){\n";
  $buf .= "    buf = \"Required time :\\n \" + Math.round(time/3600*10)/10 + \"hour\";\n";
  $buf .= "  }else if(time >= 60){\n";
  $buf .= "    buf = \"Required time :\\n \" + Math.round(time/60*10)/10 + \"min\";\n";
  $buf .= "  }else{\n";
  $buf .= "    buf = \"Required time :\\n \" + Math.round(time*10)/10 + \"sec\";\n";
  $buf .= "  }\n";
  $buf .= "\n";
  $buf .= "  alert(buf);\n";
  $buf .= "}\n";
  $buf .= "\n";
  $buf .= "function timelimit(){\n";
  $buf .= "  var buf;\n";
  $buf .= "  var data;\n";
  $buf .= "  var time;\n";
  $buf .= "  var bps;\n";
  $buf .= "  var postfix;\n";
  $buf .= "  if((isNaN(document.hostname.data_vol.value))||(isNaN(document.hostname.time.value))){\n";
  $buf .= "    alert(\"Not a Number\");\n";
  $buf .= "    clear_form();\n";
  $buf .= "    return false;\n";
  $buf .= "  }\n";
  $buf .= "\n";
  $buf .= "  data = Number(document.hostname.data_vol.value) * Number(document.hostname.prefix.value) * Number(document.hostname.unit_data.value);\n";
  $buf .= "  time = Number(document.hostname.time.value)     * Number(document.hostname.unit_time.value);\n";
  $buf .= "\n";
  $buf .= "  bps = data / time;\n";
  $buf .= "\n";
  $buf .= "  if(bps >= 1000000000000){\n";
  $buf .= "    bps = bps / 1000000000000;\n";
  $buf .= "    postfix = \"T\";\n";
  $buf .= "  }else if(bps >= 1000000000){\n";
  $buf .= "    bps = bps / 1000000000;\n";
  $buf .= "    postfix = \"G\";\n";
  $buf .= "  }else if(bps >= 1000000){\n";
  $buf .= "    bps = bps / 1000000;\n";
  $buf .= "    postfix = \"M\";\n";
  $buf .= "  }else if(bps >= 1000){\n";
  $buf .= "    bps = bps / 1000;\n";
  $buf .= "    postfix = \"k\";\n";
  $buf .= "  }else{\n";
  $buf .= "    postfix = \"\";\n";
  $buf .= "  }\n";
  $buf .= "\n";
  $buf .= "  buf = \"Required Bandwidth :\\n \" + Math.round(bps*10)/10 + postfix + \"bit/sec\";\n";
  $buf .= "\n";
  $buf .= "  alert(buf);\n";
  $buf .= "}\n";
  $buf .= "\n";
  $buf .= "function clear_form(){\n";
  $buf .= "  document.hostname.data_vol.value = \"\";\n";
  $buf .= "  document.hostname.time.value = \"\";\n";
  $buf .= "}\n";
  $buf .= "\n";
  $buf .= "// -->\n";
  $buf .= "</script>\n";
  $buf .= "\n";
  $buf .= "</head>\n";
  $buf .= "\n";
  $buf .= "<body bgcolor=\"#ffffff\" link=\"blue\" alink=\"blue\" vlink=\"blue\" >\n";
  $buf .= "<center>\n";
  $buf .= "<form method=post name=\"hostname\">\n";
  $buf .= "<table>\n";
  $buf .= "<tr>\n";
  $buf .= " <td> DataSize </td>\n";
  $buf .= " <td> <input name=data_vol type=text size=10> </td>\n";
  $buf .= " <td>\n";
  $buf .= "<select name=prefix>\n";
  $buf .= " <option value=1000> K\n";
  $buf .= " <option value=1000000 selected > M\n";
  $buf .= " <option value=1000000000> G\n";
  $buf .= " <option value=1000000000000> T\n";
  $buf .= "</select>\n";
  $buf .= "<select name=unit_data>\n";
  $buf .= " <option value=8 selected > byte\n";
  $buf .= " <option value=1> bit\n";
  $buf .= "</select>\n";
  $buf .= " </td>\n";
  $buf .= "</tr>\n";
  $buf .= "\n";
  $buf .= "<tr>\n";
  $buf .= " <td> BandWidth </td>\n";
  $buf .= " <td> <input name=bw type=text size=10> </td>\n";
  $buf .= " <td align=left>\n";
  $buf .= "<select name=unit_bw>\n";
  $buf .= " <option value=1000000 selected >Mbps\n";
  $buf .= " <option value=1000000000       >Gbps\n";
  $buf .= "</select>\n";
  $buf .= "<input type=button value=\"calc time\" onClick=bwcalc();>\n";
  $buf .= " </td>\n";
  $buf .= "</tr>\n";
  $buf .= "\n";
  $buf .= "<tr>\n";
  $buf .= " <td> TimeLimit </td>\n";
  $buf .= " <td> <input name=time type=text size=10> </td>\n";
  $buf .= " <td align=left>\n";
  $buf .= "<select name=unit_time>\n";
  $buf .= " <option value=1             >sec\n";
  $buf .= " <option value=60            >min\n";
  $buf .= " <option value=3600 selected >hour\n";
  $buf .= " <option value=86400         >day\n";
  $buf .= "</select>\n";
  $buf .= "<input type=button value=\"calc bandwidth\" onClick=timelimit();>\n";
  $buf .= " </td>\n";
  $buf .= "</tr>\n";
  $buf .= "\n";
  $buf .= "</table>\n";
  $buf .= "\n";
  $buf .= "<BR>\n";
  $buf .= "<input type=button value=\"RESET\" onClick=clear_form(); >\n";
  $buf .= "\n";
  $buf .= "</form>\n";
  $buf .= "<BR>\n";
  $buf .= "<center>\n";

  return $buf;

}

############################3
# TOOL WILDCARDMASK FUNCTION
############################3
function wcmcalc_main(){
  global $binpath;

  if(isset($_POST['address1']) && preg_match("/^([0-9]{1,3}\.){3}[0-9]{1,3}$/",$_POST['address1'])){
    $address1 = $_POST['address1'];
  }else{
    $address1 = "";
  }

  if(isset($_POST['address2']) && preg_match("/^([0-9]{1,3}\.){3}[0-9]{1,3}$/",$_POST['address2'])){
    $address2 = $_POST['address2'];
  }else{
    $address2 = "";
  }

  $buf = "";

  $buf .= "<script type=\"text/javascript\"><!--\n";
  $buf .= "function clearText() {\n";
  $buf .= "  for(var i=0;i<document.wcmcalc.elements.length;i++){\n";
  $buf .= "    if(document.wcmcalc.elements[i].type == \"text\"){\n";
  $buf .= "      document.wcmcalc.elements[i].value = \"\";\n";
  $buf .= "    }\n";
  $buf .= "  }\n";
  $buf .= "}\n";
  $buf .= "//--></script>\n";

  $buf .= "<form method=post name=wcmcalc action=./haruca.php>\n";
  $buf .= "<center>\n";
  $buf .= "<input name=address1 type=text size=15 value=\"$address1\" >\n";
  $buf .= "〜\n";
  $buf .= "<input name=address2 type=text size=15 value=\"$address2\" >\n";
  $buf .= "<BR>\n";
  $buf .= "<BR>\n";
  $buf .= "<input type=hidden name=funcs value=tool_wcmcalc>\n";
  $buf .= "<input type=submit value=\"OK\">\n";
  $buf .= "<input type=button  value=\"CLEAR\" onClick=clearText();>\n";
  $buf .= "</form>\n";
  $buf .= "<BR>\n\n";
  $buf .= "<PRE>\n";

  if(empty($address1) || empty($address2)){
    $buf .= "input address \n";
  }else{
    $result = `{$binpath}wildcard_calc $address1 $address2`;
    $buf .= $result."\n";
  }
  $buf .= "</PRE>\n";
  $buf .= "</center>\n";
  $buf .= "IPv4 Calculator <BR>\n";
  $buf .= "Copyright (C) Krischan Jodies 2000 - 2004 <BR>\n";
  $buf .= "</center>\n";

  return $buf;

}

############################3
# MANAGEMENT HOST FUNCTION
############################3
function manage_host_main(){
  $buf = "";

  $sql  = "select categorycode,categoryname from plugin_haruca_category order by categorycode asc ";
  $result = db_fetch_assoc($sql);
  foreach($result as $row) {
    $categories{$row['categorycode']} = $row['categoryname'];
  }

  $sql  = "select officecode,officename from plugin_haruca_office order by officecode asc ";
  $result = db_fetch_assoc($sql);
  foreach($result as $row) {
    $offices{$row['officecode']} = $row['officename'];
  }

  $sql  = "select ";
  $sql .= " host.disabled as disabled,";
  $sql .= " host.id as id,";
  $sql .= " plugin_haruca_host.id as id_haruca,";
  $sql .= " host.description as hostname ,";
  $sql .= " host.hostname as address , ";
  $sql .= " plugin_haruca_category.categoryname as categoryname, ";
  $sql .= " plugin_haruca_office.officename as officename,";
  $sql .= " plugin_haruca_category.categorycode as categorycode , ";
  $sql .= " plugin_haruca_office.officecode as officecode, ";
  $sql .= " plugin_haruca_host.model as model ,";
  $sql .= " plugin_haruca_host.version as version,";
  $sql .= " plugin_haruca_host.serial as serial ,";
  $sql .= " plugin_haruca_category.vtypass as vtypass ";
  $sql .= "from host ";
  $sql .= " left join plugin_haruca_host     on plugin_haruca_host.id           = host.id ";
  $sql .= " left join plugin_haruca_category on plugin_haruca_host.categorycode = plugin_haruca_category.categorycode ";
  $sql .= " left join plugin_haruca_office   on plugin_haruca_office.officecode = plugin_haruca_host.officecode ";
  $sql .= " where host.disabled != 'on' ";


  $buf .= "<center>\n";
  $buf .= "<form method=post action=./haruca.php>\n";
  $buf .= "\n";
  $buf .= "<table cellspacing=0 cellpadding=0 border=1>\n";
  $buf .= "<tr>\n";
  $buf .= " <th width=50><a href=./haruca.php?funcs=manage_host&sort=id>ID</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=manage_host&sort=hostname>hostname</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=manage_host&sort=address>Address</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=manage_host&sort=category>Category</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=manage_host&sort=office>Office</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=manage_host&sort=model>Model</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=manage_host&sort=version>Version</a></th>\n";
  $buf .= " <th><a href=./haruca.php?funcs=manage_host&sort=serial>Serial</a></th>\n";
  $buf .= " <th>GetInfo</th>\n";
  $buf .= "</tr>\n";

  if(isset($_REQUEST['sort'])){
    if($_REQUEST['sort'] === "id"){
      $sql .= " order by host.id asc ";
      $buf .= "<input type=hidden name=sort value=id>\n";
    }else if($_REQUEST['sort'] === "hostname"){
      $sql .= " order by host.description asc ";
      $buf .= "<input type=hidden name=sort value=hostname>\n";
    }else if($_REQUEST['sort'] === "address"){
      $sql .= " order by INET_ATON(host.hostname) asc , host.description asc";
      $buf .= "<input type=hidden name=sort value=address>\n";
    }else if($_REQUEST['sort'] === "category"){
      $sql .= " order by plugin_haruca_host.categorycode asc , host.description asc";
      $buf .= "<input type=hidden name=sort value=category>\n";
    }else if($_REQUEST['sort'] === "office"){
      $sql .= " order by plugin_haruca_office.officecode asc , host.description asc";
      $buf .= "<input type=hidden name=sort value=office>\n";
    }else if($_REQUEST['sort'] === "model"){
      $sql .= " order by plugin_haruca_host.model asc , host.description asc";
      $buf .= "<input type=hidden name=sort value=model>\n";
    }else if($_REQUEST['sort'] === "version"){
      $sql .= " order by plugin_haruca_host.version asc , host.description asc";
      $buf .= "<input type=hidden name=sort value=version>\n";
    }else if($_REQUEST['sort'] === "serial"){
      $sql .= " order by plugin_haruca_host.serial asc , host.description asc";
      $buf .= "<input type=hidden name=sort value=serial>\n";
    }
  }else{
    $sql .= " order by host.description asc ";
  }
    
  $result = db_fetch_assoc($sql);
  foreach($result as $row) {
    if(empty($row['model'])){$row['model']="-";}
    if(empty($row['version'])){$row['version']="-";}
    if(empty($row['serial'])){$row['serial']="-";}
    $buf .= "<tr>\n";
  
    $buf .= " <td align=center>{$row['id']}</td>\n";
    $buf .= " <td align=left>{$row['hostname']}</td>\n";
    $buf .= " <td align=left>{$row['address']}</td>\n";
  
    $buf .= " <td align=left>\n";
    $buf .= "  <select name=categorycode:{$row['id']} >\n";
    foreach($categories as $code  => $value){
      $selected = "";
      if($code == $row['categorycode']){ $selected = "selected"; }
      $buf .= "  <option value=$code $selected>{$categories{$code}}</option>\n";
    }
    $buf .= "  </select>\n";
    $buf .= "</td>\n";
  
    $buf .= " <td align=left>\n";
    $buf .= "  <select name=officecode:{$row['id']} >\n";
    foreach($offices as $code => $value){
      $selected = "";
      if($code == $row['officecode']){ $selected = "selected"; }
      $buf .=  "  <option value=$code $selected>{$offices{$code}}</option>\n";
    }
    $buf .= " </select>\n";
    $buf .= "</td>\n";
  
    $buf .= "<td align=left>{$row['model']}</td>\n";
    $buf .= "<td align=left>{$row['version']}</td>\n";
    $buf .= "<td align=left>{$row['serial']}</td>\n";
 
    if(empty($row['vtypass'])){
      $disabled="disabled";
    }else{
      $disabled="";
    }
  
    $buf .= "<td align=center>";
    #$buf .= "<input type=submit name={$row['id']} value=get $disabled>";
    $buf .= "<button class=\"submit_button\"  name=\"{$row['id']}\" value=\"get\" $disabled>get</button>\n";
    $buf .= "</td>\n";
    $buf .= "</tr>\n";
  
  }

  $buf .= "<tr>\n";
  $buf .= "<td colspan=8></td>\n";
  $buf .= "<td >";
  #$buf .= "<input type=submit name=type value=AllGet OnClick=\"return confirm('Get information ?')\";>";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"AllGet\" OnClick=\"return confirm('Get information from all host?')\"; >AllGet</button>\n";
  $buf .= "</td>\n";
  $buf .= "</tr>\n";

  $buf .= "</table>\n";
  $buf .= "<BR>\n";

  $sql  = "select id from plugin_haruca_host order by id";
  $result = db_fetch_assoc($sql);
  foreach($result as $row) {
    $registed_hosts[] = $row['id'];
  }

  $sql  = "select id from host order by id";
  $result = db_fetch_assoc($sql);
  foreach($result as $row) {
    $cacti_hosts[] = $row['id'];
  }

  if(!($registed_hosts === $cacti_hosts)){
    $buf .= "Modified cacti host information.<BR>\n";
    $buf .= "Please click UPDATE button , now updating haruca information.<BR>\n";
  }

  #$buf .= "<input type=submit name=type value=update OnClick=\"return confirm('Edit confirm?')\";>\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"update\" OnClick=\"return confirm('Edit confirm?')\"; >update</button>\n";
  $buf .= "<input type=hidden name=funcs value=manage_host >\n";
  $buf .= "</form>\n";
  $buf .= "<BR>\n";
  $buf .= "</center>\n";
  
  return $buf;

}

########################################
# MANAGEMENT CATEGORY FUNCTION
########################################
function manage_category_main(){

  $itemlist[] = "prepare_cmd";
  $itemlist[] = "uid_prompt";
  $itemlist[] = "username";
  $itemlist[] = "pwd_prompt";
  $itemlist[] = "vtypass";
  $itemlist[] = "enable_cmd";
  $itemlist[] = "en_prompt";
  $itemlist[] = "enable";

  $buf = "";

  $buf .= "<script type=\"text/javascript\"><!--\n";
  $buf .= "function default_prepare_cmd(categorycode) {\n";
  $buf .= " var buf;\n";

  $buf .= " list = new Array (\"${itemlist[0]}:\"+categorycode";
  for($num = 1; $num < count($itemlist); $num++) {
    $buf .= ",\"".$itemlist[$num] . ":\"+categorycode";
  }
  $buf .= ");\n";

  $buf .= " buf = String(document.manage_category.elements['login_method:'+categorycode].value);\n";
  $buf .= " if(buf.length == 0){\n";
  $buf .= "  for(i=0;i<list.length ; i++){ document.manage_category.elements[list[i]].disabled = true; }\n";
  $buf .= " }else{\n";
  $buf .= "  for(i=0;i<list.length ; i++){ document.manage_category.elements[list[i]].disabled = false; }\n";
  $buf .=  "}\n";
  $buf .=  "}\n";
  $buf .=  "//--></script>\n";

  $buf .= "<center>\n";
  $buf .= "<form method=post name=manage_category action=./haruca.php>\n";
  $buf .= "<table cellspacing=0 cellpadding=0 border=1>\n";
  $buf .= "<tr>\n";
  $buf .= " <th width=100>ID</th>\n";
  $buf .= " <th>Name</th>\n";
  $buf .= " <th>Method</th>\n";

  foreach($itemlist as $key => $value){
    $buf .= " <th>$value</th>\n";
  }

  $buf .= "</tr>\n";


  $sql  = "select categorycode,categoryname,login_method,prepare_cmd,enable_cmd,uid_prompt,";
  $sql .= " pwd_prompt,en_prompt,username,vtypass,enable from plugin_haruca_category order by categorycode asc";

  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {

    # デフォルトのカテゴリは操作させない
    if($row['categorycode'] == 1 ){
      $disabled = "disabled";
    }else{
      $disabled = "";
      $categorycodes[$row['categorycode']] = $row['categoryname'];
    }

    $buf .= "<tr>\n";
    $buf .= " <td align=center>{$row['categorycode']}</td>\n";
    $buf .= " <td><input type=text size=8 name=\"categoryname:{$row['categorycode']}\" value=\"{$row['categoryname']}\" {$disabled} ></td>\n";


    #ログイン方法選択
    #現時点ではtelnet,sshのみ対応
    $buf .= " <td>\n";
    $buf .= "  <select name=\"login_method:{$row['categorycode']}\" onChange=\"default_prepare_cmd('$row[categorycode]');\">\n";

    $flg_tel_sel = "";
    $flg_ssh_sel = "";
    if($row['login_method'] === "telnet"){
      $flg_tel_sel = "selected";
    }elseif($row['login_method'] === "ssh"){
      $flg_ssh_sel = "selected";
    }

    $buf .= "   <option value=\"\"                 {$disabled} ></option>\n";
    $buf .= "   <option value=\"telnet\" {$flg_tel_sel} {$disabled} >telnet</option>\n";
    $buf .= "   <option value=\"ssh\"    {$flg_ssh_sel} {$disabled} >ssh</option>\n";
    $buf .= "  </select>\n";
    $buf .= " </td>\n";


    #telnet以外が選択されていたら、以下の項目はすべて選択不可とする
    if(($row['login_method'] === "telnet")||($row['login_method'] === "ssh")){
      $disabled = "";
    }else{
      $disabled = "disabled";
    }

    foreach($itemlist as $key => $value){
      $buf .= " <td><input type=text size=8 name=\"$value:{$row['categorycode']}\"  value=\"{$row[$value]}\"  {$disabled} ></td>\n";
    }

    $buf .= "</tr>\n";
  }
  $buf .= "<tr>\n";
  $buf .= " <td align=center>New Record</td>\n";
  $buf .= " <td><input type=text size=8 name=categoryname:new value=\"\" ></td>\n";
  $buf .= " <td>\n";
  $buf .= "  <select name=\"login_method:new\" onChange=\"default_prepare_cmd('new');\">\n";
  $buf .= "   <option value=\"\"       ></option>\n";
  $buf .= "   <option value=\"telnet\" >telnet</option>\n";
  $buf .= "   <option value=\"ssh\" >ssh</option>\n";
  $buf .= "  </select>\n";
  $buf .= " </td>\n";

  foreach($itemlist as $key => $value){
    $buf .= " <td><input type=text size=8 name=$value:new  value=\"\" disabled></td>\n";
  }

  $buf .= "</tr>\n";
  $buf .= "</table>\n";

  $buf .= "<BR>\n";
  $buf .= "ID<select name='removecode'>\n";
  $buf .= "  <option value=0>select remove code\n";
  
  foreach($categorycodes as $key => $value){
    $buf .= "  <option value=$key>$value\n";
  }
  $buf .= "</select>\n";

  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"remove\" OnClick=\"return confirm('Remove confirm?')\"; >remove</button>\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"update\" OnClick=\"return confirm('Update confirm?')\"; >update</button>\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"insert\" OnClick=\"return confirm('Insert confirm?')\"; >insert</button>\n";
  $buf .= "<input type=hidden name=funcs value=manage_category >\n";
  $buf .= "</form>\n";
  $buf .= "<BR>\n";
  $buf .= "</center>\n";

  return $buf;
}



############################3
# MANAGEMENT LOGTYPE FUNCTION
############################3
function manage_logtype_main(){
  $buf = "";

  $sql  = "select plugin_haruca_logtype.logtypecode as logtypecode,plugin_haruca_category.categorycode as categorycode from plugin_haruca_cat_get_log ";
  $sql .= "inner join plugin_haruca_logtype on plugin_haruca_cat_get_log.logtypecode = plugin_haruca_logtype.logtypecode ";
  $sql .= "inner join plugin_haruca_category on plugin_haruca_category.categorycode = plugin_haruca_cat_get_log.categorycode ";
  $sql .= "order by plugin_haruca_logtype.logtypecode ";
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $avail{$row['categorycode']}{$row['logtypecode']} = 1;
  }

  $buf .= "<center>\n";
  $buf .= "<form method=post action=./haruca.php>\n";

  $buf .= "<table cellspacing=0 cellpadding=0 border=1>\n";
  $buf .= "<tr>\n";
  $buf .= " <th width=100>ID</th>\n";
  $buf .= " <th>LogName</th>\n";
  $buf .= " <th>Command</th>\n";
  $buf .= " <th>DiffCheck</th>\n";
  $buf .= " <th>IgnoreStrings</th>\n";
  $buf .= " <th>Cycle</th>\n";

  $sql  = "select categorycode,categoryname from plugin_haruca_category where vtypass is not NULL and vtypass <> '' order by categorycode";
  $cnt=0;
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    $cat_name_to_code{$row['categoryname']} = $row['categorycode'];
    $buf .= " <th>{$row['categoryname']}</th>\n";
    $cnt++;
    $categorycodes[$row['categorycode']] = $row['categoryname'];
  }
  $buf .= "</tr>\n";
  
  $sql  = "select logtypecode,logname,loggetcmd,diffcheck,cycle,ignore_str from plugin_haruca_logtype order by logtypecode asc";
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    if($row['diffcheck'] == 1){
      $checked_diffcheck = "checked";
    }else{
      $checked_diffcheck = "";
    }
    $buf .= "<tr>\n";
    $buf .= " <td align=center width=20>{$row['logtypecode']}</td>\n";
    $buf .= " <td><input type=text size=10 name=logname:{$row['logtypecode']}    value=\"{$row['logname']}\"  ></td>\n";
    $buf .= " <td><input type=text size=20 name=loggetcmd:{$row['logtypecode']}  value=\"{$row['loggetcmd']}\"   ></td>\n";
    $buf .= " <td align=center><input type=checkbox     name=diffcheck:{$row['logtypecode']}   $checked_diffcheck ></td>\n";
    $buf .= " <td><input type=text size=50 name=ignore_str:{$row['logtypecode']}      value=\"{$row['ignore_str']}\" ></td>\n";
    $buf .= " <td><input type=text size=5 name=cycle:{$row['logtypecode']}      value=\"{$row['cycle']}\" ></td>\n";
  
    foreach($categorycodes as $key => $value){
      if(isset($avail{$key}{$row['logtypecode']})){
        $checked = "checked";
      }else{
        $checked = "";
      }
      $buf .= " <td><input type=checkbox name=avail:{$key}:{$row['logtypecode']} {$checked}></td>\n";
    }
  
    $buf .= "</tr>\n";
    if($row['logtypecode'] != 1 && $row['logtypecode'] != 2){
      $logtypecodes[$row['logtypecode']] = $row['logname'];
    }
  }
  
  $buf .= "<tr>\n";
  $buf .= " <td align=center width=20>New Record</td>\n";
  $buf .= " <td><input type=text size=10 name=logname:new    value=\"\" ></td>\n";
  $buf .= " <td><input type=text size=20 name=loggetcmd:new  value=\"\" ></td>\n";
  $buf .= " <td align=center><input type=checkbox size=5 name=diffcheck:new       ></td>\n";
  $buf .= " <td><input type=text size=50 name=ignore_str:new      value=\"\" ></td>\n";
  $buf .= " <td><input type=text size=5 name=cycle:new      value=\"\" ></td>\n";
  $buf .= str_repeat(" <td>&nbsp;</td>\n",$cnt);
  $buf .= "</tr>\n";
  $buf .= "</table>\n";
  $buf .= "<BR>\n";
  $buf .= "ID<select name='removecode'>\n";
  $buf .= "  <option value=0>select remove logname\n";
  foreach($logtypecodes as $key => $value){
    $buf .= "  <option value=$key>$value\n";
  }
  $buf .= "</select>\n";

  $buf .= "<input type=hidden name=funcs value=manage_logtype >\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"remove\" OnClick=\"return confirm('Remove confirm?')\"; >remove</button>\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"update\" OnClick=\"return confirm('Update confirm?')\"; >update</button>\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"insert\" OnClick=\"return confirm('Insert confirm?')\"; >insert</button>\n";
  $buf .= "</form>\n";
  $buf .= "<BR>\n";
  $buf .= "</center>\n";
  
  
  return $buf;
}


########################################################
# MANAGEMENT TRAPTYPE FUNCTION
########################################################
function manage_traptype_main(){
  $buf = "";


  $buf .= "<center>\n";
  $buf .= "<form method=post action=./haruca.php>\n";
  
  $buf .= "<table cellspacing=0 cellpadding=0 border=1>\n";
  $buf .= "<tr>\n";
  $buf .= " <th>ID</th>\n";
  $buf .= " <th>OID</th>\n";
  $buf .= " <th>Name</th>\n";
  $buf .= " <th>TargetOID</th>\n";
  $buf .= " <th>SummaryOID</th>\n";
  $buf .= " <th>Available</th>\n";
  $buf .= " <th>AlertMail</th>\n";
  $buf .= "</tr>\n";
  
  $sql  = "select oidstring,trapname,target,summary,available,alertmail from plugin_haruca_traptype ";
  $cnt=0;
  #$result = mysql_query($sql) or die (mysql_error());
  #while($row = mysql_fetch_assoc($result)){
  $rows = db_fetch_assoc($sql);
  foreach($rows as $row) {
    if($row['available']){
     $checked_available="checked";
    }else{
     $checked_available="";
    }
    if($row['alertmail']){
     $checked_alertmail="checked";
    }else{
     $checked_alertmail="";
    }
    $cnt++;
  
    $buf .= "<tr>\n";
    $buf .= " <td align=center>{$cnt}</td>\n";
    $buf .= " <td align=center><input type=text  name=\"oidstring:{$row['trapname']}\" value=\"{$row['oidstring']}\" ></td>\n";
    $buf .= " <td align=center><input type=text  name=\"trapname:{$row['trapname']}\"  value=\"{$row['trapname']}\"  ></td>\n";
    $buf .= " <td align=center><input type=text  name=\"target:{$row['trapname']}\"    value=\"{$row['target']}\"    ></td>\n";
    $buf .= " <td align=center><input type=text  name=\"summary:{$row['trapname']}\"   value=\"{$row['summary']}\"   ></td>\n";
    $buf .= " <td align=center><input type=checkbox  name=\"available:{$row['trapname']}\"  {$checked_available}   ></td>\n";
    $buf .= " <td align=center><input type=checkbox  name=\"alertmail:{$row['trapname']}\"  {$checked_alertmail}   ></td>\n";
    $buf .= "</tr>\n";

    if($row['trapname'] != "PingFail" && $row['trapname'] != "PingSuccess"){
      $trapnames[] = $row['trapname'];
    }

  }

  $buf .= "<tr>\n";
  $buf .= " <td align=center>NewRecord</td>\n";
  $buf .= " <td align=center><input type=text  name=oidstring:new value=\"\" ></td>\n";
  $buf .= " <td align=center><input type=text  name=trapname:new  value=\"\" ></td>\n";
  $buf .= " <td align=center><input type=text  name=target:new    value=\"\" ></td>\n";
  $buf .= " <td align=center><input type=text  name=summary:new   value=\"\" ></td>\n";
  $buf .= " <td align=center>&nbsp;</td>\n";
  $buf .= " <td align=center>&nbsp;</td>\n";
  $buf .= "</tr>\n";
  $buf .= "</table>\n";
  $buf .= "<BR>\n";
  $buf .= "ID<select name='removecode'>\n";
  $buf .= "  <option value=0>select remove logname\n";

  foreach($trapnames as $key => $value){
    $buf .= "  <option value=$value>$value\n";
  }
  $buf .= "</select>\n";

  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"remove\" OnClick=\"return confirm('Remove confirm?')\"; >remove</button>\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"update\" OnClick=\"return confirm('Update confirm?')\"; >update</button>\n";
  $buf .= "<button class=\"submit_button\"  name=\"type\" value=\"insert\" OnClick=\"return confirm('Insert confirm?')\"; >insert</button>\n";
  $buf .= "<input type=hidden name=funcs value=manage_traptype >\n";
  $buf .= "</form>\n";
  $buf .= "<BR>\n";
  $buf .= "</center>\n";

  return $buf;
}



########################################################
# export configurations 
########################################################
function manage_export_execute(){
  $export = "";
  $sql = "show tables like 'plugin_haruca%'";

  #$tables = mysql_query($sql) or die (mysql_error());
  #while($table = mysql_fetch_assoc($tables)){
  $tables = db_fetch_assoc($sql);
  foreach($tables as $table) {

    foreach($table as $table_key => $table_name){

      $check=0;
      if(isset($_POST['haruca'])){
        if(
            ($table_name !== "plugin_haruca_rtt")&&
            ($table_name !== "plugin_haruca_traplog")&&
            ($table_name !== "plugin_haruca_log")&&
            ($table_name !== "plugin_haruca_logold")
          ){$check++;}
      }

      if(isset($_POST['rtt'])){
        if($table_name === "plugin_haruca_rtt"){$check++;}
      }

      if(isset($_POST['traplog'])){
        if($table_name === "plugin_haruca_traplog"){$check++;}
      }

      if(isset($_POST['log'])){
        if($table_name === "plugin_haruca_log"){$check++;}
      }

      if(isset($_POST['logold'])){
        if($table_name === "plugin_haruca_logold"){$check++;}
      }

      if($check === 0){continue;}

      $sql = "desc $table_name";
      $fields = array();

      #$col_name = mysql_query($sql) or die (mysql_error());
      #while($cols = mysql_fetch_assoc($col_name)){

      $cols = db_fetch_assoc($sql);
      foreach($cols as $col) {
        $fields[] = $col['Field'];
      }

      if(($table_name === "plugin_haruca_log")||($table_name === "plugin_haruca_logold")){
        $sql = "select * from $table_name where logtypecode = 1 ";
      }else{
        $sql = "select * from $table_name ";
      }

      #$result = mysql_query($sql) or die (mysql_error());
      #while($row = mysql_fetch_assoc($result)){
      $rows = db_fetch_assoc($sql);
      foreach($rows as $row) {

        $export .= "{$table_name} set ";
        foreach($row as $key => $value){
          $export .= mysql_escape_string($key)."='".mysql_escape_string($value)."',";
        }
        $export = rtrim($export,",")."\n";
      }
    }
  }

  $filename = "export.txt";
  header("Content-type: application/octet-stream");
  header("Content-Disposition: attachment; filename=".$filename);
  header('Content-Length:' . strlen($export));

  if(0){
    header('Pragma: no-cache');
    header('Cache-Control: no-cache');
  }else{
    header('Pragma: private');
    header('Cache-control: private, must-revalidate');
  }

  print $export;
}


########################################################
# import haruca configurations
########################################################
function manage_import_execute(){

  if(is_uploaded_file($_FILES['importfile']['tmp_name'])){

    $results = file("{$_FILES['importfile']['tmp_name']}");

    foreach($results as $key => $value){
      $tables[] =  preg_replace("/\ .*/","",$value);
    }
    $tables = array_unique($tables);

    foreach($tables as $key => $value){
      if(empty($value)){break;}
      $sql = "delete from $value ";
      #print $sql."<BR>\n";
      #mysql_query($sql) or die (mysql_error());
      db_execute($sql);
    }
    
    foreach($results as $key => $value){
      if(empty($value)){break;}
      $sql = "insert $value ";
      #print $sql."<BR>\n";
      #mysql_query($sql) or die (mysql_error());
      db_execute($sql);
    }

    return true;

  }else{
    return false;
  }

}


########################################################
# user functions
########################################################
function show_logs_execute(){
  global $binpath;
  global $tmppath;


  if(isset($_REQUEST['date']) && isset($_REQUEST['host']) && isset($_REQUEST['type'])){
    if(isset($_REQUEST['diff'])){

      $cmd = "{$binpath}show ".$_REQUEST['type']." ".$_REQUEST['host']." 2000 | grep ^[0-9] | sed \"s/[-:\ ]//g\"";
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

        $file_target = "{$tmppath}target_".$_REQUEST['type'];
        $file_prev   = "{$tmppath}prev_".$_REQUEST['type'];
        system("{$binpath}show ".$_REQUEST['type']." ".$_REQUEST['host']." $target_date   $ignore_str  > $file_target");
        system("{$binpath}show ".$_REQUEST['type']." ".$_REQUEST['host']." $prev_date   $ignore_str  > $file_prev");
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
        $result = `{$binpath}router {$_REQUEST['host']} $cmd `;
      }else{
        $cmd = "{$binpath}show ".$_REQUEST['type']." ".$_REQUEST['host']." ".$_REQUEST['date'];
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


########################################################
# user functions
########################################################
function local_quote($string){
  if(empty($string)){
    $string = "NULL";
  }else{
    $string = '"'.  $string . '"';
  }
  return $string;
}


function print_page(){


  $title = func_get_arg(0);
  $function = func_get_arg(1);

  print "<center>\n";
  print "	<H3>".func_get_arg(0)."</H3>\n";
  print "	<HR>\n";
  print "</center>\n";
  print "<table width=100% >\n";

  print "	<tr>\n";
  print "		<td>". call_user_func(func_get_arg(1)) . "</td>\n";
  print "	</tr>\n";


  if(func_num_args() === 3){
    print "	<tr>\n";
    print "		<td>". call_user_func(func_get_arg(2)) . "</td>\n";
    print "	</tr>\n";
  }
  print "</table>\n";

}

function haruca_send_mail($to,$subject,$body,$from_name,$from_address,$server){
  //各種設定

  $header  = "";

  if(empty($from_name)){
    $header .= "From: $from_address\n";
  }else{
    $header .= "From: $from_name <{$from_address}>\n";
  }
  $header .= "To: $to\n";
  $header .= "Subject: $subject\n";
  $header .= "Content-Transfer-Encoding: 7bit\n";
  $header .= "Content-Type: text/plain;\n\n";
  $res = "";

  $sock = fsockopen($server,25);
  fputs($sock,"EHLO $server\n"); //SMTPコマンド発行
  fputs($sock,"MAIL FROM: $from_address\n"); //FROMアドレス指定
  fputs($sock,"RCPT TO: $to\n"); //宛先指定
  fputs($sock,"DATA\n"); //DATAを送信後、ピリオドオンリーの行を送るまで本文。
  fputs($sock,"$header"); //Subjectヘッダ送信
  fputs($sock,"$body\n"); //本文送信
  fputs($sock,"\n.\n"); //ピリオドのみの行を送信。
  $result = fgets($sock);

  if(preg_match("/^220/",$result)){
    $res = "success";
  }else{
    $res = "fail";
  }
  fclose($sock); //ソケット閉じる

  return $res;
}

?>

