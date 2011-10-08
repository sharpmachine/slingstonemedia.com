<?php
/** This is a special controller that handles all of the
  * PayPal specific functions for Affiliate Royale.
  */
class WafpPayPal extends WafpIntegration

  private $listener_slug;
  private $listener_slug_store = 'wafp_paypal_ipn_slug';
  private $sandbox;
  private $debug;
  private $url = array(
    'sandbox'  => 'https://www.sandbox.paypal.com/webscr',
    'live'    => 'https://www.paypal.com/webscr'
  );

  function __construct($sandbox=false,$debug=false)
  {
    if(!$slug = get_option($this->listener_slug_store))
    {
      $slug = 'ipn' . substr(md5(WafpUtils::wp_salt()), 3, 5); 
      update_option($this->listener_slug_store, $slug);
    }
    $this->listener_slug = $slug;
    $this->sandbox = $sandbox?'sandbox':'live';
    $this->debug = $debug;

    add_action('init', array(&$this,'listener'));
    add_action('wlmem_paypal_display_custom_var', array(&$this,'wl_custom_instructions'));
    add_action('wlmem_paypal_ipn_response', array(&$this,'wl_process_ipn'));
    
    parent::__construct('WafpPayPal');
  }

  function listener()
  {
    $request_uri = $_SERVER['REQUEST_URI'];
    if( $request_uri == '/' . $this->listener_slug )
    {
      $_POST = stripslashes_deep($_POST);

      // Try to validate the response to make sure it's from PayPal
      if ($this->_validate_message())
      {
        // If the message validated, process it.
        $this->_process_message();
      }
      // Stop WordPress entirely
      exit;
    }
  }

  function _process_message()
  {
    global $wafp_options;
    $this->_email_status("PayPal IPN Processing\n" . WafpUtils::array_to_string($_POST, true) . "\n");

    // We don't necessarily rely on the custom variable being set for subscr_payments --
    // if it isn't set then we'll attempt to pull the affiliate id & remote ip address from
    // the first subscr_payment for this specific subscription...
    if(!isset($_POST['custom']) and isset($_POST['subscr_id']) and !empty($_POST['subscr_id']))
    {
      $first_subscr_transaction = WafpTransaction::get_first_subscr_transaction($_POST['subscr_id']);

      if(!$first_subscr_transaction or !$first_subscr_transaction->affiliate_id)
        return false; // we can't find the affiliate then there's no point to the rest of this function

      $custom_array = array( 'aff_id' => $first_subscr_transaction->affiliate_id,
                             'ip_addr' => $first_subscr_transaction->ip_addr );
    }
    else
      $custom_array = wp_parse_args($_POST['custom']);

    if(isset($_POST['payment_status']) and isset($custom_array['aff_id']))
    {
      if($_POST['payment_status'] == 'Refunded')
      {
        $og_transaction = WafpTransaction::get_one_by_trans_num( $_POST['parent_txn_id'] );
        WafpTransaction::update_refund( $og_transaction->id, abs($_POST['mc_gross']) );
      }
      else if($_POST['payment_status'] == 'Completed')
      {
        $affiliate_id = $custom_array['aff_id'];
        $affiliate = new WafpUser($affiliate_id);
        $this->_email_status("Affiliate Info:\nID:{$affiliate_id}" . WafpUtils::array_to_string($affiliate->userdata) . "\n");


        if($affiliate and $affiliate->is_affiliate()) // is this a valid affiliate?
        {
          $payment_amount       = $_POST['mc_gross'];
          
          // Account for the user override commission percentage -- if there is one
          if($tmp_percent = get_user_meta($affiliate_id, 'wafp-commission-override', true))
            $commission_percent = (float)$tmp_percent;
          else
            $commission_percent = (float)$wafp_options->commission;

          $commission_amount    = (float)$payment_amount * $commission_percent / 100.00;

          $affiliate_login      = $affiliate->get_field('user_login');
          $affiliate_email      = $affiliate->get_field('user_email');
          $affiliate_first_name = $affiliate->get_first_name();
          $affiliate_last_name  = $affiliate->get_last_name();
          $item_name            = $_POST['item_name'];
          $trans_num            = $_POST['txn_id'];
          $trans_type           = $_POST['txn_type'];
          $payment_status       = $_POST['payment_status'];
          $remote_ip_addr       = isset($custom_array['ip_addr'])?$custom_array['ip_addr']:'';
          $response             = WafpUtils::array_to_string($_POST);
          $customer_name        = $_POST['first_name'] . ' ' . $_POST['last_name'];
          $customer_email       = $_POST['payer_email'];
          $subscription_id      = isset($_POST['subscr_id'])?$_POST['subscr_id']:NULL;
          $transaction_type     = (isset($_POST['subscr_id']) and !empty($_POST['subscr_id']))?__("Subscription Payment", 'affiliate-royale'):__("Standard Payment", 'affiliate-royale');

          if( !is_null($subscription_id) and
              !empty($subscription_id) and 
              $subscription_id )
          {
            $subscription_already_paid = WafpTransaction::get_one_by_subscription_id($subscription_id);
            $pay_affiliate = $affiliate->pay_commission($subscription_already_paid);
          }
          else
            $pay_affiliate = true;

          if($pay_affiliate)
          {
            WafpTransaction::create( $item_name, $payment_amount, $commission_amount,
                                     $trans_num, $trans_type, $payment_status,
                                     $response, $affiliate_id, $customer_name,
                                     $customer_email, $remote_ip_addr, $subscription_id,
                                     $commission_percent );

            $params = compact( 'affiliate_id', 'affiliate_login', 'affiliate_email',
                               'affiliate_first_name', 'affiliate_last_name', 'item_name',
                               'trans_num', 'trans_type', 'payment_status',
                               'commission_percent', 'remote_ip_addr', 'response',
                               'payment_amount', 'commission_amount', 'customer_name',
                               'customer_email', 'subscription_id', 'transaction_type' );

            WafpUtils::send_admin_sale_notification($params);
            WafpUtils::send_affiliate_sale_notification($params);
          }
          else
            WafpTransaction::create( $product_id,
                                     $amount,
                                     '0.00',
                                     $order_id,
                                     'no_commission',
                                     'complete',
                                     $response,
                                     '', '', '', '',
                                     $_SERVER['REMOTE_ADDR'],
                                     '',
                                     '' );
        }
        else
        {
          WafpTransaction::create( $product_id,
                                   $amount,
                                   '0.00',
                                   $order_id,
                                   'no_commission',
                                   'complete',
                                   $response,
                                   '', '', '', '',
                                   $_SERVER['REMOTE_ADDR'],
                                   '',
                                   '' );
        }
      }
    }
  }

  /**
   * Validate the message by checking with PayPal to make sure they really
   * sent it
   */
  function _validate_message()
  {
    // Set the command that is used to validate the message
    $_POST['cmd'] = "_notify-validate";

    // We need to send the message back to PayPal just as we received it
    $params = array(
      'body'    => $_POST,
      'sslverify' => false,
      'timeout'   => 30,
    );

    if ( !function_exists('wp_remote_post') )
      require_once('http.php');

    $resp = wp_remote_post( $this->url[$this->sandbox], $params );

    // Put the $_POST data back to how it was so we can pass it to the action
    unset($_POST['cmd']);

    // If the response was valid, check to see if the request was valid
    if ( !is_wp_error($resp) and
         $resp['response']['code'] >= 200 and
         $resp['response']['code'] < 300 and
         (strcmp( $resp['body'], "VERIFIED") == 0))
      return true;

    return false;
  }

  function _email_status($message)
  {
    global $wafp_blogname;

    if($this->debug)
    {
      // Send notification email to admin user (to and from the admin user)
      $recipient = get_option('admin_email'); //senders name
      $header    = "From: {$recipient}"; //optional headerfields

      /* translators: In this string, %s is the Blog Name/Title */
      $subject       = sprintf( __("[%s] PayPal Debug Email",'affiliate-royale'), $wafp_blogname);

      WafpUtils::wp_mail($recipient, $subject, $message, $header);
    }
  }
}
