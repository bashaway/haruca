<?php

include_once("../../include/auth.php");
include_once("../../include/config.php");
include_once("./haruca_functions.php");

# ファイルダウンロードする
if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "export")){
  manage_export_execute();
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
    case "manage_reset_config":
      manage_reset_config();
      break;
    case "manage_config":
      manage_config();
      break;
    case "manage_export":
      manage_export();
      break;

    default:
      print $ret['msg'];
      break;
}

haruca_footer();
html_end_box();
bottom_footer();



############################3
# management hosts
############################3
function manage_host(){
  global $config_haruca;
  foreach($_REQUEST as $key => $value){
    if($value === "get"){
      $cmd = "{$config_haruca['perlpath']}  {$config_haruca['binpath']}/sys_get_cisco_info {$key}";
      #print "GET info : $cmd\n";
      `{$config_haruca['perlpath']}  {$config_haruca['binpath']}/sys_get_cisco_info {$key}`;
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
        #print "{$config_haruca['perlpath']} {$config_haruca['binpath']}/sys_regist_host.pl --id=".$key.$value."<BR>\n";
        exec("{$config_haruca['perlpath']} {$config_haruca['binpath']}/sys_regist_host.pl --id=".$key.$value);
      }
    }
  }elseif(isset($_REQUEST['type']) && ($_REQUEST['type'] === "AllGet")){
      `{$config_haruca['perlpath']}  {$config_haruca['binpath']}/sys_get_cisco_info --all`;
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

  $buf .= "<form method=post action=./haruca_manage.php>\n";

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

  $buf .= "<input type=hidden name=action value=manage_office >\n";
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
  $buf .= "<form method=post action=./haruca_manage.php>\n";
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

  $buf .= "<input type=hidden name=action value=manage_config>\n";
  $buf .= "<input type=hidden name=type value=update>\n";
  $buf .= "</table>\n";
  #$buf .= "<input type=submit value=OK OnClick=\"return confirm('Update Configurations ?')\";>\n";
  $buf .= "<button class=\"submit_button\"  value=\"OK\" OnClick=\"return confirm('Update Configurations?')\"; >OK</button>\n";
  $buf .= "<input type=reset  value=RESET>\n";
  $buf .= "</form>\n";

  $buf .= "<BR><BR>\n";

  $buf .= "<form method=post action=./haruca_manage.php>\n";
  $buf .= "<input type=hidden name=type value=testmail>\n";
  $buf .= "<input type=hidden name=action value=manage_config>\n";
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
  $buf .= "<form method=post name=export action=./haruca_manage.php>\n";

  $buf .= "<table>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=haruca checked>haruca configuretion</td>";
  $buf .= "<td rowspan=3><button class=\"submit_button\" value=\"export\" >export</button></td></tr>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=rtt>rtt</td></tr>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=traplog>trap</td></tr>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=log>latest config</td></tr>\n";
  $buf .= "  <tr><td align=left><input type=checkbox name=logold>old config</td></tr>\n";
  $buf .= "</table>\n";

  $buf .= "<input type=hidden name=action value=manage_export >\n";
  $buf .= "<input type=hidden name=type value=export>\n";
  $buf .= "</form>\n";
  $buf .= "<br><BR>\n";

  $buf .= "import haruca configurations from file";
  $buf .= "<form method=post name=import action=./haruca_manage.php enctype=\"multipart/form-data\">\n";
  $buf .= "<input type=hidden name=action value=manage_export >\n";
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
function manage_reset_config(){
  global $config_haruca;
  include_once($config_haruca['basepath'].'/setup.php');

  print_page("ResetConfiguration","manage_reset_config_main");
  if(isset($_REQUEST['type']) && ($_REQUEST['type'] === "execute")){
    plugin_haruca_drop_table();
    plugin_haruca_setup_table_new();
    print "<BR>\n";
    print "<center><H2>RESET HARUCA CONFIGURATIONS.</H2></center>\n";
  }
}

########################################################
# RESET CONFIGURATION FUNCTION
########################################################
function manage_reset_config_main(){
  $buf = "";

  $buf .= "<center>\n";
  $buf .= "Please push the lower button to reset configuration.<BR><BR>\n";
  $buf .= "<form method=post action=./haruca_manage.php>\n";
  $buf .= "<input type=hidden name=action value=manage_reset_config>\n";
  $buf .= "<input type=hidden name=type value=execute>\n";
  $buf .= "<input type=submit value=OK OnClick=\"return confirm('Execute Reset Config ?')\";>\n";
  $buf .= "</form>\n";
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
  $buf .= "<form method=post action=./haruca_manage.php>\n";
  $buf .= "\n";
  $buf .= "<table cellspacing=0 cellpadding=0 border=1>\n";
  $buf .= "<tr>\n";
  $buf .= " <th width=50><a href=./haruca_manage.php?action=manage_host&sort=id>ID</a></th>\n";
  $buf .= " <th><a href=./haruca_manage.php?action=manage_host&sort=hostname>hostname</a></th>\n";
  $buf .= " <th><a href=./haruca_manage.php?action=manage_host&sort=address>Address</a></th>\n";
  $buf .= " <th><a href=./haruca_manage.php?action=manage_host&sort=category>Category</a></th>\n";
  $buf .= " <th><a href=./haruca_manage.php?action=manage_host&sort=office>Office</a></th>\n";
  $buf .= " <th><a href=./haruca_manage.php?action=manage_host&sort=model>Model</a></th>\n";
  $buf .= " <th><a href=./haruca_manage.php?action=manage_host&sort=version>Version</a></th>\n";
  $buf .= " <th><a href=./haruca_manage.php?action=manage_host&sort=serial>Serial</a></th>\n";
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
  $buf .= "<input type=hidden name=action value=manage_host >\n";
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
  $buf .= "<form method=post name=manage_category action=./haruca_manage.php>\n";
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
  $buf .= "<input type=hidden name=action value=manage_category >\n";
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
  $buf .= "<form method=post action=./haruca_manage.php>\n";

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

  $buf .= "<input type=hidden name=action value=manage_logtype >\n";
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
  $buf .= "<form method=post action=./haruca_manage.php>\n";
  
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
  $buf .= "<input type=hidden name=action value=manage_traptype >\n";
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
          #$export .= $key."=".db_qstr($value) .",";
          $export .= $key."=".local_quote($value) .",";
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


