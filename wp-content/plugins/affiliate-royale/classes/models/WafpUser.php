<?php
class WafpUser
{
  var $userdata;

  static $id_str           = 'ID';
  static $first_name_str   = 'first_name';
  static $last_name_str    = 'last_name';
  static $password_str     = 'user_pass';
  static $paypal_email_str = 'wafp_paypal_email';
  static $address_one_str  = 'wafp_user_address_one';
  static $address_two_str  = 'wafp_user_address_two';
  static $city_str         = 'wafp_user_city';
  static $state_str        = 'wafp_user_state';
  static $zip_str          = 'wafp_user_zip';
  static $country_str      = 'wafp_user_country';
  static $tax_id_us_str    = 'wafp_user_tax_id_us';
  static $tax_id_int_str   = 'wafp_user_tax_id_int';
  static $is_affiliate_str = 'wafp_is_affiliate';
  static $referrer_str     = 'wafp-affiliate-referrer';
  static $recurring_str    = 'wafp_recurring';

  function WafpUser( $id = '')
  {
    $this->load_user_data_by_id( $id );
  }

  function load_user_data_by_id( $id = '' )
  { 
    if( empty($id) or !$id )
      $this->userdata = array();
    else
      $this->userdata = (array)WafpUtils::get_userdata($id);
    
    // This must be here to ensure that we don't pull an encrypted 
    // password, encrypt it a second time and store it
    unset($this->userdata[self::$password_str]);
  }

  function load_user_data_by_login( $login = '' )
  {
    if( empty($login) or !$login )
      $this->userdata = array();
    else
      $this->userdata = (array)WafpUtils::get_userdatabylogin($login);
    
    // This must be here to ensure that we don't pull an encrypted 
    // password, encrypt it a second time and store it
    unset($this->userdata[self::$password_str]);
  }

  function load_user_data_by_email( $email = '' )
  {
    $user_id = email_exists($email);
    $this->load_user_data_by_id( $user_id );
  }

  function load_posted_data()
  {
    $object_vars =& get_object_vars($this);
    
    foreach( $object_vars as $key => $value )
    {
      if(preg_match('#^.*_str$#', $key))
        $this->userdata[ $value ] = $_POST[ $value ];
    }
  }

  function get_id()
  {
    return $this->userdata[ self::$id_str ];
  }  
  
  function set_id( $value )
  {
    $this->userdata[ self::$id_str ] = $value;
  }

  function get_first_name()
  {
    return $this->userdata[ self::$first_name_str ];
  }

  function set_first_name($value)
  {
    $this->userdata[ self::$first_name_str ] = $value;
  }

  function get_last_name()
  {
    return $this->userdata[ self::$last_name_str ];
  }
  function set_last_name($value)
  {
    $this->userdata[ self::$last_name_str ] = $value;
  }

  function get_full_name()
  {
    return $this->get_first_name() . ' ' . $this->get_last_name();
  }

  function get_paypal_email()
  {
    return (isset($this->userdata[ self::$paypal_email_str ])?$this->userdata[ self::$paypal_email_str ]:'');
  }

  function set_paypal_email($value)
  {
    $this->userdata[ self::$paypal_email_str ] = $value;
  }
  
  function get_address_one()
  {
    return (isset($this->userdata[ self::$address_one_str ])?$this->userdata[ self::$address_one_str ]:'');
  }

  function set_address_one($value)
  {
    $this->userdata[ self::$address_one_str ] = $value;
  }
  
  function get_address_two()
  {
    return (isset($this->userdata[ self::$address_two_str ])?$this->userdata[ self::$address_two_str ]:'');
  }

  function set_address_two($value)
  {
    $this->userdata[ self::$address_two_str ] = $value;
  }
  
  function get_city()
  {
    return (isset($this->userdata[ self::$city_str ])?$this->userdata[ self::$city_str ]:'');
  }

  function set_city($value)
  {
    $this->userdata[ self::$city_str ] = $value;
  }
  
  function get_state()
  {
    return (isset($this->userdata[ self::$state_str ])?$this->userdata[ self::$state_str ]:'');
  }

  function set_state($value)
  {
    $this->userdata[ self::$state_str ] = $value;
  }
  
  function get_zip()
  {
    return (isset($this->userdata[ self::$zip_str ])?$this->userdata[ self::$zip_str ]:'');
  }

  function set_zip($value)
  {
    $this->userdata[ self::$zip_str ] = $value;
  }

  function get_country()
  {
    return (isset($this->userdata[ self::$country_str ])?$this->userdata[ self::$country_str ]:'');
  }

  function set_country($value)
  {
    $this->userdata[ self::$country_str ] = $value;
  }
  
  function get_password()
  {
    return (isset($this->userdata[ self::$password_str ])?$this->userdata[ self::$password_str ]:'');
  }

  function set_password($value)
  {
    $this->userdata[ self::$password_str ] = $value;
  }

  function get_is_affiliate()
  {
    return (isset($this->userdata[ self::$is_affiliate_str ])?$this->userdata[ self::$is_affiliate_str ]:false);
  }
  
  function set_is_affiliate($value)
  {
    $this->userdata[ self::$is_affiliate_str ] = $value;
  }
  
  function get_tax_id_us()
  {
    return (isset($this->userdata[ self::$tax_id_us_str ])?$this->userdata[ self::$tax_id_us_str ]:'');
  }
  
  function set_tax_id_us($value)
  {
    $this->userdata[ self::$tax_id_us_str ] = $value;
  }
  
  function get_tax_id_int()
  {
    return (isset($this->userdata[ self::$tax_id_int_str ])?$this->userdata[ self::$tax_id_int_str ]:'');
  }
  
  function set_tax_id_int($value)
  {
    $this->userdata[ self::$tax_id_int_str ] = $value;
  }
  
  function get_referrer()
  {
    return (isset($this->userdata[ self::$referrer_str ])?$this->userdata[ self::$referrer_str ]:'');
  }
  
  function set_referrer($value)
  {
    $this->userdata[ self::$referrer_str ] = $value;
  }
  
  function get_recurring()
  {
    return (isset($this->userdata[ self::$recurring_str ])?$this->userdata[ self::$recurring_str ]:'');
  }
  
  function set_recurring($value)
  {
    $this->userdata[ self::$recurring_str ] = $value;
  }
  
  // Generic getters and setters for the userdata object
  function get_field($name)
  {
    return (isset($this->userdata[$name])?$this->userdata[$name]:'');
  }
  
  function set_field($name, $value)
  {
    $this->userdata[$name] = $value;
  }
  
  // alias of get_is_affiliate
  function is_affiliate()
  {
    return $this->get_is_affiliate();
  }

  function create()
  {
    if(isset($this->userdata[ self::$id_str ]))
      unset($this->userdata[ self::$id_str ]);

    $user_id = $this->store();

    $this->set_id($user_id);

    return $user_id;
  }
  
  // alias of store
  function update()
  {
    return $this->store();
  }
  
  function store()
  {
    global $wafp_options;
    
    if(!function_exists('wp_insert_user'))
      require_once( ABSPATH . '/wp-includes/registration.php' );
    
    if(isset($this->userdata[ self::$id_str ]) and is_numeric($this->userdata[ self::$id_str ]))
      $user_id = wp_update_user($this->userdata);
    else
      $user_id = wp_insert_user($this->userdata);
      
    if ($user_id)
    {
      update_user_meta($user_id, self::$paypal_email_str, $this->get_paypal_email());
      update_user_meta($user_id, self::$is_affiliate_str, $this->get_is_affiliate());
      update_user_meta($user_id, self::$referrer_str, $this->get_referrer());
      update_user_meta($user_id, self::$recurring_str, $this->get_recurring());
      if($wafp_options->show_address_fields)
      {
        update_user_meta($user_id, self::$address_one_str, $this->get_address_one());
        update_user_meta($user_id, self::$address_two_str, $this->get_address_two());
        update_user_meta($user_id, self::$city_str, $this->get_city());
        update_user_meta($user_id, self::$state_str, $this->get_state());
        update_user_meta($user_id, self::$zip_str, $this->get_zip());
        update_user_meta($user_id, self::$country_str, $this->get_country());
      }
      if($wafp_options->show_tax_id_fields)
      {
        update_user_meta($user_id, self::$tax_id_us_str, $this->get_tax_id_us());
        update_user_meta($user_id, self::$tax_id_int_str, $this->get_tax_id_int());
      }
    }
  
    return $user_id;
  }
  
  function send_account_notifications($password='',$send_admin_notification=true, $send_affiliate_notification=true)
  {
    global $wafp_blogname, $wafp_blogurl, $wafp_options;
    
    $login_link = $login_url = get_permalink($wafp_options->login_page_id);
    
    if(empty($login_url))
      $login_url = $wafp_blogurl;
    
    if($send_admin_notification)
    {
      // Send notification email to admin user
      $from_name  = $wafp_blogname; //senders name
      $from_email = get_option('admin_email'); //senders e-mail address
      $recipient  = "{$from_name} <{$from_email}>"; //recipient
      $header     = "From: {$recipient}"; //optional headerfields
      
      /* translators: In this string, %s is the Blog Name/Title */
      $subject    = sprintf( __("[%s] New Affiliate Signup",'affiliate-royale'), $wafp_blogname);
      
      /* translators: In this string, %1$s is the blog's name/title, %2$s is the user's real name, %3$s is the user's username and %4$s is the user's email */
      $message    = sprintf( __( "A new user just joined your Affiliate Program at %1\$s!\n\nName: %2\$s\nUsername: %3\$s\nE-Mail: %4\$s", 'affiliate-royale' ), $wafp_blogname, $this->get_full_name(), $this->get_field('user_login'), $this->get_field('user_email') ) . "\n\n";
      
      WafpUtils::wp_mail($recipient, $subject, $message, $header);
    }

    if($send_affiliate_notification)
    {
      // Send password email to new user
      $from_name  = $wafp_blogname; //senders name
      $from_email = get_option('admin_email'); //senders e-mail address
      $recipient  = "{$this->get_full_name()} <{$this->get_field('user_email')}>"; //recipient
      $header     = "From: {$from_name} <{$from_email}>"; //optional headerfields
      
      // Replacement Variables
      $site_name            = $wafp_blogname;
      $affiliate_first_name = $this->get_first_name();
      $affiliate_first_name = (empty($affiliate_first_name)?$this->get_field('user_login'):$affiliate_first_name);
      $affiliate_login      = $this->get_field('user_login');
      $affiliate_password   = (empty($password)?__("*** The password you created at signup ***", 'affiliate-royale'):$password);
      
      $rep_vars = compact( 'site_name', 'affiliate_first_name', 'affiliate_first_name', 'affiliate_login', 'affiliate_password', 'login_url' );
      
      $subject = WafpUtils::replace_text_variables($wafp_options->welcome_email_subject, $rep_vars);
      $message = WafpUtils::replace_text_variables($wafp_options->welcome_email_body, $rep_vars);
      
      WafpUtils::wp_mail($recipient, $subject, $message, $header);
    }  
  }
  
  function reset_form_key_is_valid($key)
  {
    $stored_key = $this->get_field( 'wafp_reset_password_key' );
    
    return ($stored_key and ($key == $stored_key));
  }
  
  function send_reset_password_requested_notification()
  {
    global $wafp_blogname, $wafp_blogurl, $wafp_options;

    $key = md5(time() . $this->get_id());
    update_user_meta( $this->get_id(), 'wafp_reset_password_key', $key );
    
    $permalink = get_permalink($wafp_options->login_page_id);
    $delim     = WafpAppController::get_param_delimiter_char($permalink);
    
    $reset_password_link = "{$permalink}{$delim}action=reset_password&mkey={$key}&u=" . $this->get_field('user_login');

    // Send password email to new user
    $from_name  = $wafp_blogname; //senders name
    $from_email = get_option('admin_email'); //senders e-mail address
    $recipient  = "{$this->get_full_name()} <{$this->get_field('user_email')}>"; //recipient
    $header     = "From: {$from_name} <{$from_email}>"; //optional headerfields
    
    /* translators: In this string, %s is the Blog Name/Title */
    $subject       = sprintf( __("[%s] Affiliate Password Reset",'affiliate-royale'), $wafp_blogname);
    
    /* translators: In this string, %1$s is the user's username, %2$s is the blog's name/title, %3$s is the blog's url, %4$s the reset password link */
    $message       = sprintf( __( "Someone requested to reset your password for %1\$s on the Affiliate Program at %2\$s at %3\$s\n\nTo reset your password visit the following address, otherwise just ignore this email and nothing will happen.\n\n%4\$s", 'affiliate-royale' ), $this->get_field('user_login'), $wafp_blogname, $wafp_blogurl, $reset_password_link );
    
    WafpUtils::wp_mail($recipient, $subject, $message, $header);
  }
  
  function set_password_and_send_notifications($key, $password)
  {  
    global $wafp_blogname, $wafp_blogurl, $wafp_options;

    if($this->reset_form_key_is_valid($key))
    {
      delete_user_meta( $this->get_id(), 'wafp_reset_password_key' );

      $this->set_password($password);
      $this->store();

      $edit_permalink = get_permalink($wafp_options->dashboard_page_id);

      // Send notification email to admin user
      $from_name  = $wafp_blogname; //senders name
      $from_email = get_option('admin_email'); //senders e-mail address
      $recipient  = "{$from_name} <{$from_email}>"; //recipient
      $header     = "From: {$recipient}"; //optional headerfields

      /* translators: In this string, %s is the Blog Name/Title */
      $subject    = sprintf( __("[%s] Affiliate Password Lost/Changed",'affiliate-royale'), $wafp_blogname);

      /* translators: In this string, %1$s is the user's username */
      $message       = sprintf( __( "Affiliate Password Lost and Changed for user: %1\$s", 'affiliate-royale' ), $this->get_field('user_login') );

      WafpUtils::wp_mail($recipient, $subject, $message, $header);

      $login_link = get_permalink($wafp_options->login_page_id);
      
      // Send password email to new user
      $from_name  = $wafp_blogname; //senders name
      $from_email = get_option('admin_email'); //senders e-mail address
      $recipient  = "{$this->get_full_name()} <{$this->get_field('user_email')}>"; //recipient
      $header     = "From: {$from_name} <{$from_email}>"; //optional headerfields

      /* translators: In this string, %s is the Blog Name/Title */
      $subject       = sprintf( __("[%s] Your new Affiliate Password",'affiliate-royale'), $wafp_blogname);

      /* translators: In this string, %1$s is the user's first name, %2$s is the blog's name/title, %3$s is the user's username, %4$s is the user's password, and %5$s is the blog's URL... */
      $message       = sprintf( __( "%1\$s,\n\nYour Affiliate Password was successfully reset on %2\$s!\n\nUsername: %3\$s\nPassword: %4\$s\n\nYou can login here: %5\$s", 'affiliate-royale' ), (empty($this->first_name)?$this->get_field('user_login'):$this->first_name), $wafp_blogname, $this->get_field('user_login'), $password, $login_link );

      WafpUtils::wp_mail($recipient, $subject, $message, $header);
      
      return true;
    }
    
    return false;
  }

  /** Calculates the commission percentage for the current user on the given level */
  public function get_commission_percentage( $level = 0 )
  {
    global $wafp_options;

    // Account for the user override commission percentage -- if there is one
    if($tmp_percent = get_user_meta($this->get_id(), 'wafp_override', true))
    {
      if( !isset($tmp_percent[$level]) )
        return false;
      
      $commission_percentage = (float)$tmp_percent[$level];
    }
    else
    {
      if( !isset($wafp_options->commission[$level]) )
        return false;

      $commission_percentage = (float)$wafp_options->commission[$level];
    }

    return $commission_percentage;
  }

  /** Calculates the commission amount for the current user for the amount on a given level */
  public function calculate_commission( $amount, $level = 0 )
  {
    global $wafp_options;

    $commission_percentage = $this->get_commission_percentage($level);

    if($commission_percentage !== false)
      return sprintf( "%0.2f", ( (float)$amount * $commission_percentage / 100.00 ) );
    else
      return false;
  }
  
  /** Get commission percentages for the affiliates above the current user */
  public function get_commission_percentages($im_the_first_affiliate=false, $compress_levels=false)
  {
    $commission_percentages = array();
    $affiliates = $this->get_affiliates($im_the_first_affiliate, $compress_levels);
    
    // We just skip over users
    foreach($affiliates as $level => $affiliate)
    {
      $commission_percentages[] = ( $affiliate->is_affiliate() ? $affiliate->get_commission_percentage($level) : 0.0 );
    }
    
    return $commission_percentages;
  }
  
  /** Get commission amounts for the affiliates above the current user given the total sale amount */
  public function calculate_commissions($amount, $im_the_first_affiliate=false, $compress_levels=false)
  {
    $commission_amounts = array();
    $affiliates = $this->get_affiliates($im_the_first_affiliate,$compress_levels);
    
    foreach($affiliates as $level => $affiliate)
      $commission_amounts[] = ( $affiliate->is_affiliate() ? $affiliate->calculate_commission($amount,$level) : 0.0 );
    
    return $commission_amounts;
  }
  
  public function get_commission_percentages_total($im_the_first_affiliate=false, $compress_levels=false)
  {
    return (float)array_sum($this->get_commission_percentages($im_the_first_affiliate, $compress_levels));
  }
  
  public function calculate_commissions_total($amount, $im_the_first_affiliate=false, $compress_levels=false)
  {
    return sprintf("%0.2f",array_sum($this->calculate_commissions($amount, $im_the_first_affiliate, $compress_levels)));
  }
  
  /** Returns an array of the affiliates for this user */
  public function get_affiliates( $im_the_first_affiliate=false, $compress_levels=false, $commissionable_only=true )
  {
    global $wafp_options;
    
    $levels = count($wafp_options->commission);
    $affiliate_array = array();
    
    if($im_the_first_affiliate)
      $affiliate_array[] = $this;
    
    $curr_id = $this->get_id();
    while( $aff_id = get_user_meta( $curr_id, 'wafp-affiliate-referrer', true ) )
    {
      $curr_id = $aff_id;
      
      if( is_numeric($aff_id) and ( $aff = new WafpUser($aff_id) ) )
      {
        if($compress_levels and !$aff->is_affiliate())
          continue;
        
        $affiliate_array[] = $aff;
        
        if($commissionable_only and count($affiliate_array) >= $levels)
          break;
      }
      else
        break;
    }
    
    return $affiliate_array;
  }

  function pay_commission($is_recurring=false)
  {
    global $wafp_options;
    
    if($is_recurring)
    {
      $user_override_set = (get_user_meta($this->get_id(), 'wafp_override', true));
      if($user_override_set)
        return $this->get_recurring();
      else
        return $wafp_options->recurring;
    }
    
    return true;
  }
  
  function affiliate_datatable() {
    global $wafp_db, $wpdb, $wafp_options;
    
    $year = date('Y');
    $month = date('m');
    
    $cols = array(
      'username' => "{$wpdb->users}.user_login",
      'first_name' => 'um_first_name.meta_value',
      'last_name' => 'um_last_name.meta_value',
      'ID' => "{$wpdb->users}.ID",
      'mtd_clicks' => "(SELECT IFNULL(COUNT(*),0) FROM {$wafp_db->clicks} as clk WHERE clk.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-{$month}-01 00:00:00' AND NOW())",
      'ytd_clicks' => "(SELECT IFNULL(COUNT(*),0) FROM {$wafp_db->clicks} as clk WHERE clk.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-01-01 00:00:00' AND NOW())",
      'mtd_commissions' => "(SELECT CONCAT('{$wafp_options->currency_symbol}', FORMAT(IFNULL(SUM(commish.commission_amount),0.00),2) ) FROM {$wafp_db->commissions} AS commish WHERE commish.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-{$month}-01 00:00:00' AND NOW())",
      'ytd_commissions' => "(SELECT CONCAT('{$wafp_options->currency_symbol}', FORMAT(IFNULL(SUM(commish.commission_amount),0.00),2) ) FROM {$wafp_db->commissions} AS commish WHERE commish.affiliate_id={$wpdb->users}.ID AND created_at BETWEEN '{$year}-01-01 00:00:00' AND NOW())",
      'signup_date' => "DATE({$wpdb->users}.user_registered)",
      'parent_name' => "CONCAT(um_parent_first_name.meta_value,' ', um_parent_last_name.meta_value, ' (', parent.user_login, ')')",
      'parent_id' => "parent.ID"
    );
      
    $joins = array(
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_first_name ON um_first_name.user_id={$wpdb->users}.ID AND um_first_name.meta_key='first_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_last_name ON um_last_name.user_id={$wpdb->users}.ID AND um_last_name.meta_key='last_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_affiliate_referrer ON um_affiliate_referrer.user_id={$wpdb->users}.ID AND um_affiliate_referrer.meta_key='wafp-affiliate-referrer'",
      "LEFT OUTER JOIN {$wpdb->users} AS parent ON parent.ID=um_affiliate_referrer.meta_value",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_parent_first_name ON um_parent_first_name.user_id=parent.ID AND um_parent_first_name.meta_key='first_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_parent_last_name ON um_parent_last_name.user_id=parent.ID AND um_parent_last_name.meta_key='last_name'",
      "LEFT OUTER JOIN {$wpdb->usermeta} AS um_is_affiliate ON um_is_affiliate.user_id={$wpdb->users}.ID AND um_is_affiliate.meta_key='wafp_is_affiliate'"
    );
    
    $args = array(
      'um_is_affiliate.meta_value IS NOT NULL',
      'um_is_affiliate.meta_value=1'
    );
    
    return WafpDb::datatable($cols, $wpdb->users, '', '', $joins, $args);
  }

/***** STATIC METHODS *****/
  function validate_signup($params,$errors)
  {
    global $wafp_options;

    require_once(ABSPATH . WPINC . '/registration.php');
    
    extract($params);
  
    if(empty($user_login))
      $errors[] = __('Username must not be blank','affiliate-royale');

    if(!preg_match('#^[a-zA-Z0-9_]+$#',$user_login))
      $errors[] = __('Username must only contain letters, numbers and/or underscores','affiliate-royale');

    if ( username_exists( $user_login ) )
    	$errors[] = __('Username is Already Taken.','affiliate-royale');
  
    if($wafp_options->payment_type == 'paypal' and empty($wafp_paypal_email))
      $errors[] = __('PayPal email must not be blank','affiliate-royale');
    
    if(!is_email($user_email))
      $errors[] = __('Email must be a real and properly formatted email address','affiliate-royale');
    
    if(email_exists($user_email))
      $errors[] = __('Email Address has already been used by another user.','affiliate-royale');

    if(empty($wafp_user_password))
      $errors[] = __('You must enter a Password.','affiliate-royale');
    
    if(empty($wafp_user_password_confirm))
      $errors[] = __('You must enter a Password Confirmation.', 'affiliate-royale');
    
    if($wafp_user_password != $wafp_user_password_confirm)
      $errors[] = __('Your Password and Password Confirmation don\'t match.', 'affiliate-royale');
      
    if($wafp_options->show_address_fields && empty($wafp_user_address_one))
      $errors[] = __('You must enter an Address', 'affiliate-royale');
      
    if($wafp_options->show_address_fields && empty($wafp_user_city))
      $errors[] = __('You must enter a City', 'affiliate-royale');
      
    if($wafp_options->show_address_fields && empty($wafp_user_state))
      $errors[] = __('You must enter a State/Province', 'affiliate-royale');
      
    if($wafp_options->show_address_fields && empty($wafp_user_zip))
      $errors[] = __('You must enter a Zip/Postal Code', 'affiliate-royale');
  
    if($wafp_options->show_address_fields && empty($wafp_user_country))
      $errors[] = __('You must enter a Country', 'affiliate-royale');
    
    return $errors;
  }

  static function validate_login($params,$errors)
  {
    extract($params);

    if(empty($log))
      $errors[] = __('Username must not be blank','affiliate-royale');

    if(!function_exists('username_exists'))
      require_once(ABSPATH . WPINC . '/registration.php');

    if(!username_exists($log))
      $errors[] = __('Username was not found','affiliate-royale');
    else
    {
      if(!function_exists('user_pass_ok'))
        require_once(ABSPATH . WPINC . '/user.php');

      if(!user_pass_ok($log, $pwd))
        $errors[] = __('Your Password was Incorrect','affiliate-royale');
    }

    return $errors;
  }

  static function validate_forgot_password($params,$errors)
  {
    extract($params);

    if(empty($wafp_user_or_email))
      $errors[] = __('You must enter a Username or Email','affiliate-royale');
    else
    {
      if(!function_exists('username_exists') or !function_exists('email_exists'))
        require_once(ABSPATH . WPINC . '/registration.php');

      $is_email = (is_email($wafp_user_or_email) and email_exists($wafp_user_or_email));
      $is_username = username_exists($wafp_user_or_email);
      
      if(!$is_email and !$is_username)
        $errors[] = __('That Username or Email wasn\'t found.','affiliate-royale');
    }

    return $errors;    
  }

  static function validate_reset_password($params,$errors)
  {
    extract($params);

    if(empty($wafp_user_password))
      $errors[] = __('You must enter a Password.','affiliate-royale');
      
    if(empty($wafp_user_password_confirm))
      $errors[] = __('You must enter a Password Confirmation.', 'affiliate-royale');
      
    if($wafp_user_password != $wafp_user_password_confirm)
      $errors[] = __('Your Password and Password Confirmation don\'t match.', 'affiliate-royale');
    
    return $errors;
  }
}
?>