<?php
class WafpUsersController
{
  function __construct()
  {
    global $wafp_update, $wafp_options;
    if( $wafp_update->pro_is_installed_and_authorized() )
    {
      add_action('edit_user_profile', array(&$this, 'display_user_fields'));
      add_action('edit_user_profile_update', array(&$this, 'update_user_fields'));
      add_filter('manage_users_columns', array(&$this, 'add_affiliate_to_user_column'));
      add_filter('manage_users_custom_column', array(&$this, 'modify_user_affiliate_row'), 10, 3);
      add_action('wp_ajax_wafp_resend_welcome_email', array(&$this, 'resend_welcome_email_callback'));
      add_action('wp_ajax_wafp_load_affiliate_datatable', array(&$this, 'load_affiliate_datatable_callback'));
      add_action('admin_head', array(&$this, 'resend_welcome_email_js'));
      add_action('user_register', array(&$this, 'affiliate_registration_actions'));
    }
  }
  
  function display_login_form()
  {
    global $wafp_options, $wafp_blogurl;

    extract($_POST);

    $redirect_to = ( (isset($redirect_to) and !empty($redirect_to) )?$redirect_to:get_permalink( $wafp_options->dashboard_page_id ) );
    $redirect_to = apply_filters( 'wafp-login-redirect-url', $redirect_to );

    if(!empty($wafp_options->login_page_id) and $wafp_options->login_page_id > 0)
    {
      $login_url = get_permalink($wafp_options->login_page_id);
      $login_delim = WafpAppController::get_param_delimiter_char($login_url);
      $forgot_password_url = "{$login_url}{$login_delim}action=forgot_password";
    }
    else
    {
      $login_url = "{$wafp_blogurl}/wp-login.php";
      $forgot_password_url = "{$wafp_blogurl}/wp-login.php?action=lostpassword";
    }

    if(!empty($wafp_options->signup_page_id) and $wafp_options->signup_page_id > 0)
      $signup_url = get_permalink($wafp_options->signup_page_id);
    else
      $signup_url = $wafp_blogurl . '/wp-login.php?action=register';

    if(WafpUtils::is_user_logged_in())
      require( WAFP_VIEWS_PATH . '/shared/already_logged_in.php' );
    else
    {
      if( !empty($wafp_process_login_form) and !empty($errors) )
        require( WAFP_VIEWS_PATH . "/shared/errors.php" );

      require( WAFP_VIEWS_PATH . '/shared/login_form.php' );
    }
  }

  function process_login_form()
  {
    global $wafp_options, $wafp_profiles_controller;

    $errors = WafpUser::validate_login($_POST,array());

    $errors = apply_filters('wafp-validate-login', $errors);

    extract($_POST);

    if(empty($errors))
    {
      $creds = array();
      $creds['user_login'] = $log;
      $creds['user_password'] = $pwd;
      $creds['remember'] = $rememberme;

      if(!function_exists('wp_signon'))
        require_once(ABSPATH . WPINC . '/user.php');

      wp_signon($creds);

      $redirect_to = ((!empty($redirect_to))?$redirect_to:get_permalink($wafp_options->dashboard_page_id));

      WafpUtils::wp_redirect($redirect_to);
      exit;
    }
    else
      $_POST['errors'] = $errors;
  }

  function display_signup_form()
  {
    global $wafp_options, $wafp_blogurl;

    $process = WafpAppController::get_param('wafp-process-form');

    $redirect_to = ( (isset($redirect_to) and !empty($redirect_to) )?$redirect_to:get_permalink( $wafp_options->dashboard_page_id ) );
    $redirect_to = apply_filters( 'wafp-login-redirect-url', $redirect_to );

    if(empty($process))
    {
      if(WafpUtils::is_user_logged_in())
        require( WAFP_VIEWS_PATH . '/shared/already_logged_in.php' );
      // As it turns out we want this to be disabled in most cases for security reasons
      //else if (!get_option('users_can_register'))
      //  require( WAFP_VIEWS_PATH . '/shared/no_registration.php' );
      else
        require( WAFP_VIEWS_PATH . '/shared/signup_form.php' );
    }
    else
      $this->process_signup_form();
  }

  function process_signup_form()
  {
    global $wafp_options, $wafp_blogname;

    // Yeah, sometimes this method get's loaded multiple times (depending on the theme).
    // So these are static to not get tripped up by this
    static $errors, $user;

    if(!isset($errors))
    {
      $errors = WafpUser::validate_signup($_POST,array());
      $errors = apply_filters('wafp-validate-signup', $errors);
    }

    extract($_POST);

    if(empty($errors))
    {
      if(!isset($user))
      {
        $new_password = $wafp_user_password;

        $user = new WafpUser();
        $user->set_field('user_login', $user_login);
        $user->set_field('user_email', $user_email);
        $user->set_first_name($user_first_name);
        $user->set_last_name($user_last_name);
        $user->set_paypal_email($_POST[WafpUser::$paypal_email_str]);
        if($wafp_options->show_address_fields)
        {
          $user->set_address_one($_POST[WafpUser::$address_one_str]);
          $user->set_address_two($_POST[WafpUser::$address_two_str]);
          $user->set_city($_POST[WafpUser::$city_str]);
          $user->set_state($_POST[WafpUser::$state_str]);
          $user->set_zip($_POST[WafpUser::$zip_str]);
          $user->set_country($_POST[WafpUser::$country_str]);
        }
        if($wafp_options->show_tax_id_fields)
        {
          $user->set_tax_id_us($_POST[WafpUser::$tax_id_us_str]);
          $user->set_tax_id_int($_POST[WafpUser::$tax_id_int_str]);
        }
        $user->set_password($wafp_user_password);
        $user->set_is_affiliate(1); // uh .. yeah, this is the affiliate signup page

        // Makin' it happen...
        $user->create();
      }

      if($user->get_id())
      {
        // Yeah, we're going to record affiliate parent no matter what
        $affiliate_id = $_COOKIE['wafp_click'];
        if(isset($affiliate_id) and !empty($affiliate_id))
          update_user_meta($user->get_id(), 'wafp-affiliate-referrer', $affiliate_id);

        // Handled elsewhere
        //$user->send_account_notifications($user->get_password(),true,$wafp_options->welcome_email);

        do_action('wafp-process-signup', $user->get_id());

        require( WAFP_VIEWS_PATH . "/wafp-users/signup_thankyou.php" );
      }
      else
        require( WAFP_VIEWS_PATH . "/shared/unknown_error.php" );
    }
    else
    {
      require( WAFP_VIEWS_PATH . "/shared/errors.php" );
      require( WAFP_VIEWS_PATH . '/shared/signup_form.php' );
    }
  }

  function display_forgot_password_form()
  {
    global $wafp_options, $wafp_blogurl;

    $process = WafpAppController::get_param('wafp_process_forgot_password_form');

    if(empty($process))
      require( WAFP_VIEWS_PATH . '/wafp-users/forgot_password.php' );
    else
      $this->process_forgot_password_form();
  }

  function process_forgot_password_form()
  {
    global $wafp_options;

    $errors = WafpUser::validate_forgot_password($_POST,array());

    extract($_POST);

    if(empty($errors))
    {
      $is_email = (is_email($wafp_user_or_email) and email_exists($wafp_user_or_email));

      if(!function_exists('username_exists'))
        require_once(ABSPATH . WPINC . '/registration.php');

      $is_username = username_exists($wafp_user_or_email);

      $user = new WafpUser();

      // If the username & email are identical then let's rely on it as a username first and foremost
      if($is_username)
        $user->load_user_data_by_login( $wafp_user_or_email );
      else if($is_email)
        $user->load_user_data_by_email( $wafp_user_or_email );

      if($user->get_id())
      {
        $user->send_reset_password_requested_notification();

        require( WAFP_VIEWS_PATH . "/wafp-users/forgot_password_requested.php" );
      }
      else
        require( WAFP_VIEWS_PATH . "/shared/unknown_error.php" );
    }
    else
    {
      require( WAFP_VIEWS_PATH . "/shared/errors.php" );
      require( WAFP_VIEWS_PATH . '/wafp-users/forgot_password.php' );
    }
  }

  function display_reset_password_form($wafp_key,$wafp_screenname)
  {
    $user = new WafpUser();
    $user->load_user_data_by_login( $wafp_screenname );

	  $loginURL = get_permalink($wafp_options->login_page_id);
	  
	  if (empty($loginURL))
	    $loginURL = wp_login_url();

    if($user->get_id())
    {
      if($user->reset_form_key_is_valid($wafp_key))
        require( WAFP_VIEWS_PATH . '/wafp-users/reset_password.php' );
      else
        require( WAFP_VIEWS_PATH . '/shared/unauthorized.php' );
    }
    else
      require( WAFP_VIEWS_PATH . '/shared/unauthorized.php' );
  }

  function process_reset_password_form()
  {
    global $wafp_options;
    $errors = WafpUser::validate_reset_password($_POST,array());

    extract($_POST);

    if(empty($errors))
    {
      $user = new WafpUser();
      $user->load_user_data_by_login( $wafp_screenname );

      if($user->get_id())
      {
        $user->set_password_and_send_notifications($wafp_key, $wafp_user_password);

        require( WAFP_VIEWS_PATH . "/wafp-users/reset_password_thankyou.php" );
      }
      else
        require( WAFP_VIEWS_PATH . "/shared/unknown_error.php" );
    }
    else
    {
      require( WAFP_VIEWS_PATH . "/shared/errors.php" );
      require( WAFP_VIEWS_PATH . '/wafp-users/reset_password.php' );
    }
  }
  
  public function display_user_fields( $wpuser )
  {
    global $wafp_options;
    $user = new WafpUser( $wpuser->ID );
    
    if( WafpUtils::is_logged_in_and_an_admin() and
        is_a($user, 'WafpUser') )
    {
      if( isset($_POST['wafp_override_enabled']) and 
          isset($_POST['wafp_override']) and
          !empty($_POST['wafp_override']))
        $wafp_override = $_POST['wafp_override'];
      else
        $wafp_override = get_user_meta($user->get_id(), 'wafp_override', true);

      if( isset($_POST['wafp_override_enabled']) and 
          isset($_POST[WafpUser::$recurring_str]) )
        $recurring = true;
      else
        $recurring = $user->get_recurring();
        
      if(is_numeric($wafp_override))
        $wafp_override = array($wafp_override);
      
      if( isset($_POST[WafpUser::$is_affiliate_str]) )
        $is_affiliate = true;
      else
        $is_affiliate = $user->is_affiliate();
        
      if ($wafp_options->show_tax_id_fields)
      {
        if ( isset($_POST[WafpUser::$tax_id_us_str]) )
          $tax_id_us = $_POST[WafpUser::$tax_id_us_str];
        else
          $tax_id_us = $user->get_tax_id_us();
        
        if ( isset($_POST[WafpUser::$tax_id_int_str]) )
          $tax_id_int = $_POST[WafpUser::$tax_id_int_str];
        else
          $tax_id_int = $user->get_tax_id_int();
      }

      if($wafp_options->show_address_fields)
      {
        if ( isset($_POST[WafpUser::$address_one_str]) )
          $address_one = $_POST[WafpUser::$address_one_str];
        else
          $address_one = $user->get_address_one();
        
        if ( isset($_POST[WafpUser::$address_two_str]) )
          $address_two = $_POST[WafpUser::$address_two_str];
        else
          $address_two = $user->get_address_two();
        
        if ( isset($_POST[WafpUser::$city_str]) )
          $city = $_POST[WafpUser::$city_str];
        else
          $city = $user->get_city();
        
        if ( isset($_POST[WafpUser::$state_str]) )
          $state = $_POST[WafpUser::$state_str];
        else
          $state = $user->get_state();
        
        if ( isset($_POST[WafpUser::$zip_str]) )
          $zip = $_POST[WafpUser::$zip_str];
        else
          $zip = $user->get_zip();

        if ( isset($_POST[WafpUser::$country_str]) )
          $country = $_POST[WafpUser::$country_str];
        else
          $country = $user->get_country();
      }
      
      $hidden_str   = ($wafp_override?"":" wafp-hidden");
      $selected_str = ($wafp_override?' checked="checked"':'');
      $affiliate_selected_str = ($is_affiliate?' checked="checked"':'');
      $recurring_selected_str = ($recurring?' checked="checked"':'');
      
      $affiliate = false;
      $affiliate_id = get_user_meta($user->get_id(), 'wafp-affiliate-referrer', true);
      
      if($affiliate_id)
        $affiliate = new WafpUser($affiliate_id);
      
      require(WAFP_VIEWS_PATH . "/wafp-users/admin_profile.php");
    }
  }

  public function update_user_fields( $user_id )
  {
    if( WafpUtils::is_logged_in_and_an_admin() )
    {
      update_user_meta($user_id, WafpUser::$is_affiliate_str, isset($_POST[WafpUser::$is_affiliate_str]));

      if(isset($_POST['wafp_override_enabled']) ) {
        update_user_meta($user_id, 'wafp_override', $_POST['wafp_override']);
        update_user_meta($user_id, 'wafp_recurring', $_POST['wafp_recurring']);
      }
      else {
        delete_user_meta($user_id, 'wafp_override');
        delete_user_meta($user_id, 'wafp_recurring');
      }
      
      if ( isset($_POST[WafpUser::$tax_id_us_str]) )
        update_user_meta($user_id, WafpUser::$tax_id_us_str, $_POST[WafpUser::$tax_id_us_str]);
      
      if ( isset($_POST[WafpUser::$tax_id_int_str]) )
        update_user_meta($user_id, WafpUser::$tax_id_int_str, $_POST[WafpUser::$tax_id_int_str]);
      
      if ( isset($_POST[WafpUser::$tax_id_int_str]) )
        update_user_meta($user_id, WafpUser::$tax_id_int_str, $_POST[WafpUser::$tax_id_int_str]);
      
      if ( isset($_POST[WafpUser::$address_one_str]) )
        update_user_meta($user_id, WafpUser::$address_one_str, $_POST[WafpUser::$address_one_str]);
      
      if ( isset($_POST[WafpUser::$address_two_str]) )
        update_user_meta($user_id, WafpUser::$address_two_str, $_POST[WafpUser::$address_two_str]);
      
      if ( isset($_POST[WafpUser::$city_str]) )
        update_user_meta($user_id, WafpUser::$city_str, $_POST[WafpUser::$city_str]);

      if ( isset($_POST[WafpUser::$state_str]) )
        update_user_meta($user_id, WafpUser::$state_str, $_POST[WafpUser::$state_str]);

      if ( isset($_POST[WafpUser::$zip_str]) )
        update_user_meta($user_id, WafpUser::$zip_str, $_POST[WafpUser::$zip_str]);

      if ( isset($_POST[WafpUser::$country_str]) )
        update_user_meta($user_id, WafpUser::$country_str, $_POST[WafpUser::$country_str]);
    }
  }
  
  function add_affiliate_to_user_column( $column ) {
    $column['wafp_is_affiliate'] = 'Is Affiliate';
    $column['wafp_affiliate'] = 'Affiliate Referrer';
    return $column;
  }
  
  function modify_user_affiliate_row( $val, $column_name, $user_id ) {
    if($column_name=='wafp_affiliate')
    {
      $affiliate_id = get_user_meta($user_id, 'wafp-affiliate-referrer', true);
      
      if($affiliate_id)
      {
        $affiliate = new WafpUser($affiliate_id);
      
        if($affiliate != false)
          return "<a href=\"{$wafp_blogurl}/wp-admin/user-edit.php?user_id={$affiliate_id}&wp_http_referer=%2Fwp-admin%2Fusers.php\">" . $affiliate->get_full_name() . "</a>";
      }
      
      return __('None', 'affiliate-royale');
    }
    else if($column_name=='wafp_is_affiliate')
    {
      $user = new WafpUser($user_id);

      return ($user->is_affiliate()?__('Yes', 'affiliate-royale'):__('No', 'affiliate-royale'));
    }
  }
  
  
  function resend_welcome_email_callback()
  {  
    if( wp_verify_nonce( $_REQUEST['_wafp_nonce'], 'wafp-resend-welcome-email' ) )
    {
      if( WafpUtils::is_logged_in_and_an_admin() )
      {
        $user = new WafpUser($_REQUEST['uid']);
        $user->send_account_notifications( '', false, true );
        _e('Message Sent', 'affiliate-royale');
        die();
      }
      _e('Unauthorized to resend message', 'affiliate-royale');
      die();
    }
    _e('Cannot resend message', 'affiliate-royale');
    die();
  }
  
  function resend_welcome_email_js()
  {
    ?>
    <script type="text/javascript" >
      jQuery(document).ready(function() {
        jQuery('.wafp-resend-welcome-email').click( function() {
          jQuery('.wafp-resend-welcome-email-loader').show();
          
          var data = {
            action: 'wafp_resend_welcome_email',
            uid: jQuery(this).attr('user-id'),
            _wafp_nonce: jQuery(this).attr('wafp-nonce')
          };
          
          jQuery.post(ajaxurl, data, function(response) {
            jQuery('.wafp-resend-welcome-email-loader').hide();
            jQuery('.wafp-resend-welcome-email-message').text(response);
          });
        });
      });
    </script>
    <?php
  }
  
  function load_affiliate_datatable_callback()
  {
    if( wp_verify_nonce( $_REQUEST['_wafp_nonce'], 'wafp_load_affiliate_datatable' ) )
    {
      if( WafpUtils::is_logged_in_and_an_admin() )
      {
        echo WafpUser::affiliate_datatable();
        die();
      }
      _e('Unauthorized', 'affiliate-royale');
      die();
    }
    _e('Unauthorized', 'affiliate-royale');
    die();
  }
  
  function display_affiliates_list()
  {
    $ajax_action = 'wafp_load_affiliate_datatable';
    $sortcol = 8; // currently this is the signup date column
    $sortdir = "desc"; // Newest first
    
    $columns = array(
      'username' => array( 'width' => '10%',
                           'label' => __('Username', 'affiliate-royale'),
                           'type' => 'link',
                           'replace' => 'ID',
                           'link' => '/wp-admin/user-edit.php?user_id=:ID' ),
      'first_name' => array( 'width' => '10%',
                             'label' => __('First Name', 'affiliate-royale'),
                             'type' => 'string' ),
      'last_name' => array( 'width' => '10%',
                            'label' => __('Last Name', 'affiliate-royale'),
                            'type' => 'string' ),
      'ID' => array( 'width' => '5%',
                     'label' => __('ID', 'affiliate-royale'),
                     'type' => 'string' ),
      'mtd_clicks' => array( 'width' => '5%',
                             'label' => __('MTD Clicks', 'affiliate-royale'),
                             'type' => 'string' ),
      'ytd_clicks' => array( 'width' => '5%',
                             'label' => __('YTD Clicks', 'affiliate-royale'),
                             'type' => 'string' ),
      'mtd_commissions' => array( 'width' => '10%',
                                  'label' => __('MTD Commissions', 'affiliate-royale'),
                                  'type' => 'string' ),
      'ytd_commissions' => array( 'width' => '10%',
                                  'label' => __('YTD Commissions', 'affiliate-royale'),
                                  'type' => 'string' ),
      'signup_date' => array( 'width' => '15%',
                              'label' => __('Signup Date', 'affiliate-royale'),
                              'type' => 'string ' ),
      'parent_name' => array( 'width' => '15%',
                              'label' => __('Referrer', 'affiliate-royale'),
                              'type' => 'link',
                              'replace' => 'parent_id',
                              'link' => '/wp-admin/user-edit.php?user_id=:parent_id'),
      'parent_id' => array( 'type' => 'hidden' )
    );
    
    require(WAFP_VIEWS_PATH . '/wafp-users/affiliates_list.php');
  }
  
  function affiliate_registration_actions($user_id) {
    global $wafp_options;
    
    $user = new WafpUser($user_id);
    
    // Let's set user to be an affiliate automatically
    if($wafp_options->make_new_users_affiliates and !$user->get_is_affiliate()) {
      $user->set_is_affiliate(true);
      $user->store();
    }
    
    if($user->get_is_affiliate())
      $user->send_account_notifications( $user->get_password(), $wafp_options->admin_email, $wafp_options->welcome_email );
  }
}
?>