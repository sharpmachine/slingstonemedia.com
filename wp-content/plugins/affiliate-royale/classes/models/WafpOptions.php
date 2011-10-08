<?php
class WafpOptions
{
  // Page Setup Variables
  var $dashboard_page_id;
  var $dashboard_page_id_str;
  var $signup_page_id;
  var $signup_page_id_str;
  var $login_page_id;
  var $login_page_id_str;
  
  //Affiliate Settings
  var $make_new_users_affiliates;
  var $make_new_users_affiliates_str;
  var $show_address_fields;
  var $show_address_fields_str;
  var $show_tax_id_fields;
  var $show_tax_id_fields_str;
  
  // Commission Settings
  var $commission;
  var $commission_str;
  
  var $recurring;
  var $recurring_str;

  // Integration Settings
  var $integration;
  var $integration_str;
  
  var $integrations;

  // Payment Settings
  var $payment_type;
  var $payment_type_str;
  
  // Dashboard CSS Settings
  var $dash_css_width;
  var $dash_css_width_str;
  
  // Cookie Settings
  var $expire_after_days;
  var $expire_after_days_str;
  
  // International Settings
  var $currency_code;
  var $currency_code_str;
  var $currency_symbol;
  var $currency_symbol_str;
  var $number_format;
  var $number_format_str;

  // Notification Settings
  var $welcome_email;
  var $welcome_email_str;
  var $welcome_email_subject;
  var $welcome_email_subject_str;
  var $welcome_email_body;
  var $welcome_email_body_str;
  var $admin_email;
  var $admin_email_str;
  var $admin_email_subject;
  var $admin_email_subject_str;
  var $admin_email_body;
  var $admin_email_body_str;
  var $affiliate_email;
  var $affiliate_email_str;
  var $affiliate_email_subject;
  var $affiliate_email_subject_str;
  var $affiliate_email_body;
  var $affiliate_email_body_str;
  
  var $custom_message;
  var $custom_message_str;
  
  // Is the setup sufficiently completed for affiliate program to function?
  var $setup_complete;

  function __construct()
  {
    $this->set_default_options();
  }

  function set_default_options()
  {
    global $wafp_blogname;

    if(!isset($this->dashboard_page_id))
      $this->dashboard_page_id = 0;

    if(!isset($this->signup_page_id))
      $this->signup_page_id = 0;

    if(!isset($this->login_page_id))
      $this->login_page_id = 0;

    if(!isset($this->welcome_email))
      $this->welcome_email = 1;

    if(!isset($this->welcome_email_subject))
      $this->welcome_email_subject = __("Welcome to the Affiliate Program on {\$site_name}!",'affiliate-royale');

    if(!isset($this->welcome_email_body))
      $this->welcome_email_body = <<<WELCOME_EMAIL
{\$affiliate_first_name},

Welcome to the Affiliate Program on {\$site_name}!

Username: {\$affiliate_login}
Password: {\$affiliate_password}

You can login here: {\$login_url}

Enjoy!

The {\$site_name} Team
WELCOME_EMAIL;
    
    if(!isset($this->admin_email))
      $this->admin_email = 1;

    if(!isset($this->admin_email_subject))
      $this->admin_email_subject = __('** Affiliate Sale', 'affiliate-royale');

    if(!isset($this->admin_email_body))
      $this->admin_email_body = <<<ADMIN_EMAIL
Dear admin,

New sale has been made with an affiliate link.
You may find sale details below:

----
Affilate: {\$affiliate_id} / {\$affiliate_login} / {\$affiliate_email} 
          {\$affiliate_first_name} {\$affiliate_last_name}

Transaction Type: {\$transaction_type}
Customer Name: {\$customer_name}
Customer Email: {\$customer_email}
Product: {\$item_name}
Transaction #: {\$trans_num}
Remote IP Address: {\$remote_ip_addr}
Total:       {\$payment_amount}
Commission paid: {\$commission_amount}
----
ADMIN_EMAIL;

    if(!isset($this->affiliate_email))
      $this->affiliate_email = 1;

    if(!isset($this->affiliate_email_subject))
      $this->affiliate_email_subject = __('** Affiliate Commission', 'affiliate-royale');
      
    if(!isset($this->affiliate_email_body))
      $this->affiliate_email_body = <<<AFFILIATE_EMAIL
Dear {\$affiliate_first_name},

New sale has been made with your affiliate link and 
commission credited to your balance. You can see the 
sale details below:

----
Transaction Type: {\$transaction_type}
Customer Name: {\$customer_name}
Product: {\$item_name}
Transaction #: {\$trans_num}
Total: {\$payment_amount}
Your commission: {\$commission_amount}
----
AFFILIATE_EMAIL;
    
    // Affiliate Settings
    if(!isset($this->make_new_users_affiliates))
      $this->make_new_users_affiliates = 0;

    $this->make_new_users_affiliates_str     = 'wafp-make-new-users-affiliates';
    
    if(!isset($this->show_address_fields))
      $this->show_address_fields = 0;

    $this->show_address_fields_str     = 'wafp-show-address-fields';
    
    if(!isset($this->show_tax_id_fields))
      $this->show_tax_id_fields = 0;
      
     $this->show_tax_id_fields_str    = 'wafp-show-tax-id-fields';

    if(!isset($this->commission))
      $this->commission = array(0);
    else if(is_numeric($this->commission))
      $this->commission = array($this->commission);

    if(!isset($this->recurring))
      $this->recurring = true;

    $this->dashboard_page_id_str       = 'wafp-dashboard-page-id';
    $this->signup_page_id_str          = 'wafp-signup-page-id';
    $this->login_page_id_str           = 'wafp-login-page-id';

    $this->commission_str              = 'wafp-commission';
    $this->recurring_str               = 'wafp_recurring';

    // Payment Settings
    if(!isset($this->payment_type))
      $this->payment_type = 'paypal';

    $this->payment_type_str            = 'wafp-payment-type';
	
    //Dash CSS Settings
    if(!isset($this->dash_css_width))
      $this->dash_css_width = 500;

    $this->dash_css_width_str          = 'wafp-dash-css-width';

    // Cookie Settings
    if(!isset($this->expire_after_days))
      $this->expire_after_days = 60;

    $this->expire_after_days_str       = 'wafp-expire-after-days';

    // Notification Settings
    $this->welcome_email_str           = 'wafp-welcome-email';
    $this->welcome_email_subject_str   = 'wafp-welcome-email-subject';
    $this->welcome_email_body_str      = 'wafp-welcome-email-body';
    $this->admin_email_str             = 'wafp-admin-email';
    $this->admin_email_subject_str     = 'wafp-admin-email-subject';
    $this->admin_email_body_str        = 'wafp-admin-email-body';
    $this->affiliate_email_str         = 'wafp-affiliate-email';
    $this->affiliate_email_subject_str = 'wafp-affiliate-email-subject';
    $this->affiliate_email_body_str    = 'wafp-affiliate-email-body';
    
    if(!isset($this->custom_message))
      $this->custom_message = sprintf(__('Welcome to %s\'s Affiliate Program.', 'affiliate-royale'), $wafp_blogname);
    $this->custom_message_str = 'wafp-custom-message';
    
    if( $this->dashboard_page_id == 0 or $this->signup_page_id == 0 or $this->login_page_id == 0 )
      $this->setup_complete = 0;
    else
      $this->setup_complete = 1;
    
    $this->currency_code_str   = 'wafp_currency_code';
    $this->currency_symbol_str = 'wafp_currency_symbol';
    $this->number_format_str   = 'wafp_number_format';
    
    if( !isset($this->currency_code))
      $this->currency_code = 'USD';
    if( !isset($this->currency_symbol))
      $this->currency_symbol = '$';
    if( !isset($this->number_format))
      $this->number_format = '#,###.##';
    
    if(!isset($this->integration))
      $this->integration = '';

    $this->integration_str = 'wafp-integration-type';
    
    $this->integrations = apply_filters('wafp_integrations_array', array());
  }
  
  function validate($params,$errors)
  {   
    /* We now auto create a page if one isn't selected
    if($params[ $this->dashboard_page_id_str ] == 0)
      $errors[] = __("The Affiliate Dashboard Page Must Not Be Blank.", 'affiliate-royale');

    if($params[ $this->signup_page_id_str ] == 0)
      $errors[] = __("The Affiliate Signup Page Must Not Be Blank.", 'affiliate-royale');

    if($params[ $this->login_page_id_str ] == 0)
      $errors[] = __("The Affiliate Login Page Must Not Be Blank.", 'affiliate-royale');
    */

    if( empty($params[ $this->integration_str ]) )
      $errors[] = __("Your Payment Integration Must Not Be Blank.", 'affiliate-royale');

    if( empty($params[$this->commission_str]) )
      $errors[] = __("The Commission Amount Must Not Be Blank.", 'affiliate-royale');

    foreach($params[$this->commission_str] as $index => $commish)
    {
      $level = $index + 1;
      if( !is_numeric($commish) )
        $errors[] = sprintf(__("The commission amount in level %d must be a number.", 'affiliate-royale'), $level);
      else if( (int)$commish > 100 or (int)$commish < 0 )
        $errors[] = sprintf(__("The commission amount in level %d is a percentage so it must be a number from 0 to 100.", 'affiliate-royale'), $level);
    }
      
    if( !isset($params[$this->dash_css_width_str]) or empty($params[$this->dash_css_width_str]) )
      $errors[] = __("Your Dashboard Width Must be Set. A sensible default is 500px.", 'affiliate-royale');
    else if( !is_numeric($params[$this->dash_css_width_str]) )
      $errors[] = __("Your Dashboard Width Must be A Number.", 'affiliate-royale');

    return $errors;
  }
  
  function update(&$params)
  {
    // Page Settings
    if( !is_numeric($params[$this->dashboard_page_id_str]) and
        preg_match("#^__auto_page:(.*?)$#",$params[$this->dashboard_page_id_str],$matches) )
      $this->dashboard_page_id = $params[$this->dashboard_page_id_str] = $this->auto_add_page($matches[1]);
    else
      $this->dashboard_page_id = (int)$params[$this->dashboard_page_id_str];

    if( !is_numeric($params[$this->signup_page_id_str]) and
        preg_match("#^__auto_page:(.*?)$#",$params[$this->signup_page_id_str],$matches) )
      $this->signup_page_id = $params[$this->signup_page_id_str] = $this->auto_add_page($matches[1]);
    else
      $this->signup_page_id = (int)$params[$this->signup_page_id_str];

    if( !is_numeric($params[$this->login_page_id_str]) and
        preg_match("#^__auto_page:(.*?)$#",$params[$this->login_page_id_str],$matches) )
      $this->login_page_id = $params[$this->login_page_id_str] = $this->auto_add_page($matches[1]);
    else
      $this->login_page_id = (int)$params[$this->login_page_id_str];

    // Notification Settings
    $this->welcome_email           = isset($params[$this->welcome_email_str]);
    $this->welcome_email_subject   = stripslashes($params[$this->welcome_email_subject_str]);
    $this->welcome_email_body      = stripslashes($params[$this->welcome_email_body_str]);
    $this->admin_email             = isset($params[$this->admin_email_str]);
    $this->admin_email_subject     = stripslashes($params[$this->admin_email_subject_str]);
    $this->admin_email_body        = stripslashes($params[$this->admin_email_body_str]);
    $this->affiliate_email         = isset($params[$this->affiliate_email_str]);
    $this->affiliate_email_subject = stripslashes($params[$this->affiliate_email_subject_str]);
    $this->affiliate_email_body    = stripslashes($params[$this->affiliate_email_body_str]);
    
    $this->make_new_users_affiliates = isset($params[$this->make_new_users_affiliates_str]);
    $this->show_address_fields     = isset($params[$this->show_address_fields_str]);
    $this->show_tax_id_fields      = isset($params[$this->show_tax_id_fields_str]);
    
    $this->commission              = $params[$this->commission_str];
    $this->recurring               = isset($params[$this->recurring_str]);
    
    $this->payment_type            = stripslashes($params[$this->payment_type_str]);
    $this->expire_after_days       = stripslashes($params[$this->expire_after_days_str]);
    
    $this->dash_css_width          = stripslashes($params[$this->dash_css_width_str]);
    
    $this->integration             = stripslashes($params[$this->integration_str]);
    
    if(!isset($this->recurring))
      $this->recurring = 1;
    
    $this->custom_message          = stripslashes($params[$this->custom_message_str]);

    $this->currency_code   = stripslashes($params[$this->currency_code_str]);
    $this->currency_symbol = stripslashes($params[$this->currency_symbol_str]);
    $this->number_format   = stripslashes($params[$this->number_format_str]);
  }
  
  function store()
  {
    // Save the posted value in the database
    update_option( 'wafp_options', $this );
  }
  
  function affiliate_page_url( $args )
  {
    $url = get_permalink( $this->dashboard_page_id );
    $delimiter = WafpAppController::get_param_delimiter_char( $url );
    return $url . $delimiter . $args;
  }
  
  function transaction_tracking_url( $amount='', $order_id='', $prod_id='', $aff_id='', $subscr_id='', $use_params=false, $idev_compatible=false )
  {
    $delimiter = WafpAppController::get_param_delimiter_char( WAFP_SCRIPT_URL );
    
    if($use_params)
    {  
      $amount    = urlencode(empty($amount)?'':$_REQUEST[$amount]);
      $order_id  = urlencode(empty($prod_id)?'':$_REQUEST[$order_id]);
      $prod_id   = urlencode(empty($prod_id)?'':$_REQUEST[$prod_id]);
      $aff_id    = urlencode(empty($aff_id)?'':$_REQUEST[$aff_id]);
      $subscr_id = urlencode(empty($aff_id)?'':$_REQUEST[$subscr_id]);
    }
    else
    {
      $amount    = urlencode(empty($amount)?'':$amount);
      $order_id  = urlencode(empty($prod_id)?'':$order_id);
      $prod_id   = urlencode(empty($prod_id)?'':$prod_id);
      $aff_id    = urlencode(empty($aff_id)?'':$aff_id);
      $subscr_id = urlencode(empty($aff_id)?'':$aff_id);
    }
    
    if($idev_compatible)
      $args = "controller=transactions&action=track&prod_id=Cart66&idev_saleamt={$amount}&idev_ordernum={$order_id}";
    else
      $args = "controller=transactions&action=track&amount={$amount}&order_id={$order_id}&prod_id={$prod_id}&aff_id={$aff_id}&subscr_id={$subscr_id}";
    
    return WAFP_SCRIPT_URL . $delimiter . $args;
  }
  
  function transaction_tracking_code( $html_entities=true, $amount='', $order_id='', $prod_id='', $aff_id='', $subscr_id='', $use_params=true, $idev_compatible=false )
  {
    $lt = $html_entities?"&lt;":"<";
    $gt = $html_entities?"&gt;":">";
    return "{$lt}img src=\"" . $this->transaction_tracking_url($amount, $order_id, $prod_id, $aff_id, $subscr_id, $use_params, $idev_compatible) . "\" width=\"1px\" height=\"1px\" style=\"display: none;\" /{$gt}";
  }
  
  function auto_add_page($page_name)
  {
    return wp_insert_post(array('post_title' => $page_name, 'post_type' => 'page', 'post_status' => 'publish', 'comment_status' => 'closed'));
  }
}
?>
