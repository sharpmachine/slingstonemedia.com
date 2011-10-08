<?php
class WafpAppController
{
  function WafpAppController()
  {
    global $wafp_update;
    
    add_filter('the_content', array( &$this, 'page_route' ), 100);
    add_action('wp_enqueue_scripts', array(&$this, 'load_scripts'), 1);
    add_action('wp_head', array(&$this,'load_dynamic_css'), 100);
    register_activation_hook(WAFP_PATH."/affiliate-royale.php", array( &$this, 'install' ));
    
    add_action('init', array(&$this,'parse_standalone_request'));
    add_action('admin_notices', array(&$this, 'upgrade_database_headline'));
    
    if( $wafp_update->pro_is_installed_and_authorized() )
      add_action('wp_dashboard_setup', array(&$this, 'add_dashboard_widgets'));
  }

  function setup_menus()
  {
    add_action('admin_menu', array( &$this, 'menu' ));
  }
  
  /********* INSTALL PLUGIN ***********/
  function install()
  {
    global $wafp_db, $wafp_update;
    
    $wafp_db->upgrade();
  }
  
  function menu()
  {
    global $wafp_options_controller,
           $wafp_links_controller,
           $wafp_integration_controller,
           $wafp_reports_controller,
           $wafp_users_controller,
           $wafp_update;

    if( $wafp_update->pro_is_installed_and_authorized() )
    {
      $wafp_main_menu_hook = add_menu_page(__('Affiliate Royale', 'affiliate-royale'), __('Affiliate Royale', 'affiliate-royale'), 'administrator', 'affiliate-royale-reports', array( &$wafp_reports_controller, 'overview'), WAFP_IMAGES_URL . "/affiliate_royale_logo_16.png");
      $wafp_reports_menu_hook = add_submenu_page( 'affiliate-royale-reports', __('Reports', 'affiliate-royale'), __('Reports', 'affiliate-royale'), 'administrator', 'affiliate-royale-reports', array( &$wafp_reports_controller, 'overview' ) );
      $wafp_affiliates_menu_hook = add_submenu_page( 'affiliate-royale-reports', __('Affiliates', 'affiliate-royale'), __('Affiliates', 'affiliate-royale'), 'administrator', 'affiliate-royale-affiliates', array( $wafp_users_controller, 'display_affiliates_list') );
      $wafp_links_menu_hook = add_submenu_page( 'affiliate-royale-reports', __('Links &amp; Banners', 'affiliate-royale'), __('Links &amp; Banners', 'affiliate-royale'), 'administrator', 'affiliate-royale-links', array( &$wafp_links_controller, 'route' ) );
      $wafp_options_menu_hook = add_submenu_page( 'affiliate-royale-reports', __('Options', 'affiliate-royale'), __('Options', 'affiliate-royale'), 'administrator', 'affiliate-royale-options', array( &$wafp_options_controller, 'route' ) );
      $wafp_activate_menu_hook = add_submenu_page( 'affiliate-royale-reports', __('Activate', 'affiliate-royale'), __('Activate', 'affiliate-royale'), 'administrator', 'affiliate-royale-activate', array( &$this, 'display_activation_form' ) );
      
      add_action('admin_print_scripts-'.$wafp_reports_menu_hook, array(&$this,'load_admin_scripts'));
      add_action('admin_print_scripts-'.$wafp_affiliates_menu_hook, array(&$this,'load_admin_scripts'));
      add_action('admin_print_scripts-'.$wafp_links_menu_hook, array(&$this,'load_admin_scripts'));
      add_action('admin_print_scripts-'.$wafp_options_menu_hook, array(&$this,'load_admin_scripts'));
      add_action('admin_print_scripts-'.$wafp_activate_menu_hook, array(&$this,'load_admin_scripts'));
      add_action('admin_print_scripts-users.php', array(&$this,'load_admin_scripts'));
      add_action('admin_print_scripts-user-edit.php', array(&$this,'load_admin_scripts'));
      add_action('admin_print_scripts-profile.php', array(&$this,'load_admin_scripts'));
      add_action('admin_print_scripts-index.php', array(&$this,'load_admin_scripts'));

      add_action('admin_print_styles-'.$wafp_reports_menu_hook, array(&$this,'load_admin_styles'));
      add_action('admin_print_styles-'.$wafp_affiliates_menu_hook, array(&$this,'load_admin_styles'));
      add_action('admin_print_styles-'.$wafp_links_menu_hook, array(&$this,'load_admin_styles'));
      add_action('admin_print_styles-'.$wafp_options_menu_hook, array(&$this,'load_admin_styles'));
      add_action('admin_print_styles-'.$wafp_activate_menu_hook, array(&$this,'load_admin_styles'));
      add_action('admin_print_styles-users.php', array(&$this,'load_admin_styles'));
      add_action('admin_print_styles-user-edit.php', array(&$this,'load_admin_styles'));
      add_action('admin_print_styles-profile.php', array(&$this,'load_admin_styles'));
      add_action('admin_print_styles-index.php', array(&$this,'load_admin_styles'));
    }
    else
    {
      $wafp_main_menu_hook = add_menu_page(__('Affiliate Royale', 'affiliate-royale'), __('Affiliate Royale', 'affiliate-royale'), 'administrator', 'affiliate-royale-activate', array( &$this, 'display_activation_form' ), WAFP_IMAGES_URL . "/affiliate_royale_logo_16.png");
    }
    add_action('admin_print_scripts-'.$wafp_main_menu_hook, array(&$this,'load_admin_scripts'));
    add_action('admin_print_styles-'.$wafp_main_menu_hook, array(&$this,'load_admin_styles'));
  }
  
  function display_activation_form()
  {
    global $wafp_update;
    require(WAFP_VIEWS_PATH . '/shared/activation_form.php');
  }

  // Routes for wordpress pages -- we're just replacing content here folks.
  function page_route($content)
  {
    global $wafp_current_user,
           $post, 
           $wafp_options, 
           $wafp_dashboard_controller,
           $wafp_users_controller;

    switch( $post->ID )
    {  
      case $wafp_options->dashboard_page_id:
        $action = $this->get_param('action','home');
        // Start output buffering -- we want to return the output as a string
        ob_start();
        
        if($wafp_current_user)
        {
          if (isset($_POST['become_affiliate_submit'])) // (see views/wafp-dashboard/become.php)
          {
            $wafp_current_user->set_is_affiliate(true);
            $wafp_current_user->store();
          }

          if ($wafp_current_user->get_is_affiliate())
          {
            if($action=='home' or empty($action))
              $wafp_dashboard_controller->display_dashboard();
            else if($action=='links')
              $wafp_dashboard_controller->display_links();
            else if($action=='stats')
              $wafp_dashboard_controller->display_stats();
            else if($action=='payments')
              $wafp_dashboard_controller->display_payments();
          }
          else
            $wafp_dashboard_controller->display_become_affiliate(); //Added by Paul (shows if not affiliate)
        }
        else
        {
          $loginURL = get_permalink($wafp_options->login_page_id);
          if (empty($loginURL))
            $loginURL = wp_login_url();
          require( WAFP_VIEWS_PATH . "/shared/unauthorized.php" );
        }

        // Pull all the output into this variable
        $content .= ob_get_contents();
        // End and erase the output buffer (so we control where it's output)
        ob_end_clean();
        break;
      case $wafp_options->login_page_id:
        ob_start();
        $action = $this->get_param('action');
      
        if( $action and $action == 'forgot_password' )
          $wafp_users_controller->display_forgot_password_form();
        else if( $action and $action == 'wafp_process_forgot_password' )
          $wafp_users_controller->process_forgot_password_form();
        else if( $action and $action == 'reset_password')
          $wafp_users_controller->display_reset_password_form($this->get_param('mkey'),$this->get_param('u'));
        else if( $action and $action == 'wafp_process_reset_password_form')
          $wafp_users_controller->process_reset_password_form();
        else
          $wafp_users_controller->display_login_form();
      
        $content .= ob_get_contents();
        ob_end_clean();
        break;
      case $wafp_options->signup_page_id:
        ob_start();
        $wafp_users_controller->display_signup_form();
        $content .= ob_get_contents();
        ob_end_clean();
        break;
    }
    
    return $content;
  }  

  function load_scripts()
  {
    $this->enqueue_wafp_scripts();
  }
  
  function load_admin_styles()
  {
    wp_enqueue_style( 'datatable-page', WAFP_URL . '/css/datatable_page.css', array() );
    wp_enqueue_style( 'datatable-table-jui', WAFP_URL . '/css/datatable_table_jui.css', array('datatable-page') );
    wp_enqueue_style( 'jquery-ui', WAFP_URL . '/css/jquery-ui/jquery-ui-1.8.16.custom.css', array('datatable-table-jui'), '1.8.16');
    wp_enqueue_style( 'affiliate-royale',  WAFP_CSS_URL . '/affiliate-royale.css', array() );
  }

  function load_admin_scripts()
  {
    $this->enqueue_wafp_scripts();
  }
  
  function enqueue_wafp_scripts()
  {
    global $wafp_blogurl;
        
    wp_enqueue_style( 'affiliate-royale',  WAFP_CSS_URL . '/affiliate-royale.css', array() );
    wp_enqueue_script( 'excanvas', WAFP_JS_URL . '/excanvas.min.js', array() );
    wp_enqueue_script( 'jquery-flot', WAFP_JS_URL . '/jquery.flot.min.js', array('excanvas','jquery'), '0.7' );
    wp_enqueue_script( 'jquery-datatables', WAFP_JS_URL . '/jquery.datatables.js', array('jquery'), '1.7.6' );
    wp_enqueue_script( 'affiliate-royale', WAFP_JS_URL . '/affiliate-royale.js', array('jquery','jquery-flot','jquery-datatables') );
  }

  // The tight way to process standalone requests dogg...
  function parse_standalone_request()
  {
    global $wafp_users_controller;

    $plugin     = isset($_REQUEST['plugin'])?$_REQUEST['plugin']:'';
    $action     = isset($_REQUEST['action'])?$_REQUEST['action']:'';
    $controller = isset($_REQUEST['controller'])?$_REQUEST['controller']:'';

    $request_uri = $_SERVER['REQUEST_URI'];

    if( !empty($plugin) and $plugin == 'wafp' and 
        !empty($controller) and !empty($action) )
    {
      $this->standalone_route($controller, $action);
      exit;
    }
    else if( isset( $_POST ) and isset( $_POST['wafp_process_login_form'] ) )
      $wafp_users_controller->process_login_form();
  }

  // Routes for standalone / ajax requests
  function standalone_route($controller, $action)
  {
    global $wafp_links_controller, $wafp_transactions_controller, $wafp_reports_controller, $wafp_dashboard_controller;

    if($controller=='links')
    {
      if($action=='redirect')
        $wafp_links_controller->redirect_link( WafpAppController::get_param('l'), WafpAppController::get_param('a') );
      else if($action=='pixel')
        $wafp_links_controller->track_link( WafpAppController::get_param('a') );
      else if($action=='delete')
        $wafp_links_controller->delete_link( WafpAppController::get_param('lid') );
    }
    else if($controller=='transactions')
    {
      if($action=='track')
      {
        // Translate parameters for iDevAffiliate URL compatibility...
        if( isset($_REQUEST['idev_saleamt']) and !empty($_REQUEST['idev_saleamt']) )
          $_REQUEST['amount'] = $_REQUEST['idev_saleamt'];
        
        if( isset($_REQUEST['idev_ordernum']) and !empty($_REQUEST['idev_ordernum']) )
          $_REQUEST['order_id'] = $_REQUEST['idev_ordernum'];
          
        if( !isset($_REQUEST['order_id']) )
          $_REQUEST['order_id'] = uniqid();
        
        $wafp_transactions_controller->track( $_REQUEST['amount'],
                                              $_REQUEST['order_id'],
                                              $_REQUEST['prod_id'],
                                              $_REQUEST['aff_id'],
                                              $_REQUEST['subscr_id'] );
      }
    }
    else if( $controller=='reports' )
    {
      if($action=='admin_affiliate_stats')
        $wafp_reports_controller->admin_affiliate_stats( WafpAppController::get_param('period') );
      else if($action=='admin_affiliate_clicks')
        $wafp_reports_controller->admin_affiliate_clicks( WafpAppController::get_param('wafpage') );
      else if($action=='admin_affiliate_transactions')
        $wafp_reports_controller->admin_affiliate_transactions( WafpAppController::get_param('wafpage') );
      else if($action=='admin_affiliate_top')
        $wafp_reports_controller->admin_affiliate_top( WafpAppController::get_param('period'), WafpAppController::get_param('wafpage') );
      else if($action=='admin_affiliate_payments')
        $wafp_reports_controller->admin_affiliate_payments( WafpAppController::get_param('period') );
      else if($action=='paypal_bulk_file')
        $wafp_reports_controller->admin_paypal_bulk_file( WafpAppController::get_param('id') );
    }
    else if( $controller=='dashboard' )
    {
      if($action=='dashboard_affiliate_stats')
        $wafp_dashboard_controller->display_stats( WafpAppController::get_param('period'), false );
    }
    else if($controller=='admin')
    {
      if($action=='db_upgrade')
        $this->db_upgrade();
    }
    else
      do_action('wafp_process_route');
  }
  
  function load_language()
  {
    $path_from_plugins_folder = str_replace( ABSPATH, '', WAFP_PATH ) . '/i18n/';
    
    load_plugin_textdomain( 'affiliate-royale', false, $path_from_plugins_folder );
  }
  
  function load_dynamic_css()
  {
    global $wafp_options;
    ?>
      <style type="text/css">
      #wafp-dash-wrapper
      {
        color: #333;
        width: <?php echo $wafp_options->dash_css_width; ?>px;
      }
      #wafp-dash-wrapper img
      {
        max-width: <?php echo $wafp_options->dash_css_width; ?>px;
      }
      </style>
    <?php
  }

  // Utility function to grab the parameter whether it's a get or post
  function get_param($param, $default='')
  {
    if((!isset($_POST) or empty($_POST)) and
       (!isset($_GET) or empty($_GET)))
      return $default;

    if(!isset($_POST[$param]) and !isset($_GET[$param]))
      return $default;
  
    return (isset($_POST[$param])?$_POST[$param]:(isset($_GET[$param])?$_GET[$param]:$default));
  }
  
  function get_param_delimiter_char($link)
  { 
    return ((preg_match("#\?#",$link))?'&':'?');
  }
  
  /** Show the database upgrade notice if db is out of date */
  public function upgrade_database_headline()
  {
    global $wafp_blogurl, $wafp_update, $wafp_db_version;
  
    if($wafp_update->pro_is_installed_and_authorized())
    {
      $old_wafp_db_version = get_option('wafp_db_version');
      
      if( !$old_wafp_db_version or ( intval($old_wafp_db_version) < $wafp_db_version ) )
      {
         $db_upgrade_url = wp_nonce_url(WAFP_SCRIPT_URL . "&controller=admin&action=db_upgrade", "wafp-db-upgrade");
         ?>
         <div class="error" style="padding-top: 5px; padding-bottom: 5px;"><?php printf(__('You must upgrade your database for <b>Affiliate Royale</b> to work properly<br/>%1$sAutomatically Upgrade your Database%2$s', 'affiliate-royale'), "<a href=\"{$db_upgrade_url}\">",'</a>'); ?></div>
         <?php
      }
      else if( isset($_REQUEST['wafp_db_upgraded']) )
      {
        ?>
        <div class="updated" style="padding-top: 5px; padding-bottom: 5px;"><?php _e('<b>Affiliate Royale\'s</b> database was successfully upgraded.', 'affiliate-royale'); ?></div>
        <?php
      }
    }
  }

  /** Upgrade the db when out of date */
  public function db_upgrade()
  {
    global $wafp_blogurl, $wafp_update, $wafp_db_version;
  
    if( wp_verify_nonce( $_REQUEST['_wpnonce'], "wafp-db-upgrade" ) and current_user_can( 'update_core' ) )
    {
      if( $wafp_update->pro_is_installed_and_authorized())
      {
        $old_wafp_db_version = get_option('wafp_db_version');
        
        if( !$old_wafp_db_version or ( intval($old_wafp_db_version) < $wafp_db_version ) )
        {
          $this->install();
          wp_redirect("{$wafp_blogurl}/wp-admin/admin.php?page=affiliate-royale-reports&wafp_db_upgraded=true");
        }
        else
          WafpUtils::wp_redirect($wafp_blogurl);
      }
    }  
    else
      WafpUtils::wp_redirect($wafp_blogurl);
  }
  
  public function add_dashboard_widgets()
  {
    wp_add_dashboard_widget('ar_weekly_stats_widget', 'Affiliate Royale Weekly Stats', array(&$this,'weekly_stats_widget'));
    
    // Globalize the metaboxes array, this holds all the widgets for wp-admin

  	global $wp_meta_boxes;

  	// Get the regular dashboard widgets array 
  	// (which has our new widget already but at the end)

  	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

  	// Backup and delete our new dashbaord widget from the end of the array

  	$ar_weekly_stats_widget_backup = array('ar_weekly_stats_widget' => $normal_dashboard['ar_weekly_stats_widget']);
  	unset($normal_dashboard['ar_weekly_stats_widget']);

  	// Merge the two arrays together so our widget is at the beginning

  	$sorted_dashboard = array_merge($ar_weekly_stats_widget_backup, $normal_dashboard);

  	// Save the sorted array back into the original metaboxes 

  	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
  }
  
  public function weekly_stats_widget()
  {
    $stats = WafpReport::last_n_days_stats();
    require(WAFP_VIEWS_PATH . '/wafp-reports/weekly_stats.php');
  }
}
?>