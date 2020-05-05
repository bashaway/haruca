<?php

include_once("../../include/auth.php");
include_once("../../include/config.php");
include_once("./haruca_functions.php");

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
    case "manual_setup":
      manual_setup();
      break;
    case "manual_command":
      manual_command();
      break;
    default:
      print $ret['msg'];
      break;
} 

haruca_footer();
html_end_box();
bottom_footer();


########################################################
# manual setup
########################################################
function manual_setup(){
  global $config_haruca;
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
  $buf = file_get_contents($config_haruca['basepath']."/docs/setup");
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
  global $config_haruca;
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
  $buf = file_get_contents($config_haruca['basepath']."/docs/command");
  print $buf;
?>
                  </PRE>
                </td>
              </tr>
            </table>

<?php
}


