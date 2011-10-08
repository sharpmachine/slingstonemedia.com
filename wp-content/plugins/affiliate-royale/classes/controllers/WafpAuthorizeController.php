<?php
/** This is a special controller that handles all of the
  * Authorize.Net specific functions for the Affiliate Program.
  */
class WafpAuthorizeController
{
  public $hash_key;
  private $hash_key_store = 'wafp_authorize_md5_store';

  private $debug;

  function __construct($debug=false)
  {
    $this->debug = $debug;
    if(!$md5 = get_option($this->hash_key_store))
    {
      $md5 = substr(md5(WafpUtils::wp_salt() . time()), 0, 20);
      update_option($this->hash_key_store, $md5);
    }

    $this->hash_key = $md5;

    add_action('wafp_process_route', array(&$this,'listener'));
  }

  function listener()
  {
    if( isset($_REQUEST['plugin']) and $_REQUEST['plugin'] == 'wafp' and
        isset($_REQUEST['controller']) and $_REQUEST['controller'] == 'authorize' )
    {
      if( isset($_REQUEST['action']) and $_REQUEST['action'] == 'silent_post' )
      {
        $_POST = stripslashes_deep($_POST);

        if( $this->_validate_message() )
          $this->_process_message(); 

        exit;
      }
      else if( isset($_REQUEST['action']) and $_REQUEST['action'] == 'test_silent_post' )
      {
        echo "<pre>"; print_r($this->_test_silent_post()); echo "</pre>";
        exit;
      }
    }
  }

  function get_silent_post_url()
  {
    return WAFP_SCRIPT_URL . "&controller=authorize&action=silent_post";
  }

  function _validate_message()
  {
    // Validate ARB response
    $hash_input = $this->hash_key.$_REQUEST['x_trans_id'].$_REQUEST['x_amount'];
    $hash = strtoupper( md5( $hash_input ) );

    return ($hash == $_REQUEST['x_MD5_Hash']);
  }

  function _process_message()
  {
    global $wafp_options;
    $this->_email_status("Authorize.NET Processing\n\n" . WafpUtils::object_to_string( $_POST ) . "\n");

    if($_POST['x_response_code'] and isset($_POST['x_subscription_id']) and !empty($_POST['x_subscription_id']))
    {
      $subscr = WafpSubscription::get_one_by_subscr_id($_POST['x_subscription_id']);

      if(!$subscr or !$subscr->affiliate_id)
        return false; // we can't find the affiliate then there's no point to the rest of this function

      $custom_array = array( 'aff_id' => $subscr->affiliate_id,
                             'ip_addr' => $subscr->ip_addr );

      if( strtoupper($_POST['x_type']) == 'VOID' or
          strtoupper($_POST['x_type']) == 'CREDIT' )
      {
        $og_transaction = WafpTransaction::get_one_by_trans_num( $_POST['x_trans_id'] );
        WafpTransaction::update_refund( $og_transaction->id, abs($_POST['x_amount']) );
      }
      else if( strtoupper($_POST['x_type']) == 'AUTH_CAPTURE' or
               strtoupper($_POST['x_type']) == 'CAPTURE_ONLY' )
      {
        $existing_transaction = WafpTransaction::get_one_by_trans_num($_POST['x_trans_id']);

        // If we've already recorded this transaction then don't bother
        if($existing_transaction)
          return;

        $affiliate_id = $custom_array['aff_id'];
        $affiliate = new WafpUser($affiliate_id);
        $this->_email_status("Affiliate Info:\nID:{$affiliate_id}" . WafpUtils::object_to_string($affiliate->userdata) . "\n");

        if($affiliate->is_affiliate()) // is this a valid affiliate?
        {
          $affiliate_login      = $affiliate->get_field('user_login');
          $affiliate_email      = $affiliate->get_field('user_email');
          $affiliate_first_name = $affiliate->get_first_name();
          $affiliate_last_name  = $affiliate->get_last_name();
          $item_name            = $subscr->subscription->post_title;
          $trans_num            = $_POST['x_trans_id'];
          $trans_type           = $_POST['x_type'];
          $payment_status       = $_POST['x_response_code'];
          $commission_percent   = $affiliate->get_commission_percentages_total(true);
          $remote_ip_addr       = isset($custom_array['ip_addr'])?$custom_array['ip_addr']:'';
          $response             = WafpUtils::object_to_string($_POST);
          $payment_amount       = $_POST['x_amount'];
          $commission_amount    = $affiliate->calculate_commissions_total( $_POST['x_amount'], true );
          $customer_name        = $_POST['x_first_name'] . ' ' . $_POST['x_last_name'];
          $customer_email       = $_POST['x_email'];
          $subscription_id      = isset($_POST['x_subscription_id'])?$_POST['x_subscription_id']:NULL;
          $transaction_type     = (isset($_POST['x_subscription_id']) and !empty($_POST['x_subscription_id']))?__("Subscription Payment", 'affiliate-royale'):__("Standard Payment", 'affiliate-royale');
          $wafp_subscr_id       = $subscr->subscription->ID;
          $wafp_subscr_paynum   = $_POST['x_subscription_paynum'];

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
                                     $trans_num, 'commission', $payment_status,
                                     $response, $affiliate_id, $customer_name,
                                     $customer_email, $remote_ip_addr, $commission_percent,
                                     $wafp_subscr_id, $wafp_subscr_paynum );

            // Handled in WafpTransaction::create now
            //$params = compact( 'affiliate_id', 'affiliate_login', 'affiliate_email',
            //                   'affiliate_first_name', 'affiliate_last_name', 'item_name',
            //                   'trans_num', 'trans_type', 'payment_status',
            //                   'commission_percent', 'remote_ip_addr', 'response',
            //                   'payment_amount', 'commission_amount', 'customer_name',
            //                   'customer_email', 'subscription_id', 'transaction_type',
            //                   'wafp_subscr_id', 'wafp_subscr_paynum');

            //WafpUtils::send_admin_sale_notification($params);
            //WafpUtils::send_affiliate_sale_notification($params);
          }
          else
          {
            WafpTransaction::create( $item_name, $payment_amount, '0.00',
                                     $trans_num, 'no_commission', $payment_status,
                                     $response, '', $customer_name,
                                     $customer_email, $remote_ip_addr, '',
                                     '' );
          }
        }
      }
    }
  }

  function _test_silent_post()
  {
    if($this->debug)
      return;

    // comment the return out to be able to test this stuff
    $trans_num = rand(10000,32768);
    $amount = rand(10,20) . "." . rand(10,99);

    $hash_input = $this->hash_key.$trans_num.$amount;
    $hash = strtoupper( md5( $hash_input ) );

    $silent_post_array = array(
      "x_response_code" => 1,
      "x_response_reason_code" => 1,
      "x_response_reason_text" => "This transaction has been approved.",
      "x_avs_code" => "Y",
      "x_auth_code" => "BR6D0Q",
      "x_trans_id" => $trans_num,
      "x_method" => "CC",
      "x_card_type" => "Visa",
      "x_account_number" => "XXXX0027",
      "x_first_name" => "Dirtbag",
      "x_last_name" => "Malone",
      "x_company" => "",
      "x_address" => "",
      "x_city" => "",
      "x_state" => "",
      "x_zip" => "",
      "x_country" => "",
      "x_phone" => "",
      "x_fax" => "",
      "x_email" => "",
      "x_invoice_num" => "",
      "x_description" => "",
      "x_type" => "auth_capture",
      "x_cust_id" => "",
      "x_ship_to_first_name" => "",
      "x_ship_to_last_name" => "",
      "x_ship_to_company" => "",
      "x_ship_to_address" => "",
      "x_ship_to_city" => "",
      "x_ship_to_state" => "",
      "x_ship_to_zip" => "",
      "x_ship_to_country" => "",
      "x_amount" => $amount,
      "x_tax" => 0.00,
      "x_duty" => 0.00,
      "x_freight" => 0.00,
      "x_tax_exempt" => "FALSE",
      "x_po_num" => "",
      "x_MD5_Hash" => $hash,
      "x_cvv2_resp_code" => "",
      "x_cavv_response" => 2,
      "x_test_request" => "false",
      "x_subscription_id" => 923027,
      "x_subscription_paynum" => 2
    );

    if( !class_exists( 'WP_Http' ) )
      include_once( ABSPATH . WPINC. '/class-http.php' );

    $request = new WP_Http();
    $result = $request->request( $this->get_silent_post_url(), array( 'method' => 'POST', 'body' => $silent_post_array ) );

    return $result;
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
      $subject   = sprintf( __("[%s] Authorize.Net Debug Email", 'affiliate-royale'), $wafp_blogname);

      WafpUtils::wp_mail($recipient, $subject, $message, $header);
    }
  }
}
?>
