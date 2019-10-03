<?php

function plugin_haruca_install($upgrade = 0) {

   $plugin = 'haruca';
   api_plugin_register_hook($plugin, 'page_head',             'haruca_page_head',            'setup.php');
   api_plugin_register_hook($plugin, 'top_header_tabs',       'haruca_show_tab',             "setup.php");
   api_plugin_register_hook($plugin, 'top_graph_header_tabs', 'haruca_show_tab',             "setup.php");

   api_plugin_register_hook($plugin, 'config_arrays',         'haruca_config_arrays',        "setup.php");
   api_plugin_register_hook($plugin, 'draw_navigation_text',  'haruca_draw_navigation_text', "setup.php");
   /* api_plugin_register_hook($plugin, 'config_settings',       'haruca_config_settings',      "setup.php"); */

   /* api_plugin_register_hook($plugin, 'config_form',           'haruca_config_form',          "setup.php"); */
   /* api_plugin_register_hook($plugin, 'top_graph_refresh',     'haruca_top_graph_refresh',    "setup.php"); */

   api_plugin_register_realm($plugin, 'haruca.php', 'Plugin -> haruca viewer', 1);
   api_plugin_register_realm($plugin, 'haruca_config', 'Plugin -> haruca configure', 1);

   plugin_haruca_setup_table_new ();

}

function haruca_show_tab () {

  global $config;

  #print "<!-- DEBUG : haruca_show_tab begin-->\n";
  if (api_user_realm_auth('haruca.php')) {
    $cp = false;
    if (get_current_page() == 'haruca.php') {
      $cp = true;
    }
    print "<a href=\"" . $config['url_path'] . 'plugins/haruca/haruca.php">';
    print '<img src="' . $config['url_path'] . 'plugins/haruca/images/tab_haruca' . ($cp ? '_down': '') . '.png" alt="haruca" align="absmiddle" border="0"></a>';
  }
  #print "<!-- DEBUG : haruca_show_tab end-->\n";

}


function haruca_page_head() {
    global $config;

    print "<!-- DEBUG : haruca_page_head begin-->\n";
    if(0){
    if (file_exists($config['base_path'] . '/plugins/haruca/themes/' . get_selected_theme() . '/main.css')) {
        print "<link href='" . $config['url_path'] . 'plugins/haruca/themes/' . get_selected_theme() . "/main.css' type='text/css' rel='stylesheet'>\n";
    }

    ?>
    <script type='text/javascript'>
    $(function() {
        $(document).ajaxComplete(function() {
            $('.tholdVRule').unbind().click(function(event) {
                event.preventDefault();

                href = $(this).attr('href');
                href += '&header=false';

                $.get(href, function(data) {
                    $('#main').empty().hide();
                    $('div[class^="ui-"]').remove();
                    $('#main').html(data);
                    applySkin();
                });
            });
        });
    });
    </script>
    <?php
    }
    print "<!-- DEBUG : haruca_page_head end-->\n";
}


function plugin_haruca_uninstall () {
}

function plugin_haruca_check_config () {
	/* Here we will check to ensure everything is configured */
        plugin_haruca_upgrade();
	return true;
}

function plugin_haruca_upgrade () {

	global $config;

	$files = array('index.php', 'plugins.php', 'haruca.php');
        if (!in_array(get_current_page(), $files)) {
                return false;
        }

        /*
	$current = plugin_haruca_version();
	$current = $current['version'];
	$old     = db_fetch_row("SELECT version FROM plugin_config WHERE directory='haruca'");
        if ($current != $old) {
          plugin_haruca_install (1);
        }
        */
        return true;
}

function plugin_haruca_version () {
    global $config;
    $info = parse_ini_file($config['base_path'] . '/plugins/haruca/INFO', true);
    return $info['info'];
}

function plugin_haruca_check_dependencies() {
	return true;
}

function plugin_haruca_setup_table_new () {
  # create table if not exists ....
  db_execute("SET NAMES 'utf8'");


  $data = array();
  $data['primary'] = 'id';
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_host';
  $data['columns'][] = array('name' => 'id'           , 'type' => 'mediumint(8)' , 'unsigned' => true , 'NULL' => false );
  $data['columns'][] = array('name' => 'categorycode' , 'type' => 'mediumint(8)' , 'unsigned' => true , 'NULL' => true  , 'default' => NULL );
  $data['columns'][] = array('name' => 'officecode'   , 'type' => 'mediumint(8)' , 'unsigned' => true , 'NULL' => true  , 'default' => NULL );
  $data['columns'][] = array('name' => 'model'        , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL );
  $data['columns'][] = array('name' => 'version'      , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL );
  $data['columns'][] = array('name' => 'serial'       , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL );
  api_plugin_db_table_create ('haruca', 'plugin_haruca_host', $data);


  $data = array();
  $data['primary'] = 'officecode';
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_office';
  $data['columns'][] = array('name' => 'officecode'    , 'type' => 'mediumint(8)' , 'unsigned' => true , 'NULL' => false , 'auto_increment' => true);
  $data['columns'][] = array('name' => 'officename'    , 'type' => 'varchar(32)'                       , 'NULL' => false );
  $data['columns'][] = array('name' => 'officeaddress' , 'type' => 'varchar(128)'                      , 'NULL' => false );
  api_plugin_db_table_create ('haruca', 'plugin_haruca_office', $data);

  $data = array();
  $data['primary'] = 'categorycode';
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_category';
  $data['columns'][] = array('name' => 'categorycode' , 'type' => 'mediumint(8)' , 'unsigned' => true , 'NULL' => false                        , 'auto_increment' => true);
  $data['columns'][] = array('name' => 'categoryname' , 'type' => 'varchar(32)'                       , 'NULL' => false                                                  );
  $data['columns'][] = array('name' => 'login_method' , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL                              );
  $data['columns'][] = array('name' => 'prepare_cmd'  , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL                              );
  $data['columns'][] = array('name' => 'enable_cmd'   , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL                              );
  $data['columns'][] = array('name' => 'uid_prompt'   , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL                              );
  $data['columns'][] = array('name' => 'pwd_prompt'   , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL                              );
  $data['columns'][] = array('name' => 'en_prompt'    , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL                              );
  $data['columns'][] = array('name' => 'username'     , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL                              );
  $data['columns'][] = array('name' => 'vtypass'      , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL                              );
  $data['columns'][] = array('name' => 'enable'       , 'type' => 'varchar(32)'                       , 'NULL' => true  , 'default' => NULL                              );
  api_plugin_db_table_create ('haruca', 'plugin_haruca_category', $data);

  $data = array();
  $data['primary'] = 'logtypecode';
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_logtype';
  $data['columns'][] = array('name' => 'logtypecode' , 'type' => 'tinyint'      , 'unsigned' => true , 'NULL' => false                        , 'auto_increment' => true);
  $data['columns'][] = array('name' => 'logname'     , 'type' => 'varchar(16)'                       , 'NULL' => false                                                  );
  $data['columns'][] = array('name' => 'loggetcmd'   , 'type' => 'varchar(256)'                      , 'NULL' => false                                                  );
  $data['columns'][] = array('name' => 'ignore_str'  , 'type' => 'varchar(256)'                                                                                         );
  $data['columns'][] = array('name' => 'diffcheck'   , 'type' => 'tinyint'                           , 'NULL' => false                                                  );
  $data['columns'][] = array('name' => 'cycle'       , 'type' => 'tinyint'                           , 'NULL' => false                                                  );
  api_plugin_db_table_create ('haruca', 'plugin_haruca_logtype', $data);

  $data = array();
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_cat_get_log';
  $data['columns'][] = array('name' => 'categorycode' , 'type' => 'mediumint(8)' , 'unsigned' => true , 'NULL' => false                    );
  $data['columns'][] = array('name' => 'logtypecode'  , 'type' => 'tinyint'      , 'unsigned' => true , 'NULL' => false                    );
  $data['columns'][] = array('name' => 'available'    , 'type' => 'tinyint(1)'   , 'unsigned' => true , 'NULL' => false , 'default' => '1' );
  api_plugin_db_table_create ('haruca', 'plugin_haruca_cat_get_log', $data);

  $data = array();
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_log';
  $data['columns'][] = array('name' => 'hostcode'     , 'type' => 'smallint'  , 'unsigned' => true , 'NULL' => false );
  $data['columns'][] = array('name' => 'logtypecode'  , 'type' => 'tinyint'   , 'unsigned' => true , 'NULL' => false );
  $data['columns'][] = array('name' => 'gettime'      , 'type' => 'timestamp'                      , 'NULL' => false );
  $data['columns'][] = array('name' => 'value'        , 'type' => 'mediumtext'                     , 'NULL' => false );
  api_plugin_db_table_create ('haruca', 'plugin_haruca_log', $data);

  $data = array();
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_logold';
  $data['columns'][] = array('name' => 'hostcode'     , 'type' => 'smallint'  , 'unsigned' => true , 'NULL' => false );
  $data['columns'][] = array('name' => 'logtypecode'  , 'type' => 'tinyint'   , 'unsigned' => true , 'NULL' => false );
  $data['columns'][] = array('name' => 'gettime'      , 'type' => 'timestamp'                      , 'NULL' => false );
  $data['columns'][] = array('name' => 'value'        , 'type' => 'mediumblob'                     , 'NULL' => false );
  api_plugin_db_table_create ('haruca', 'plugin_haruca_logold', $data);

  $data = array();
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_rtt';
  $data['columns'][] = array('name' => 'hostcode'     , 'type' => 'smallint'  , 'unsigned' => true , 'NULL' => false , 'default' => '0');
  $data['columns'][] = array('name' => 'gettime'      , 'type' => 'timestamp'                      , 'NULL' => false                   );
  $data['columns'][] = array('name' => 'value'        , 'type' => 'smallint'                       , 'NULL' => false , 'default' => '0');
  api_plugin_db_table_create ('haruca', 'plugin_haruca_rtt', $data);

  $data = array();
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_traplog';
  $data['columns'][] = array('name' => 'hostcode'     , 'type' => 'smallint'     , 'unsigned' => true , 'NULL' => false , 'default' => '0');
  $data['columns'][] = array('name' => 'oidstring'    , 'type' => 'varchar(50)'                       , 'NULL' => false                   );
  $data['columns'][] = array('name' => 'gettime'      , 'type' => 'timestamp'                         , 'NULL' => false                   );
  $data['columns'][] = array('name' => 'target'       , 'type' => 'varchar(500)'                                        , 'default' => NULL);
  $data['columns'][] = array('name' => 'summary'      , 'type' => 'varchar(500)'                                        , 'default' => NULL);
  $data['columns'][] = array('name' => 'description'  , 'type' => 'varchar(50)'                                         , 'default' => NULL);
  $data['columns'][] = array('name' => 'address'      , 'type' => 'varchar(20)'                                         , 'default' => NULL);
  api_plugin_db_table_create ('haruca', 'plugin_haruca_traplog', $data);

  $data = array();
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_traptype';
  $data['columns'][] = array('name' => 'oidstring'   , 'type' => 'varchar(50)'                       , 'NULL' => false                   );
  $data['columns'][] = array('name' => 'trapname'    , 'type' => 'varchar(50)'                       , 'NULL' => false                   );
  $data['columns'][] = array('name' => 'target'      , 'type' => 'varchar(500)'                                        , 'default' => NULL);
  $data['columns'][] = array('name' => 'summary'     , 'type' => 'varchar(500)'                                        , 'default' => NULL);
  $data['columns'][] = array('name' => 'available'   , 'type' => 'tinyint(1)'   , 'unsigned' => true , 'NULL' => false , 'default' => '1' );
  $data['columns'][] = array('name' => 'alertmail'   , 'type' => 'tinyint(1)'   , 'unsigned' => true , 'NULL' => false , 'default' => '0' );
  api_plugin_db_table_create ('haruca', 'plugin_haruca_traptype', $data);

  $data = array();
  $data['type'] = 'MyISAM';
  $data['comment'] = 'haruca_settings';
  $data['columns'][] = array('name' => 'item'  , 'type' => 'varchar(100)'                       , 'NULL' => false                   );
  $data['columns'][] = array('name' => 'value' , 'type' => 'varchar(100)'                                        , 'default' => NULL);
  api_plugin_db_table_create ('haruca', 'plugin_haruca_settings', $data);


  db_execute("insert into plugin_haruca_logtype (logname,loggetcmd,ignore_str,diffcheck,cycle) values ('start', 'show start'     ,'Using..*out of..*bytes|^\\ *\\!',1,7)");
  db_execute("insert into plugin_haruca_logtype (logname,loggetcmd,ignore_str,diffcheck,cycle) values ('run',   'show run'       ,'Using..*out of..*bytes|^\\ *\\!|ntp clock-period|Current configuration',1,7)");
  db_execute("insert into plugin_haruca_logtype (logname,loggetcmd,diffcheck,cycle) values ('logging', 'show logging'     ,0,7)");
  db_execute("insert into plugin_haruca_logtype (logname,loggetcmd,diffcheck,cycle) values ('inter', 'show inter'     ,0,7)");
  db_execute("insert into plugin_haruca_logtype (logname,loggetcmd,diffcheck,cycle) values ('tech',  'show tech'      ,0,7)");

  db_execute("insert into plugin_haruca_traptype (oidstring,trapname) values ('pingfail',    'PingFail')");
  db_execute("insert into plugin_haruca_traptype (oidstring,trapname) values ('pingsuccess', 'PingSuccess')");
  db_execute("insert into plugin_haruca_traptype (oidstring,trapname,summary) values ('.1.3.6.1.6.3.1.1.5.1', 'ColdStart', '.1.3.6.1.4.1.9.2.1.2')");
  db_execute("insert into plugin_haruca_traptype (oidstring,trapname,summary) values ('.1.3.6.1.6.3.1.1.5.2', 'WarmStart', '.1.3.6.1.4.1.9.2.1.2')");
  db_execute("insert into plugin_haruca_traptype (oidstring,trapname,target,summary) values ('.1.3.6.1.6.3.1.1.5.3', 'LinkDown',  '.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.4.1.9.2.2.1.1.20')");
  db_execute("insert into plugin_haruca_traptype (oidstring,trapname,target,summary) values ('.1.3.6.1.6.3.1.1.5.4', 'LinkUp',    '.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.4.1.9.2.2.1.1.20')");
  db_execute("insert into plugin_haruca_traptype (oidstring,trapname,summary,available) values ('.1.3.6.1.4.1.9.0.1', 'TelnetLogin', '.1.3.6.1.4.1.9.2.6.1.1.1.\$IP.\$NUM.',0)");
  db_execute("insert into plugin_haruca_traptype (oidstring,trapname,available) values ('.1.3.6.1.4.1.9.9.43.2.0.1', 'ConfigChange', 0)");

  db_execute("insert into plugin_haruca_category (categoryname) values ('SNMPONLY')");
  db_execute("insert into plugin_haruca_category (categoryname,login_method,prepare_cmd,enable_cmd,pwd_prompt,en_prompt,vtypass,enable) values ('telnet_nouser','telnet','term len 0','enable','Password:','Password:','password','enable')");
  db_execute("insert into plugin_haruca_category (categoryname,login_method,prepare_cmd,uid_prompt,username,enable_cmd,pwd_prompt,en_prompt,vtypass,enable) values ('telnet_user','telnet','term len 0','Username:','user','enable','Password:','Password:','password','enable')");
  db_execute("insert into plugin_haruca_category (categoryname,login_method,prepare_cmd,uid_prompt,username,enable_cmd,pwd_prompt,en_prompt,vtypass,enable) values ('ssh_user','ssh','term len 0','Username:','user','enable','Password:','Password:','password','enable')");


  db_execute("insert into plugin_haruca_office (officename,officeaddress) values ('NODATA','NODATA')");


  db_execute("insert into plugin_haruca_settings (item,value) values ('lastupdate_period','7')");
  db_execute("insert into plugin_haruca_settings (item,value) values ('proc_per_thread','10')");
  db_execute("insert into plugin_haruca_settings (item,value) values ('alert_smtp_server','127.0.0.1')");
  db_execute("insert into plugin_haruca_settings (item,value) values ('alert_email_to_address','example@example.com')");
  db_execute("insert into plugin_haruca_settings (item,value) values ('alert_email_from_name','haruca alert')");
  db_execute("insert into plugin_haruca_settings (item,value) values ('alert_email_from_address','example@example.com')");
  db_execute("insert into plugin_haruca_settings (item,value) values ('alert_ipmsg_address','')");
  db_execute("insert into plugin_haruca_settings (item,value) values ('alert_ipmsg_port','2425')");
  db_execute("insert into plugin_haruca_settings (item,value) values ('reload_period','30')");


  $ids = db_fetch_assoc("select id from host");
  if(!empty($ids)){
    foreach($ids as $id) {
      db_execute("INSERT INTO plugin_haruca_host set id = ".$id['id']." , categorycode = 1 ,  officecode = 1");
    }
  }
}


function haruca_config_arrays () {
  global $menu, $config, $haruca_tab;
  #print "<!-- DEBUG : haruca_config_arrays begin-->\n";
  include_once($config["base_path"] . "/plugins/haruca/config.php");

  if(!$haruca_tab){
    $temp = $menu["Utilities"]['logout.php'];
    unset($menu["Utilities"]['logout.php']);
    $menu["Utilities"]['plugins/haruca/haruca.php'] = "haruca";
    $menu["Utilities"]['logout.php'] = $temp;
  }
  #print "<!-- DEBUG : haruca_config_arrays end-->\n";

}

function haruca_draw_navigation_text ($nav) {
   #print "<!-- DEBUG : haruca_draw_navigation_text begin-->\n";
   /* insert all your PHP functions that are accessible */
   /*
   $nav['haruca.php:'] = array('title' => 'haruca', 'mapping' => '', 'url' => 'haruca.php', 'level' => '0');

   $nav['haruca.php:show_statuscheck']           = array('title' => 'ShowStatusCheck', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:show_statuscheckold']        = array('title' => 'ShowStatusCheckOld', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:show_traps']                 = array('title' => 'ShowTraps', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:show_logs_execute']          = array('title' => 'ShowLogsExecute', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:show_logs']                  = array('title' => 'ShowLogs', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:show_hosts']                 = array('title' => 'ShowHosts', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');

   $nav['haruca.php:manage_config']              = array('title' => 'ManageConfig', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:manage_export']              = array('title' => 'ManageExport', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:manage_host']                = array('title' => 'ManageHost', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:manage_category']            = array('title' => 'ManageCategory', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:manage_office']              = array('title' => 'ManageOffice', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:manage_logtype']             = array('title' => 'ManageLogType', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:manage_traptype']            = array('title' => 'ManageTrapType', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');

   $nav['haruca.php:reset_config']               = array('title' => 'ResetConfig', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');

   $nav['haruca.php:tool_configchanger']         = array('title' => 'ConfigChanger', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:tool_bwcalc']                = array('title' => 'BandWidthCalculate', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:tool_configchanger_execute'] = array('title' => 'ConfigChangerExecute', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:tool_shell']                 = array('title' => 'EasyShell', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:tool_wcmcalc']               = array('title' => 'WildCardMaskCalculate', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');

   $nav['haruca.php:manual_setup']               = array('title' => 'Setup', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   $nav['haruca.php:manual_command']             = array('title' => 'CLI command', 'mapping' => 'haruca.php:', 'url' => 'haruca.php', 'level' => '1');
   return $nav;
   */

   $nav['haruca.php:'] = array('title' => __('HARUCA', 'haruca'), 'mapping' => '', 'url' => 'haruca.php', 'level' => '0');
   return $nav;
   #print "<!-- DEBUG : haruca_draw_navigation_text end-->\n";

}



?>
