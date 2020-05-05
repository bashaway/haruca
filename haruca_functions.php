<?php

  global $config;
  global $config_haruca;
  global $database_type, $database_default, $database_hostname;
  global $database_username, $database_password, $database_port, $database_ssl;

  $config_haruca['ping_timeout'] = 3;
  $config_haruca['pingpath1'] = "/bin/ping -c 1 -w ".$config_haruca['ping_timeout']." ";
  $config_haruca['pingpath2'] = "/bin/ping -c 2 -w ".$config_haruca['ping_timeout']." ";
  $config_haruca['sendmailpath'] = "/usr/sbin/sendmail";
  $config_haruca['delim'] = "<<>>";
  $config_haruca['delim_line'] = "-<->----------------------------<->-";
  $config_haruca['noinfo']             = "### no information ###";
  $config_haruca['ping_fail_str']      = "### ping error ###";
  $config_haruca['unknown_error_str']  = "### unknown error ###";
  $config_haruca['not_set_pass']       = "### not set password ###";
  $config_haruca['msg_error']          = "### error ###";
  $config_haruca['not_update_str']     = "### not update ###";

  $config_haruca['cactipath'] = $config['base_path'];
  $config_haruca['basepath'] = $config['base_path']."/plugins/haruca";
  $config_haruca['binpath'] = $config_haruca['basepath']."/bin/";
  $config_haruca['datpath'] = $config_haruca['basepath']."/dat/";
  $config_haruca['datoldpath'] = $config_haruca['basepath']."/dat/old/";
  $config_haruca['conffile'] = $config_haruca['basepath']."/bin/conffile";
  $config_haruca['tmppath'] = $config_haruca['basepath']."/tmp/haruca_";
  $config_haruca['logpath'] = $config_haruca['basepath']."/log/haruca.log";
  $config_haruca['perlpath'] = "/usr/bin/perl";

  $config_haruca['database_type'] =   $database_type;
  $config_haruca['database_default'] =   $database_default;
  $config_haruca['database_hostname'] =   $database_hostname;
  $config_haruca['database_username'] =   $database_username;
  $config_haruca['database_password'] =   $database_password;
  $config_haruca['database_port'] =   $database_port;

  $config_haruca['snmpwalkpath'] = read_config_option('path_snmpwalk');
  $config_haruca['snmpgetpath'] = read_config_option('path_snmpget');


# __('JpString','po_file')
# Now not preparing locale files...

global $tabs_all,$tabs_no_manage, $tabs_show,$tabs_tool,$tabs_manage,$tabs_manual;
$tabs_all = array(
  'show'    => __('Show', 'haruca'),
  'tool'    => __('Tool', 'haruca'),
  'manage'  => __('Manage', 'haruca'),
  'manual'  => __('Manual', 'haruca')
);

$tabs_no_manage = array(
  'show'    => __('Show', 'haruca'),
  'tool'    => __('Tool', 'haruca'),
  'manual'  => __('Manual', 'haruca')
);

$tabs_show = array(
  'show_statuscheck' => __('StatChk', 'haruca'),
  'show_statuscheckold' => __('StatChk(Old)', 'haruca'),
  'show_traps' => __('Traps', 'haruca'),
  'show_logs' => __('Logs', 'haruca'),
  'show_hosts' => __('Hosts', 'haruca'),
);


$tabs_tool = array(
  'tool_loggetter' => __('LogGetter', 'haruca'),
  'tool_configchanger' => __('ConfChanger', 'haruca'),
  'tool_bwcalc' => __('BWcalc', 'haruca'),
  #'tool_configchange_execute' => __('ConfChanger_Ex', 'haruca'),
  'tool_shell' => __('Shell', 'haruca'),
  'tool_wcmcalc' => __('WCmaskCalc', 'haruca')
);

$tabs_manage = array(
  'manage_config' => __('Config', 'haruca'),
  'manage_host' => __('Host', 'haruca'),
  'manage_category' => __('Category', 'haruca'),
  'manage_office' => __('Office', 'haruca'),
  'manage_logtype' => __('LogType', 'haruca'),
  'manage_traptype' => __('TrapType', 'haruca'),
  'manage_export' => __('Export', 'haruca'),
  'manage_reset_config' => __('ResetConfig', 'haruca')
);

$tabs_manual = array(
  'manual_setup' => __('SetUp', 'haruca'),
  'manual_command' => __('Cmd', 'haruca'),
);





function haruca_conf_to_file() {
  global $config_haruca;

  $buf = "";
  foreach ($config_haruca as $key => $value){
    $buf .= "$key = $value\n";
  }

  $file = fopen($config_haruca["conffile"],"w");
  fwrite($file,$buf);
  fclose($file);

}

function haruca_tabs() {
  global $config;
  global $tabs_all,$tabs_no_manage;
  global $tabs_show,$tabs_tool,$tabs_manage,$tabs_manual;


  /* First level tab */
  /* present a tabbed interface */
  $current_page = preg_replace("/haruca_(.+?)\.php/","$1",get_current_page());
  if(api_user_realm_auth('haruca_manage.php')) {
    $tabs = $tabs_all;
  }else{
    $tabs = $tabs_no_manage;
  }

  /* draw the tabs */
  print "<div class='tabs'><nav><ul>\n";

  if (sizeof($tabs)) {
    foreach (array_keys($tabs) as $keys => $tab_short_name ) {
      print "<li><a class='tab" . (($tab_short_name == $current_page) ? " selected'" : "'") ;
      print " href='" . htmlspecialchars($config['url_path'] .  'plugins/haruca/haruca_'.$tab_short_name.'.php?' .  'action=' . $tab_short_name) ;
      print "'>" . $tabs[$tab_short_name] . "</a></li>\n";
 
    }
  }
  print "</ul></nav></div>\n";
  print "<BR>\n";




  /* Second level tab */
  /* present a tabbed interface */
  switch($current_page){
    case 'show':
      $tabs = $tabs_show;
      break;
    case 'manage':
      $tabs = $tabs_manage;
      break;
    case 'tool':
      $tabs = $tabs_tool;
      break;
    case 'manual':
      $tabs = $tabs_manual;
      break;
  }

  $current_tab = get_request_var('action');

  print "<div class='tabs'><nav><ul>\n";
  if (sizeof($tabs)) {
    foreach (array_keys($tabs) as $keys => $tab_short_name ) {
        print "<li><a class='tab" . (($tab_short_name == $current_tab) ? " selected'" : "'") ;
        print " href='" . htmlspecialchars($config['url_path'] .  'plugins/haruca/haruca_'.$current_page.'.php?' .  'action=' . $tab_short_name) ;
      print "'>" . $tabs[$tab_short_name] . "</a></li>\n";
    }
  }

  print "</ul></nav></div>\n";

}


function page_default(){
  global $config_haruca;
  $buf = "";
  if(`{$config_haruca['binpath']}pmcheck.pl` != "OK"){
    $buf .= "<H1>SETUP FAILED</H1><BR>\n";
    $buf .= "##### SET symbolic link haruca.pm TO perl @INC DIRECTORY #####<BR>\n";
    $buf .= "[user@cacti haruca]$ perl -E 'say for @INC'<BR>\n";
    $buf .= "/usr/local/lib64/perl5<BR>\n";
    $buf .= "/usr/local/share/perl5<BR>\n";
    $buf .= "/usr/lib64/perl5/vendor_perl<BR>\n";
    $buf .= "/usr/share/perl5/vendor_perl<BR>\n";
    $buf .= "/usr/lib64/perl5<BR>\n";
    $buf .= "/usr/share/perl5<BR>\n";
    $buf .= "<BR>\n";
    $buf .= "[user@cacti haruca]$ sudo ln -s /usr/share/cacti/plugins/haruca/bin/haruca.pm /usr/lib64/perl5/<BR>\n";
    $buf .= "<BR>\n";
    $buf .= "##### INSTALL require modules #####<BR>\n";
    $buf .= "[user@cacti]$ sudo yum install perl-CPAN perl-YAML perl-DBI perl-DBD-MySQL perl-Net-Telnet perl-Net-SSH perl-Expect <BR>\n";
    $buf .= "[user@cacti]$ sudo perl -MCPAN -e 'install Test::More' <BR>\n";
    $buf .= "[user@cacti]$ sudo perl -MCPAN -e 'install Net::SSH::Expect' <BR>\n";
    $ret = array('status' => 'NG' , 'msg' => $buf );
  }else{
    $buf .= "<center>\n";
    $buf .= "  <H3>haruca</H3>\n";
    $buf .= "  Please select item from tabs.\n";
    $buf .= "</center>\n";
    $ret = array('status' => 'OK' , 'msg' => $buf );
  }

  return ($ret);
  # データベースに不整合があった場合に新規追加／削除する処理を追加したい


}

function local_quote($string){

  if(0){
  if(empty($string)){
    $string = "NULL";
  }else{
    $string = '"'.  $string . '"';
  }
  }else{
    $string = db_qstr($string);
  }

  return $string;
}

function print_page(){
  $title = func_get_arg(0);
  $function = func_get_arg(1);

  print "<center>\n";
  print "       <H3>".func_get_arg(0)."</H3>\n";
  print "       <HR>\n";
  print "</center>\n";
  print "<table width=100% >\n";

  print "       <tr>\n";
  print "               <td>". call_user_func(func_get_arg(1)) . "</td>\n";
  print "       </tr>\n";


  if(func_num_args() === 3){
    print "     <tr>\n";
    print "             <td>". call_user_func(func_get_arg(2)) . "</td>\n";
    print "     </tr>\n";
  }
  print "</table>\n";

}



function haruca_footer() {

print "<hr>\n";
print "Half Ability Router Utility and Config Archiver <BR>\n";
print "( " . db_fetch_cell("select now() as today") . " )<BR>\n";

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

