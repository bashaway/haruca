<?php

include_once("../../include/auth.php");
include_once("../../include/config.php");
include_once("./haruca_functions.php");

# ファイルダウンロードする
if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "loggetter_execute")){
  tool_loggetter_execute();
  exit;
}


if( isset($_REQUEST['action']) && (($_REQUEST['action'] !== "tool_configchanger_execute"))){
  foreach($_REQUEST as $key => $value){
    $_REQUEST[$key] = preg_replace('/;.*/',"",$_REQUEST[$key]);
    $_REQUEST[$key] = preg_replace('/<.*/',"",$_REQUEST[$key]);
    $_REQUEST[$key] = preg_replace('/>.*/',"",$_REQUEST[$key]);
  }
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
    case "tool_loggetter":
      tool_loggetter();
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
    case "tool_bwcalc":
      tool_bwcalc();
      break;
    case "tool_wcmcalc":
      tool_wcmcalc();
      break;
  default:
    print $ret['msg'];
    break;




}

haruca_footer();
html_end_box();
bottom_footer();


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
  $buf .= "      <form method=post name=hostname action=./haruca_tool.php>\n";
  $buf .= "        <input type=hidden name=action value=tool_configchanger >\n";
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
  $buf .= "      <form method=post name=password action=./haruca_tool.php>\n";
  $buf .= "        <input type=hidden name=action value=tool_configchanger >\n";
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
  global $config_haruca;
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

    $list_hostname  = explode("\n",`${config_haruca['binpath']}search {$_POST['hostname']} $cond_category -h | sed s/\ \ *//g`);
    $list_ipaddress = explode("\n",`${config_haruca['binpath']}ipsearch {$_POST['ipaddress']} -h| sed s/\ \ *//g`);

    $list = array_filter(array_intersect($list_hostname,$list_ipaddress));


    ## hostlist が空の場合はエラー表示
    if(count($list) === 0){
      $buf .= "<center>\n";
      $buf .= "no match.<BR>\n";
      $buf .= "</center>\n";
      return $buf;
    }

    
    $buf .= "<center>\n";
    $buf .= "<form method=post name=config action=./haruca_tool.php>\n";
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

    $buf .= "<input type=hidden name=action value=tool_configchanger_execute>\n";
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
      $buf .=  "<form method=post action=./haruca_tool.php>\n";
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
      $buf .=  "<input type=hidden name=action value=tool_configchanger_execute>\n";
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
  global $config_haruca;

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

      $cmd = "{$config_haruca['perlpath']} {$config_haruca['binpath']}router ".$hostname." --config " . "\"".$cmd . "\"";
      #print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      #print " -> $cmd";

      $result = `$cmd`;
      $cmd = preg_replace("/.+--config/","",$cmd);

      if(preg_match("/error/",$result)){
        printf("%4d / %4d  %s : ping error skip ",$cnt,$hostnumber,$hostname);
      }else{
        printf("%4d / %4d  %s : change successful ",$cnt,$hostnumber,$hostname);
      }


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
  global $config_haruca;

  if(empty($_POST['keyword'])){
     $keyword = "";
  }else{
     $keyword = $_POST['keyword'];
  }

  $header = "";
	$header .= "<center>\n";
	$header .= "<form method=post name=hostname action=./haruca_tool.php>\n";
	$header .= "command &nbsp;<input name=keyword type=text value=\"{$keyword}\">\n";
	$header .= "<input type=hidden name=action value=tool_shell >\n";
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
          $buf .= $config_haruca['binpath'].$one . " ";
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
  $buf .= "      <form method=post name=hostname action=./haruca_tool.php>\n";
  $buf .= "        <input type=hidden name=action value=tool_loggetter >\n";
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
  global $config_haruca;
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

  $list_hostname  = explode("\n",`${config_haruca['binpath']}search {$_POST['hostname']} $cond_category -h | sed s/\ \ *//g`);
  $list_ipaddress = explode("\n",`${config_haruca['binpath']}ipsearch {$_POST['ipaddress']} -h| sed s/\ \ *//g`);

  $list = array_filter(array_intersect($list_hostname,$list_ipaddress));


  ## hostlist が空の場合はエラー表示
  if(count($list) === 0){
    $buf .= "<center>\n";
    $buf .= "no match.<BR>\n";
    $buf .= "</center>\n";
    return $buf;
  }

  
  $buf .= "<center>\n";
  $buf .= "<form method=post name=loggetter action=./haruca_tool.php>\n";
  $buf .= "select hosts<BR>\n";

  ## hostlist のtableヘッダ表示
  $buf .= "<table border=1>\n";
  $buf .= "  <tr>\n";
  $buf .= "    <th>hostname</th>\n";
  $buf .= "    <th>address</th>\n";
  $buf .= "    <th>category</th>\n";
  #$buf .= "    <th>office</th>\n";
  $buf .= "    <th>model</th>\n";
  $buf .= "    <th>version</th>\n";
  #$buf .= "    <th>serial</th>\n";
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

  
    # ホストの詳細を表示
    $buf .= "  <tr>\n";
    $buf .= "    <td>{$hostname}</td>\n";
    $buf .= "    <td>{$rows['ipaddress']}</td>\n";
    $buf .= "    <td>{$rows['categoryname']}</td>\n";
    #$buf .= "    <td>{$rows['officename']}</td>\n";
    $buf .= "    <td>{$rows['model']}</td>\n";
    $buf .= "    <td>{$rows['version']}</td>\n";
    #$buf .= "    <td>{$rows['serial']}</td>\n";
    if(empty($rows['vtypass'])){
      $buf .= "    <td align=center>VTY not set</td>\n";
    }else{
      $buf .= "    <td align=center><input type=checkbox name={$hostname_js}></td>\n";
    }
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
  $buf .= "    <td colspan=5>&nbsp;</td>\n";
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
  global $config_haruca;
    $f = "/usr/share/cacti/plugins/haruca/tmp/check";

  
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

    $filename = $config_haruca['tmppath'].$hostname."_".rtrim(`date '+%Y%m%d-%H%M%S'`,"\n").".txt";
    $filenames[] = $filename;
    $result = `{$config_haruca['perlpath']} {$config_haruca['binpath']}router $hostname $cmd`;
    $result = str_replace("\n","\r\n",$result);
    file_put_contents($filename,$result);

  }

  # zip routine
  $filename_zip = $config_haruca['tmppath']."autolog.zip";
  $cmd = "zip -j $filename_zip ";
  foreach($filenames as $key => $filename){
    $cmd .= $filename . " ";
  }
  `$cmd`;

    $fp = fopen($f,"w");
    fwrite($fp,$cmd);
    fclose($fp);



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
  global $config_haruca;

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

  $buf .= "<form method=post name=wcmcalc action=./haruca_tool.php>\n";
  $buf .= "<center>\n";
  $buf .= "<input name=address1 type=text size=15 value=\"$address1\" >\n";
  $buf .= "〜\n";
  $buf .= "<input name=address2 type=text size=15 value=\"$address2\" >\n";
  $buf .= "<BR>\n";
  $buf .= "<BR>\n";
  $buf .= "<input type=hidden name=action value=tool_wcmcalc>\n";
  $buf .= "<input type=submit value=\"OK\">\n";
  $buf .= "<input type=button  value=\"CLEAR\" onClick=clearText();>\n";
  $buf .= "</form>\n";
  $buf .= "<BR>\n\n";
  $buf .= "<PRE>\n";

  if(empty($address1) || empty($address2)){
    $buf .= "input address \n";
  }else{
    $result = `{$config_haruca['binpath']}wildcard_calc $address1 $address2`;
    $buf .= $result."\n";
  }
  $buf .= "</PRE>\n";
  $buf .= "</center>\n";
  $buf .= "IPv4 Calculator <BR>\n";
  $buf .= "Copyright (C) Krischan Jodies 2000 - 2004 <BR>\n";
  $buf .= "</center>\n";

  return $buf;

}

