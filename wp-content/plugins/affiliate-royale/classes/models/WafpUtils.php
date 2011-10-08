<?php
class WafpUtils
{
  function get_user_id_by_email($email)
  {
    if(isset($email) and !empty($email))
    {
      global $wpdb;
      $query = "SELECT ID FROM {$wpdb->users} WHERE user_email=%s";
      $query = $wpdb->prepare($query, mysql_escape_string($email));
      return (int)$wpdb->get_var($query);
    }
    
    return '';
  }
  
  function is_image($filename)
  {
    if(!file_exists($filename))
      return false;

    $file_meta = getimagesize($filename);
    
    $image_mimes = array("image/gif", "image/jpeg", "image/png");
    
    return in_array($file_meta['mime'], $image_mimes);
  }
  
  function rewriting_on()
  {
    $permalink_structure = get_option('permalink_structure');
    
    return ($permalink_structure and !empty($permalink_structure));
  }
  
  // Returns a list of just user data from the wp_users table
  function get_raw_users($where = '', $order_by = 'user_login')
  {
    global $wpdb;

    static $raw_users;
    
    if(!isset($raw_users))
    {
      $where    = ((empty($where))?'':" WHERE {$where}");
      $order_by = ((empty($order_by))?'':" ORDER BY {$order_by}");
      
      $query = "SELECT * FROM {$wpdb->users}{$where}{$order_by}";
      $raw_users = $wpdb->get_results($query);
    }
    
    return $raw_users;
  }
  
  function is_robot()
  {
    $ua_string = trim(urldecode($_SERVER['HTTP_USER_AGENT']));

    // Yah, if the whole user agent string is missing -- wtf?
    if(empty($ua_string))
      return 1;

    // Some bots actually say they're bots right up front let's get rid of them asap
    if(preg_match("#(bot|spider|crawl)#i",$ua_string))
      return 1;
      
    $browsecap = WafpUtils::php_get_browser($ua_string);
    $btype = trim($browsecap['browser']);

    $crawler = $browsecap['crawler'];

    // If php_browsecap tells us its a bot, let's believe it
    if($crawler == 1)
      return 1;

    // If the Browser type was unidentifiable then it's most likely a bot
    if(empty($btype))
      return 1;

    return 0;
  }

  function send_affiliate_sale_notification($params)
  {
    global $wafp_options, $wafp_blogname;

    if($wafp_options->affiliate_email)
    {
      extract($params);
      $email_body = self::replace_text_variables( $wafp_options->affiliate_email_body, $params );

      // Send notification email to admin user (to and from the admin user)
      $to_email = $affiliate_email;
      $to_name  = "{$affiliate_first_name} {$affiliate_last_name}";
      $nice_to_email = "{$to_name} <{$to_email}>";

      $from_email = get_option('admin_email'); //senders name
      $nice_from_email = "{$wafp_blogname} <{$from_email}>";
      $header    = "From: {$nice_from_email}"; //optional headerfields

      WafpUtils::wp_mail($nice_to_email, $wafp_options->affiliate_email_subject, $email_body, $header);
    }
  }
  
  function send_affiliate_sale_notifications($params, $affiliates)
  {
    $payment_amount = $params['payment_amount'];
    $params['payment_amount']        = WafpAppHelper::format_currency( $params['payment_amount']);
    foreach($affiliates as $level => $affiliate)
    {
      $params['affiliate_login']       = $affiliate->get_field('user_login'); 
      $params['affiliate_email']       = $affiliate->get_field('user_email');
      $params['affiliate_first_name']  = $affiliate->get_first_name();
      $params['affiliate_last_name']   = $affiliate->get_last_name();
      
      $params['affiliate_id']          = $affiliate->get_id();
      $params['commission_percentage'] = $affiliate->get_commission_percentage( $level );
      $params['commission_amount']     = $affiliate->calculate_commission( $payment_amount, $level );
      
      $params['commission_percentage'] = sprintf("%0.2f", $params['commission_percentage']) . "%";
      $params['commission_amount']     = WafpAppHelper::format_currency( $params['commission_amount']);
      $params['payment_level']         = $level + 1; // we're doing 1 based level any time its displayed
      
      WafpUtils::send_affiliate_sale_notification($params);
    }
  }

  function send_admin_sale_notification($params, $affiliates)
  {
    global $wafp_options, $wafp_blogname;

    if($wafp_options->admin_email)
    {
      extract($params);
      
      $email_body = '';
      $payment_amount_num = $payment_amount;
      $payment_amount     = WafpAppHelper::format_currency( $payment_amount);
      $i = 0;
      foreach($affiliates as $level => $affiliate)
      {
        $affiliate_login       = $affiliate->get_field('user_login'); 
        $affiliate_email       = $affiliate->get_field('user_email');
        $affiliate_first_name  = self::with_default($affiliate->get_first_name(),$affiliate_login);
        $affiliate_last_name   = $affiliate->get_last_name();

        $affiliate_id          = $affiliate->get_id();
        $commission_percentage = $affiliate->get_commission_percentage( $level );
        $commission_amount     = $affiliate->calculate_commission( $payment_amount_num, $level );

        $commission_percentage = sprintf("%0.2f", $commission_percentage) . "%";
        $commission_amount     = WafpAppHelper::format_currency( $commission_amount);
        
        $rep_vars = array_merge( $params, compact( 'payment_amount_num', 'payment_amount', 'affiliate_login', 'affiliate_email', 'affiliate_first_name', 'affiliate_last_name', 'affiliate_id', 'commission_percentage', 'commission_amount' ) );
        
        $email_body .= self::replace_text_variables( $wafp_options->admin_email_body, $rep_vars );
        
        if($i < (count($affiliates) - 1))
          $email_body .= "\n=====================================\n";
        
        $i++;
      }
      
      $from_email = get_option('admin_email'); //senders name
      $nice_from_email = "{$wafp_blogname} <{$from_email}>";
      $header    = "From: {$nice_from_email}"; //optional headerfields

      WafpUtils::wp_mail($nice_from_email, $wafp_options->admin_email_subject, $email_body, $header);
    }
  }
  
  function is_logged_in_and_current_user($user_id)
  {
    global $current_user;
    WafpUtils::get_currentuserinfo();

    return (WafpUtils::is_user_logged_in() and ($current_user->ID == $user_id));
  }
  
  function is_logged_in_and_an_admin()
  {
    return (WafpUtils::is_user_logged_in() and WafpUtils::is_admin());
  }
  
  function is_logged_in_and_a_subscriber()
  {
    return (WafpUtils::is_user_logged_in() and WafpUtils::is_subscriber());
  }
  
  function is_admin()
  {
    return current_user_can('administrator');
  }

  function is_subscriber()
  {
    return (current_user_can('subscriber') and !current_user_can('contributor'));
  }
  
  function array_to_string($my_array, $debug=false, $level=0)
  {
    if(is_array($my_array))
    {
      $my_string = '';

      if($level<=0 and $debug)
        $my_string .= "<pre>";

      foreach($my_array as $my_key => $my_value)
      {
        for($i=0; $i<$level; $i++)
          $my_string .= "    ";

        $my_string .= "{$my_key} => " . WafpUtils::array_to_string($my_value, $debug, $level+1) . "\n";
      }

      if($level<=0 and $debug)
        $my_string .= "</pre>";

      return $my_string;
    }
    else if(is_string($my_array))
      return $my_array;
    else
      return '';
  }

  function object_to_string($object)
  {
    ob_start();
    print_r($object);
    $obj_string = ob_get_contents();
    ob_end_clean();
    return $obj_string;
  }
  
  function replace_text_variables($text, $variables)
  {
    $patterns = array();
    $replacements = array();
    
    foreach($variables as $var_key => $var_val)
    {
      $patterns[] = '/\{\$' . preg_quote( $var_key, '/' ) . '\}/';
      $replacements[] = preg_replace( '/\$/', '\\\$', $var_val ); // $'s must be escaped for some reason
    }
    
    $preliminary_text = preg_replace( $patterns, $replacements, $text );
    
    // Clean up any failed matches
    return preg_replace( '/\{\$.*?\}/', '', $preliminary_text );
  }
  
  function with_default($variable, $default)
  {
    if(isset($variable))
    {
      if(is_numeric($variable))
        return $variable;
      elseif(!empty($variable))
        return $variable;
    }
    
    return $default;
  }

/* PLUGGABLE FUNCTIONS AS TO NOT STEP ON OTHER PLUGINS' CODE */
  function get_currentuserinfo()
  {
    WafpUtils::_include_pluggables('get_currentuserinfo');
    return get_currentuserinfo();
  }

  function get_userdata($id)
  {
    WafpUtils::_include_pluggables('get_userdata');
    return get_userdata($id);
  }

  function &get_userdatabylogin($screenname)
  {
    WafpUtils::_include_pluggables('get_userdatabylogin');
    return get_userdatabylogin($screenname);
  }

  function wp_mail($recipient, $subject, $message, $header)
  {
    WafpUtils::_include_pluggables('wp_mail');
    return wp_mail($recipient, $subject, $message, $header);
  }

  function is_user_logged_in()
  {
    WafpUtils::_include_pluggables('is_user_logged_in');
    return is_user_logged_in();
  }

  function get_avatar( $id, $size )
  {
    WafpUtils::_include_pluggables('get_avatar');
    return get_avatar( $id, $size );
  }
  
  function wp_hash_password( $password_str )
  {
    WafpUtils::_include_pluggables('wp_hash_password');
    return wp_hash_password( $password_str );
  }
  
  function wp_generate_password( $length, $special_chars )
  {
    WafpUtils::_include_pluggables('wp_generate_password');
    return wp_generate_password( $length, $special_chars );
  }
  
  function wp_redirect( $location, $status=302 )
  {
    WafpUtils::_include_pluggables('wp_redirect');
    return wp_redirect( $location, $status );
  }

  function wp_salt( $scheme='auth' )
  {
    WafpUtils::_include_pluggables('wp_salt');
    return wp_salt( $scheme );
  }
  
  function _include_pluggables($function_name)
  {
    if(!function_exists($function_name))
      require_once(ABSPATH . WPINC . '/pluggable.php');
  }
}
?>
