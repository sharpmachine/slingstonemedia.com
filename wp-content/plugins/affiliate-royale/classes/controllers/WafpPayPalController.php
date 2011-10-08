<?php
/** This is a special controller that handles all of the PayPal specific
  * functions for the Affiliate Program.
  */
class WafpPayPalController {

  var $sandbox;
  var $debug;
  var $url = array(
    'sandbox'  => 'https://www.sandbox.paypal.com/webscr',
    'live'    => 'https://www.paypal.com/webscr'
  );

  function WafpPayPalController($sandbox=false,$debug=false)
  {
    $this->sandbox = $sandbox?'sandbox':'live';
    $this->debug = $debug;

    add_action('wafp_process_route', array(&$this,'listener'));
    
    // If Wishlist Member is installed, hook into it
    add_action('wlmem_paypal_display_custom_var', array(&$this,'wl_custom_instructions'));
    add_action('wlmem_paypal_ipn_response', array(&$this,'wl_process_ipn'));
  }

  function wl_custom_instructions()
  {
?>
<li><?php _e('Make Sure You Uncheck "Save Button at PayPal" in Step 2', 'affiliate-royale'); ?></li>
<li><?php _e('Make Sure "Add advanced variables" is checked in Step 3 and add the following text into the "Advanced Variables" text area:', 'affiliate-royale'); ?><br/>
<pre><strong>custom=[wafp_custom_args]</strong></pre>
</li>
<li><?php _e('Click "Create Button"', 'affiliate-royale'); ?></li>
<li><?php _e('Click "Remove code protection"', 'affiliate-royale'); ?></li>
<?php
  }

  function wl_process_ipn()
  {
    $this->_email_status("Got Wishlist Member IPN:\n" . WafpUtils::array_to_string($_POST, true) . "\n");

    $this->_process_message();
  }

  function listener()
  {
    if( isset($_REQUEST['plugin']) and $_REQUEST['plugin'] == 'wafp' and
        isset($_REQUEST['controller']) and $_REQUEST['controller'] == 'paypal' and
        isset($_REQUEST['action']) and $_REQUEST['action'] == 'ipn' )
    {
      $_POST = stripslashes_deep($_POST);

      if( $this->_validate_message() )
        $this->_process_message(); 

      exit;
    }
  }

  function _process_message()
  {
    global $wafp_options;

    $subscr = false;

    // We don't necessarily rely on the custom variable being set for subscr_payments --
    // if it isn't set then we'll attempt to pull the affiliate id & remote ip address from
    // the first subscr_payment for this specific subscription...
    if(!isset($_POST['custom']) and isset($_POST['subscr_id']) and !empty($_POST['subscr_id']))
    {
      $subscr = WafpSubscription::get_one_by_subscr_id($_POST['subscr_id']);

      if(!$subscr or !$subscr->affiliate_id)
        return false; // we can't find the affiliate then there's no point to the rest of this function

      $custom_array = array( 'aff_id' => $subscr->affiliate_id,
                             'ip_addr' => $subscr->ip_addr );
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
        $existing_transaction = WafpTransaction::get_one_by_trans_num($_POST['txn_id']);

        // If we've already recorded this transaction then don't bother
        if($existing_transaction)
          return;

        $affiliate_id = $custom_array['aff_id'];
        $affiliate = new WafpUser($affiliate_id);
        $this->_email_status("Affiliate Info:\nID:{$affiliate_id}" . WafpUtils::array_to_string($affiliate->userdata) . "\n");

        if($affiliate->is_affiliate()) // is this a valid affiliate?
        {
          $affiliate_login      = $affiliate->get_field('user_login');
          $affiliate_email      = $affiliate->get_field('user_email');
          $affiliate_first_name = $affiliate->get_first_name();
          $affiliate_last_name  = $affiliate->get_last_name();
          $item_name            = $_POST['item_name'];
          $trans_num            = $_POST['txn_id'];
          $trans_type           = $_POST['txn_type'];
          $payment_status       = $_POST['payment_status'];
          $commission_percent   = $affiliate->get_commission_percentages_total(true);
          $remote_ip_addr       = isset($custom_array['ip_addr'])?$custom_array['ip_addr']:'';
          $response             = WafpUtils::object_to_string($_POST);
          $payment_amount       = $_POST['mc_gross'];
          $commission_amount    = $affiliate->calculate_commissions_total( $_POST['mc_gross'], true );
          $customer_name        = $_POST['first_name'] . ' ' . $_POST['last_name'];
          $customer_email       = $_POST['payer_email'];
          $subscription_id      = isset($_POST['subscr_id'])?$_POST['subscr_id']:NULL;
          $transaction_type     = (isset($_POST['subscr_id']) and !empty($_POST['subscr_id']))?__("Subscription Payment", 'affiliate-royale'):__("Standard Payment", 'affiliate-royale');

          // Create a subscription if it's set
          if( !empty($affiliate_id) and $affiliate_id and
              !empty($subscription_id) and $subscription_id )
          {
            if( !( $wafp_subscr = WafpSubscription::subscription_exists($subscription_id) ) )
              $wafp_subscr_id = WafpSubscription::create( $subscription_id, 'paypal', $affiliate_id, $item_name, $remote_ip_addr );
            else
              $wafp_subscr_id = $wafp_subscr->subscription->ID;
          }

          if( !is_null($subscription_id) and
              !empty($subscription_id) and 
              $subscription_id )
          {
            $subscription_already_paid = WafpTransaction::get_one_by_subscription_id($subscription_id);
            $pay_affiliate = $affiliate->pay_commission($subscription_already_paid);
          }
          else
            $pay_affiliate = true;

          $wafp_subscr_paynum = ($wafp_subscr_id?(WafpTransaction::get_num_trans_by_subscr_id($wafp_subscr_id) + 1):0);

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
            //                   'wafp_subscr_id', 'wafp_subscr_paynum' );
            //
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

    $this->_email_status("PayPal IPN Processing\n" . WafpUtils::array_to_string($_POST, true) . "\n");

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
      $subject       = sprintf( __("[%s] PayPal Debug Email", 'affiliate-royale'), $wafp_blogname);

      WafpUtils::wp_mail($recipient, $subject, $message, $header);
    }
  }
}
