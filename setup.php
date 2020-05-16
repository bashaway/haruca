<?php

function plugin_haruca_install($upgrade = 0) {
  global $config;
  include_once($config["base_path"] . "/plugins/haruca/haruca_functions.php");

  $str_error = "Please read README.md (installation chapter) and install perl modules.";
    if( exec("perl {$config["base_path"]}/plugins/haruca/bin/pmcheck.pl ") == "OK" ){
    } else {
      raise_message('rrdcalendar_info', __($str_error, 'rrdcalendar'), MESSAGE_LEVEL_ERROR);
      header('Location:' . $config['url_path'] . 'plugins.php?header=false');
    exit;
  }



   $plugin = 'haruca';

   api_plugin_register_hook($plugin, 'page_head',             'haruca_page_head',            'setup.php');
   api_plugin_register_hook($plugin, 'top_header_tabs',       'haruca_show_tab',             "setup.php");
   api_plugin_register_hook($plugin, 'top_graph_header_tabs', 'haruca_show_tab',             "setup.php");

   api_plugin_register_hook($plugin, 'config_arrays',         'haruca_config_arrays',        "setup.php");
   api_plugin_register_hook($plugin, 'draw_navigation_text',  'haruca_draw_navigation_text', "setup.php");
   api_plugin_register_hook($plugin, 'config_insert',         'haruca_config_insert',        'setup.php');

   api_plugin_register_hook($plugin, 'config_settings',       'haruca_config_settings',      "setup.php");
   api_plugin_register_hook($plugin, 'config_form',           'haruca_config_form',          "setup.php");

   api_plugin_register_realm($plugin, 'haruca_show.php,haruca_tool.php,haruca_manual.php', 'Plugin -> haruca viewer', 1);
   api_plugin_register_realm($plugin, 'haruca_manage.php', 'Plugin -> haruca configure', 1);

   plugin_haruca_setup_table_new ();

   plugin_haruca_setup_dbinfo();

}

function haruca_config_settings ($force = false) {
  global $config , $tabs, $settings,$haruca_fontsize;

  /* check for an upgrade */
  plugin_haruca_check_config();


        if ($force === false && isset($_SERVER['PHP_SELF']) &&
                basename($_SERVER['PHP_SELF']) != 'settings.php' &&
                basename($_SERVER['PHP_SELF']) != 'auth_profile.php')
                return;

        $tabs['haruca'] = __('Haruca', 'haruca');

        $treeList = array_rekey(get_allowed_trees(), 'id', 'name');
        $tempHeader = array('haruca_header' => array(
                        'friendly_name' => __('Haruca General', 'haruca'),
                        'method' => 'spacer',
                        ));
        $temp = array(
                'haruca_legend' => array(
                        'friendly_name' => __('Display Legend', 'haruca'),
                        'description' => __('Check this to display legend.', 'haruca'),
                        'method' => 'checkbox',
                        'default' => ''
                        ),

                'haruca_fontsize' => array(
                        'friendly_name' => __('Fontsize', 'haruca'),
                        'description' => __('Select graph scale by fontsize.', 'haruca'),
                        'method' => 'drop_array',
                        'default' => '10',
                        'array' => $haruca_fontsize
                        ),

                'haruca_path_setting' => array(
                        'friendly_name' => __('Path Options', 'haruca'),
                        'method' => 'spacer',
                        ),
                'haruca_path_rrdtool' => array(
                        'friendly_name' => __('RRDTool command path', 'haruca'),
                        'description' => __('input RRDTool command path ', 'haruca'),
                        'method' => 'filepath',
                        'filetype' => 'binary',
                        'default' => '/usr/bin/rrdtool',
                        'max_length' => 64,
                        ),
                'haruca_path_images' => array(
                        'friendly_name' => __('writable image directory ', 'haruca'),
                        'description' => __('generate images for this directory', 'haruca'),
                        'method' => 'dirpath',
                        'default' => $config['base_path'] .  '/plugins/haruca/images',
                        'max_length' => 64,
                        ),

                'haruca_cheader' => array(
                        'friendly_name' => __('Misc Options', 'haruca'),
                        'method' => 'spacer',
                        ),
                'haruca_custom_graph_title' => array(
                        'friendly_name' => __('Custom Title', 'haruca'),
                        'description' => __('Add Original Strings for Specified Graph Title.', 'haruca'),
                        'method' => 'textbox',
                        'max_length' => 255,
                        )
        );

        if (isset($settings['haruca'])) {
                $settings['haruca'] = array_merge($settings['haruca'], $tempHeader, $temp);
        }else {
                $settings['haruca'] = array_merge($tempHeader, $temp);
        }

        if (isset($settings_user['haruca'])) {
                $settings_user['haruca'] = array_merge($settings_user['haruca'], $temp);
        }else {
                $settings_user['haruca'] = $temp;
        }

}


function haruca_config_form() {
  global $tabs, $settings, $settings_user,$tabs_graphs;

  plugin_haruca_check_config();

  #$tabs['haruca'] = __('Haruca Config Manager', 'haruca');
  $tabs_graphs += array('haruca' => __('Haruca Settings', 'haruca'));

  $settings_user += array(
    'haruca' => array(
      'default_haruca_tab' => array(
        'friendly_name' => __('Default Tab', 'haruca'),
        'description' => __('Which Haruca tab would you want to be your Default tab every time you goto the Haruca second.', 'haruca'),
        'method' => 'drop_array',
        'default' => 'show',
        'array' => array(
          'show' => __('Haruca Show', 'haruca'),
          'tool' => __('Haruca Tool', 'haruca'),
          'manage' => __('Haruca Manage', 'haruca'),
          'manual' => __('haruca manual', 'haruca')
        )
      )
    )
  );
}



function haruca_show_tab () {

  global $config;

  #print "<!-- DEBUG : haruca_show_tab begin-->\n";
  if (api_user_realm_auth('haruca_show.php')) {
    $cp = false;
    if (get_current_page() == 'haruca_show.php' || get_current_page() == 'haruca_tool.php' || get_current_page() == 'haruca_manual.php'|| get_current_page() == 'haruca_manage.php') {
      $cp = true;
    }
    print "<a href=\"" . $config['url_path'] . 'plugins/haruca/haruca_show.php?action=show">';
    print '<img src="' . $config['url_path'] . 'plugins/haruca/images/tab_haruca' . ($cp ? '_down': '') . '.png" alt="haruca" align="absmiddle" border="0"></a>';
  }
  #print "<!-- DEBUG : haruca_show_tab end-->\n";




}


function haruca_page_head() {
}


function plugin_haruca_uninstall () {
  // Remove items from the settings table
  db_execute('DELETE FROM settings WHERE name LIKE "%haruca%"');
  db_execute('DELETE FROM settings_user WHERE name LIKE "%haruca%"');
  plugin_haruca_drop_table ();
}


function plugin_haruca_check_config () {
  /* Here we will check to ensure everything is configured */
  haruca_check_upgrade();
  return true;
}

function plugin_haruca_upgrade () {
  /* Here we will upgrade to the newest version */
  haruca_check_upgrade();
  return false;
}


function haruca_check_upgrade () {

	global $config;

	$files = array('index.php', 'plugins.php', 'haruca_show.php');
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

function plugin_haruca_drop_table () {
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_host`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_host`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_office`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_category`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_logtype`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_cat_get_log`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_log`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_logold`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_rtt`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_traplog`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_traptype`');
  db_execute('DROP TABLE IF EXISTS `plugin_haruca_settings`');

}

function plugin_haruca_setup_table_new () {
  # create table if not exists ....
  #db_execute("SET NAMES 'utf8'");

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
  global $haruca_fontsize;
  $haruca_fontsize = array(
    6 => __('Small  (%d pt)',  6, 'haruca'),
    8 => __('Medium (%d pt)',  8, 'haruca'),
   10 => __('Large  (%d pt)', 10, 'haruca')
  );

  return true;


}

function haruca_config_insert() {
  global $menu;

  $menu[__('Management')]['plugins/haruca/haruca_manage.php'] = __('Haruca Management', 'haruca');
  $menu[__('Utilities')]['plugins/haruca/haruca_manual.php'] = __('haruca manual', 'haruca');

}


function haruca_draw_navigation_text ($nav) {
   #print "<!-- DEBUG : haruca_draw_navigation_text begin-->\n";

   /* insert all your PHP functions that are accessible */

   $nav['haruca_show.php:']                      = array('title' => __('Haruca show'    , 'haruca'), 'mapping' => ''                , 'url' => 'haruca_show.php', 'level' => '0');
   $nav['haruca_show.php:show']                  = array('title' => __('Haruca show'    , 'haruca'), 'mapping' => ''                , 'url' => 'haruca_show.php', 'level' => '0');
   $nav['haruca_show.php:show_statuscheck']           = array('title' => __('StatusCheck'    , 'haruca'), 'mapping' => 'haruca_show.php:', 'url' => '', 'level' => '1');
   $nav['haruca_show.php:show_statuscheckold']        = array('title' => __('StatusCheckOld' , 'haruca'), 'mapping' => 'haruca_show.php:', 'url' => '', 'level' => '1');
   $nav['haruca_show.php:show_traps']                 = array('title' => __('Traps'          , 'haruca'), 'mapping' => 'haruca_show.php:', 'url' => '', 'level' => '1');
   $nav['haruca_show.php:show_logs']                  = array('title' => __('Logs'           , 'haruca'), 'mapping' => 'haruca_show.php:', 'url' => '', 'level' => '1');
   $nav['haruca_show.php:show_hosts']                 = array('title' => __('Hosts'          , 'haruca'), 'mapping' => 'haruca_show.php:', 'url' => '', 'level' => '1');

   $nav['haruca_tool.php:']                      = array('title' => __('Haruca tool'           , 'haruca'), 'mapping' => ''           , 'url' => 'haruca_tool.php', 'level' => '0');
   $nav['haruca_tool.php:tool']                  = array('title' => __('Haruca tool'           , 'haruca'), 'mapping' => ''           , 'url' => 'haruca_tool.php', 'level' => '0');
   $nav['haruca_tool.php:tool_loggetter']         = array('title' => __('LogGetter'         , 'haruca'), 'mapping' => 'haruca_tool.php:', 'url' => '', 'level' => '1');
   $nav['haruca_tool.php:tool_configchanger']         = array('title' => __('ConfigChanger'         , 'haruca'), 'mapping' => 'haruca_tool.php:', 'url' => '', 'level' => '1');
   $nav['haruca_tool.php:tool_bwcalc']                = array('title' => __('BandWidthCalculate'    , 'haruca'), 'mapping' => 'haruca_tool.php:', 'url' => '', 'level' => '1');
   $nav['haruca_tool.php:tool_configchanger_execute'] = array('title' => __('ConfigChangerExecute'  , 'haruca'), 'mapping' => 'haruca_tool.php:', 'url' => '', 'level' => '1');
   $nav['haruca_tool.php:tool_shell']                 = array('title' => __('EasyShell'             , 'haruca'), 'mapping' => 'haruca_tool.php:', 'url' => '', 'level' => '1');
   $nav['haruca_tool.php:tool_wcmcalc']               = array('title' => __('WildCardMaskCalc'      , 'haruca'), 'mapping' => 'haruca_tool.php:', 'url' => '', 'level' => '1');

   $nav['haruca_manage.php:']                    = array('title' => __('Haruca manage'  , 'haruca'), 'mapping' => '', 'url' => 'haruca_manage.php', 'level' => '0');
   $nav['haruca_manage.php:manage']              = array('title' => __('Haruca manage'  , 'haruca'), 'mapping' => '', 'url' => 'haruca_manage.php', 'level' => '0');
   $nav['haruca_manage.php:manage_config']              = array('title' => __('Config'         , 'haruca'), 'mapping' => 'haruca_manage.php:', 'url' => '', 'level' => '1');
   $nav['haruca_manage.php:manage_export']              = array('title' => __('Export'         , 'haruca'), 'mapping' => 'haruca_manage.php:', 'url' => '', 'level' => '1');
   $nav['haruca_manage.php:manage_host']                = array('title' => __('Host'           , 'haruca'), 'mapping' => 'haruca_manage.php:', 'url' => '', 'level' => '1');
   $nav['haruca_manage.php:manage_category']            = array('title' => __('Category'       , 'haruca'), 'mapping' => 'haruca_manage.php:', 'url' => '', 'level' => '1');
   $nav['haruca_manage.php:manage_office']              = array('title' => __('Office'         , 'haruca'), 'mapping' => 'haruca_manage.php:', 'url' => '', 'level' => '1');
   $nav['haruca_manage.php:manage_logtype']             = array('title' => __('LogType'        , 'haruca'), 'mapping' => 'haruca_manage.php:', 'url' => '', 'level' => '1');
   $nav['haruca_manage.php:manage_traptype']            = array('title' => __('TrapType'       , 'haruca'), 'mapping' => 'haruca_manage.php:', 'url' => '', 'level' => '1');
   $nav['haruca_manage.php:manage_reset_config']          = array('title' => __('ResetConfig'           , 'haruca'), 'mapping' => 'haruca_manage.php:', 'url' => '', 'level' => '1');

   $nav['haruca_manual.php:']                    = array('title' => __('Haruca manual'   , 'haruca'), 'mapping' => ''           , 'url' => 'haruca_manual.php', 'level' => '0');
   $nav['haruca_manual.php:manual']              = array('title' => __('Haruca manual'   , 'haruca'), 'mapping' => ''           , 'url' => 'haruca_manual.php', 'level' => '0');
   $nav['haruca_manual.php:manual_readme']               = array('title' => __('README.md'           , 'haruca'), 'mapping' => 'haruca_manual.php:', 'url' => '', 'level' => '1');
   $nav['haruca_manual.php:manual_command']             = array('title' => __('CLI command'     , 'haruca'), 'mapping' => 'haruca_manual.php:', 'url' => '', 'level' => '1');

   return $nav;

}


function   plugin_haruca_setup_dbinfo(){
  global $config;
  haruca_conf_to_file();
}

?>
