<?php

function haruca_tabs() {
  global $config;

  /* present a tabbed interface */
  if(api_user_realm_auth('haruca_config')) {
    $tabs = array(
      'show'    => __('show', 'haruca'),
      'tool'    => __('tool', 'haruca'),
      'manage'  => __('manage', 'haruca'),
      'manual'  => __('manual', 'haruca')
    );
  }else{
    $tabs = array(
      'show'    => __('show', 'haruca'),
      'tool'    => __('tool', 'haruca'),
      'manual'  => __('manual', 'haruca')
    );
  }

        get_filter_request_var('tab', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^([a-zA-Z]+)$/')));

        load_current_session_value('tab', 'sess_haruca_graph_tab', 'general');
        $current_tab = get_request_var('action');

        /* draw the tabs */
        print "<div class='tabs'><nav><ul>\n";

        if (sizeof($tabs)) {
                foreach (array_keys($tabs) as $tab_short_name) {
                        print "<li><a class='tab" . (($tab_short_name == $current_tab) ? " selected'" : "'") .
                                " href='" . htmlspecialchars($config['url_path'] .
                                'plugins/haruca/haruca.php?' .
                                'action=' . $tab_short_name) .
                                "'>" . $tabs[$tab_short_name] . "</a></li>\n";
                }
        }

        print "</ul></nav></div>\n";
}

function haruca_header() {
global $config, $menu, $user_menu;

$oper_mode = api_plugin_hook_function('top_header', OPER_MODE_NATIVE);
if ($oper_mode == OPER_MODE_RESKIN) {
        return;
}

$page_title = api_plugin_hook_function('page_title', draw_navigation_text('title'));
$using_guest_account = false;

?>
<!DOCTYPE html>
<html>
<head>
        <?php haruca_html_common_header($page_title);?>
</head>
<body>
<div id='cactiPageHead' class='cactiPageHead' role='banner'>
        <?php if ($oper_mode == OPER_MODE_NATIVE) { ;?>
        <div id='tabs'><?php html_show_tabs_left();?></div>
        <div class='cactiConsolePageHeadBackdrop'></div>
</div>
<div id='breadCrumbBar' class='breadCrumbBar'>
        <div id='navBar' class='navBar'><?php echo draw_navigation_text();?></div>
        <div class='scrollBar'></div>
        <?php if (read_config_option('auth_method') != 0) {?><div class='infoBar'><?php echo draw_login_status($using_guest_account);?></div><?php }?>
</div>
<div class='cactiShadow'></div>
<div id='cactiContent' class='cactiContent'>
        <?php if (isset($user_menu) && is_array($user_menu)) {?>
        <div style='display:none;' id='navigation' class='cactiConsoleNavigationArea'>
                <table style='width:100%;'>
                        <?php draw_menu($user_menu);?>
                        <tr>
                                <td style='text-align:center;'>
                                        <div class='cactiLogo' onclick='loadPage("<?php print $config['url_path'];?>about.php")'></div>
                                </td>
                        </tr>
                </table>
        </div>
        <div id='navigation_right' class='cactiConsoleContentArea'>
                <div style='position:relative;' id='main'>
        <?php } else { ?>
        <div id='navigation_right' class='cactiConsoleContentArea' style='margin-left:0px;'>
                <div style='position:relative;' id='main'>
        <?php } ?>
<?php } else { ?>
        <div id='navigation_right' class='cactiConsoleContentArea'>
                <div style='position:relative;' id='main' role='main'>
<?php } 

}

function haruca_html_common_header($title, $selectedTheme = '') {
?>

<meta http-equiv='X-UA-Compatible' content='IE=Edge,chrome=1'>
<meta content='width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0' name='viewport'>
<meta name='apple-mobile-web-app-capable' content='yes'>
<meta name='mobile-web-app-capable' content='yes'>
<meta name='robots' content='noindex,nofollow'>
<title>Console -> Haruca </title>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<script type='text/javascript'>var theme='modern';</script>
<link href='/cacti/include/themes/modern/images/favicon.ico' rel='shortcut icon'>
<link href='/cacti/include/themes/modern/images/cacti_logo.gif' rel='icon' sizes='96x96'>
<!-- 
<link href='/cacti/include/themes/modern/jquery.zoom.css?a1ae44c41387c6652e9cd9714ad643b5' type='text/css' rel='stylesheet'>
<link href='/cacti/include/themes/modern/jquery.multiselect.css?e8d1b3ce433249ef1f2f15343caa74fb' type='text/css' rel='stylesheet'>
<link href='/cacti/include/themes/modern/jquery.timepicker.css?431ab7d4ef48afd9c39a647c5c990b0a' type='text/css' rel='stylesheet'>
<link href='/cacti/include/themes/modern/jquery.colorpicker.css?fd70588fc7990c783ba6b2722371b9bd' type='text/css' rel='stylesheet'>
<link href='/cacti/include/themes/modern/jquery-ui.css?2cf675060576827318a6adf539998e06' type='text/css' rel='stylesheet'>
<link href='/cacti/include/themes/modern/default/style.css?b16e44c8ea2180ea769a22921bc369ad' type='text/css' rel='stylesheet'>
<link href='/cacti/include/themes/modern/c3.css?4aef467349628c8e407ecc205eac5375' type='text/css' rel='stylesheet'>
<link href='/cacti/include/themes/modern/pace.css?813d842ea49d68287a59d7f49575aaa6' type='text/css' rel='stylesheet'>
<link href='/cacti/include/fa/css/font-awesome.css?c495654869785bc3df60216616814ad1' type='text/css' rel='stylesheet'>
<link href='/cacti/include/themes/modern/main.css?da4e4e11cc3adf6a7e709c07350cecb1' type='text/css' rel='stylesheet'>
-->
<script type='text/javascript' src='/cacti/include/js/jquery.js?9c3a8d5bf79a2b2c25b4d9f99fbf6db2'></script>
<script type='text/javascript' src='/cacti/include/js/screenfull.js?f3fee4f35cb108c8187062e386985fe7'></script>

<script type='text/javascript' src='/cacti/include/js/jquery-migrate.js?3745a6e80cbf38ec3794302b4a47adc6'></script>
<script type='text/javascript' src='/cacti/include/js/jquery-ui.js?eac59f255a915c3c9cf56ba3286a0f0e'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.ui.touch.punch.js?4195aad6f616651c00557e84c6721646'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.cookie.js?0b804d4f90de70b032a9986b22165b75'></script>
<script type='text/javascript' src='/cacti/include/js/js.storage.js?dbcd4e6ad90c47adfa9dd509ceb55eb9'></script>
<script type='text/javascript' src='/cacti/include/js/jstree.js?b371a59bea924b430bb6b60904fbaac8'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.hotkeys.js?fbf82bcab286e9fc5cdf863eb067230f'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.tablednd.js?a33b14ebf8ce2abf7911e62cbc19e0c5'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.zoom.js?ee00764cc055d3eda4ee45b183ace84d'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.multiselect.js?d4188f31d19285683731418f0f8854e8'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.multiselect.filter.js?eb99fd8e2b0736c839c1b0a736af21c0'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.timepicker.js?f29132ab24085f909242175ad11cfcbc'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.colorpicker.js?4a5021ca49f95df1c61ba53873a1e70a'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.tablesorter.js?fd19ff8bfeaf5ac46158039bafb51db8'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.tablesorter.widgets.js?afa8ff74d0a737d7c0cb40301392966f'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.tablesorter.pager.js?a726022630463a531fdf87d5c327ff1b'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.metadata.js?bdd7532ce75cce796a5451bd4322d61f'></script>
<script type='text/javascript' src='/cacti/include/js/jquery.sparkline.js?c7638b825bc7deb1cf58c990825d35b2'></script>
<script type='text/javascript' src='/cacti/include/js/Chart.js?b3c4f8661a73c6997c7f0aad0583a9db'></script>
<script type='text/javascript' src='/cacti/include/js/dygraph-combined.js?b5b448f71f8c3eb4a39506299bd81b0c'></script>
<script type='text/javascript' src='/cacti/include/js/d3.js?fa31e071b81cbaf76bc3b63964b9dbbb'></script>
<script type='text/javascript' src='/cacti/include/js/c3.js?55bae45a4ac8b133b4ebc5667b341c15'></script>
<script type='text/javascript' src='/cacti/include/js/pace.js?30fbf6c62d78d3367fa50bd51913200c'></script>
<script type='text/javascript' src='/cacti/include/realtime.js?796b0a256e3187fe51ab8c4576da43d6'></script>
<script type='text/javascript' src='/cacti/include/layout.js?c0ecb6432b97efba399145bb5f23b6fe'></script>
<script type='text/javascript' src='/cacti/include/themes/modern/main.js?9750806ae1aa971056ea5cd9400148a4'></script>
<link href='/cacti/plugins/thold/themes/modern/main.css' type='text/css' rel='stylesheet'>
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
        <script type="text/javascript">if (top != self) {top.location.href = self.location.href;}</script><script type="text/javascript">var csrfMagicToken = "sid:9747b5351c658ff6d89d4a7c79503d1815131bbf,1528527233";var csrfMagicName = "__csrf_magic";</script><script src="/cacti/include/csrf/csrf-magic.js" type="text/javascript"></script></head>


<?php

        api_plugin_hook('page_head');

}

function haruca_footer() {
       global $config, $no_session_write;

        include($config['base_path'] . '/include/global_session.php');

        if (!isset_request_var('header') || get_nfilter_request_var('header') == 'true') {
                include($config['base_path'] . '/include/bottom_footer.php');
        }

        kill_session_var('sess_field_values');

        debug_log_clear();

        if (array_search(get_current_page(), $no_session_write) === false) {
                session_write_close();
        }

        db_close();
}



function haruca_header_orig() {

global $config, $menu, $user_menu;

$oper_mode = api_plugin_hook_function('top_header', OPER_MODE_NATIVE);
if ($oper_mode == OPER_MODE_RESKIN) {
        return;
}

$page_title = api_plugin_hook_function('page_title', draw_navigation_text('title'));
$using_guest_account = false;

?>
<!DOCTYPE html>
<html>
<head>
        <?php html_common_header($page_title);?>
</head>
<body>
<div id='cactiPageHead' class='cactiPageHead' role='banner'>
        <?php if ($oper_mode == OPER_MODE_NATIVE) { ;?>
        <div id='tabs'><?php html_show_tabs_left();?></div>
        <div class='cactiConsolePageHeadBackdrop'></div>
</div>
<div id='breadCrumbBar' class='breadCrumbBar'>
        <div id='navBar' class='navBar'><?php echo draw_navigation_text();?></div>
        <div class='scrollBar'></div>
        <?php if (read_config_option('auth_method') != 0) {?><div class='infoBar'><?php echo draw_login_status($using_guest_account);?></div><?php }?>
</div>
<div class='cactiShadow'></div>
<div id='cactiContent' class='cactiContent'>
        <?php if (isset($user_menu) && is_array($user_menu)) {?>
        <div style='display:none;' id='navigation' class='cactiConsoleNavigationArea'>
                <table style='width:100%;'>
                        <?php draw_menu($user_menu);?>
                        <tr>
                                <td style='text-align:center;'>
                                        <div class='cactiLogo' onclick='loadPage("<?php print $config['url_path'];?>about.php")'></div>
                                </td>
                        </tr>
                </table>
        </div>
        <div id='navigation_right' class='cactiConsoleContentArea'>
                <div style='position:relative;' id='main'>
        <?php } else { ?>
        <div id='navigation_right' class='cactiConsoleContentArea' style='margin-left:0px;'>
                <div style='position:relative;' id='main'>
        <?php } ?>
<?php } else { ?>
        <div id='navigation_right' class='cactiConsoleContentArea'>
                <div style='position:relative;' id='main' role='main'>
<?php } 

}

function haruca_footer_orig() {
       global $config, $no_session_write;

        include($config['base_path'] . '/include/global_session.php');

        if (!isset_request_var('header') || get_nfilter_request_var('header') == 'true') {
                include($config['base_path'] . '/include/bottom_footer.php');
        }

        kill_session_var('sess_field_values');

        debug_log_clear();

        if (array_search(get_current_page(), $no_session_write) === false) {
                session_write_close();
        }

        db_close();
}


