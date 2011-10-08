<?php
class WafpTransaction
{
  /** STATIC CRUD METHODS **/
  function create( $item_name, $sale_amount, $commission_amount, $trans_num, $type, $status, $response, $affiliate_id, $cust_name, $cust_email, $ip_addr, $commission_percentage, $subscr_id=0, $subscr_paynum=0 )
  {
    global $wafp_db;
    $args = compact( 'item_name', 'sale_amount', 'commission_amount', 'trans_num', 'type', 'status', 'response', 'affiliate_id', 'ip_addr', 'commission_percentage', 'cust_name', 'cust_email', 'subscr_id', 'subscr_paynum' );
    $transaction_id = $wafp_db->create_record($wafp_db->transactions, $args);
        
    if(!empty($affiliate_id) and is_numeric($affiliate_id) and $commission_amount > 0.00)
    {
      $affiliate = new WafpUser($affiliate_id);
      $affiliates = $affiliate->get_affiliates(true);
      
      // Record commission for each affiliate who's getting some
      foreach($affiliates as $level => $aff)
      {
        $curr_percentage = ( $aff->is_affiliate() ? $aff->get_commission_percentage($level) : 0.0 );
        $curr_amount = ( $aff->is_affiliate() ? $aff->calculate_commission($sale_amount, $level) : 0.0 );
        
        WafpCommission::create( $aff->get_id(), $transaction_id, $level, $curr_percentage, $curr_amount );
      }
      
      $trans_type = $type;
      $transaction_type = (empty($subscr_id)?'Payment':'Subscription Payment');
      $payment_status = $status;
      $remote_ip_addr = $ip_addr;
      $payment_amount = $sale_amount;
      $customer_name = $cust_name;
      $customer_email = $cust_email;
      
      $params = compact( 'item_name', 'trans_num', 'trans_type', 'payment_status',
                         'remote_ip_addr', 'response', 'payment_amount', 'customer_name',
                         'customer_email', 'transaction_type' );
      
      WafpUtils::send_admin_sale_notification( $params, $affiliates );
      WafpUtils::send_affiliate_sale_notifications( $params, $affiliates );
    }

    return $transaction_id;
  }

  function update( $id, $item_name, $sale_amount, $commission_amount, $trans_num, $type, $status, $response, $affiliate_id, $cust_name, $cust_email, $ip_addr, $refund_amount, $commission_percentage, $subscr_id=0, $subscr_paynum=0 )
  {
    global $wafp_db;
    $args = compact( 'item_name', 'sale_amount', 'commission_amount', 'trans_num', 'type', 'status', 'response', 'affiliate_id', 'ip_addr', 'refund_amount', 'commission_percentage', 'cust_name', 'cust_email', 'subscr_id', 'subscr_paynum' );
    return $wafp_db->update_record($wafp_db->transactions, $id, $args);
  }
  
  function update_refund( $id, $refund_amount, $correction_amount="" )
  {
    global $wafp_db;
    
    if(!isset($correction_amount) or empty($correction_amount))
    {
      $record = WafpTransaction::get_one($id);
      
      if($record)
        $correction_amount = (float)( (float)$refund_amount * ( (float)$record->commission_percentage / 100.0 ) );
    }
    
    $commissions = WafpCommission::get_all_by_transaction_id($id);
    foreach($commissions as $commission)
      WafpCommission::update_refund( $commission->id, $refund_amount );

    $args = compact( 'refund_amount', 'correction_amount' );
    return $wafp_db->update_record($wafp_db->transactions, $id, $args);
  }
  
  function delete( $id )
  {
    global $wafp_db;

    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->transactions, $args);
  }
  
  function delete_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    $args = compact( 'affiliate_id' );
    return $wafp_db->delete_records($wafp_db->transactions, $args);
  }
  
  function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->transactions, $args);
  }

  function get_one_by_trans_num($trans_num)
  {
    global $wafp_db;
    $args = compact( 'trans_num' );
    return $wafp_db->get_one_record($wafp_db->transactions, $args);
  }

  function get_count()
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->transactions, array('type' => 'commission'));
  }
  
  function get_count_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->transactions, array('affiliate_id' => $affiliate_id, 'type' => 'commission'));
  }
  
  function &get_all($order_by='', $limit='')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->transactions, array('type' => 'commission'), $order_by, $limit);
  }
  
  function &get_all_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->transactions, array('affiliate_id' => $affiliate_id, 'type' => 'commission'), $order_by, $limit);
  }
  
  function get_all_ids_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wpdb;
    $query = "SELECT id FROM {$wafp_db->transactions} WHERE type='commission' AND affiliate_id=%d {$order_by}{$limit}";
    $query = $wpdb->prepare($query, $affiliate_id);
    return $wpdb->get_col($query);
  }

  function &get_all_objects_by_affiliate_id( $affiliate_id, $order_by='', $limit='')
  {
    $all_records =& WafpTransaction::get_all_by_affiliate_id($affiliate_id, $order_by, $limit);
  
    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] =& WafpTransaction::get_stored_object($record->id);
  
    return $my_objects;
  }
  
  function &get_all_objects($order_by='', $limit='')
  {
    $all_records =& WafpTransaction::get_all($order_by, $limit);
  
    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] =& WafpTransaction::get_stored_object($record->id);
  
    return $my_objects;
  }

  function &get_stored_object($id)
  { 
    static $my_objects;

    if( !isset($my_objects) )
      $my_objects = array();

    if( !isset($my_objects[$id]) or
        empty($my_objects[$id]) or
        !is_object(&$my_objects[$id]) )
      $my_objects[$id] =& new WafpTransaction($id);
    
    return $my_objects[$id];
  }

  function get_num_trans_by_subscr_id($subscr_id)
  {
    global $wpdb, $wafp_db;

    $sql = "SELECT COUNT(*) {$wafp_db->transactions} WHERE subscr_id=%d";
    $sql = $wpdb->prepare($sql, $subscr_id);

    return $wpdb->get_var($sql);
  }

  function track( $amount, $order_id, $product_id='', $user_id='', $subscription_id='', $response='' )
  {
    global $wafp_options, $current_user;
    
    $transaction_id = false; // by default this is false
    $recurring_purchase = false;
    
    $affiliate_id = $_COOKIE['wafp_click'];
    $wafp_subscr_id = 0;

    // Create a subscription if it's set
    if( !empty($affiliate_id) and $affiliate_id and
        !empty($subscription_id) and $subscription_id )
    {
      if( !($wafp_subscr = WafpSubscription::subscription_exists($subscription_id) ) ) {
        $wafp_subscr_id = WafpSubscription::create( $subscription_id, $wafp_options->integration, $affiliate_id, $product_id, $_SERVER['REMOTE_ADDR'] );
        $recurring_purchase = false; // This is the first purchase of a subscription
      }
      else {
        $wafp_subscr_id = $wafp_subscr->subscription->ID;
        $recurring_purchase = true; // This is the first purchase of a subscription
      }
    }
    else if( !empty( $subscription_id ) and $subscription_id and 
             ( $wafp_subscr = WafpSubscription::subscription_exists( $subscription_id ) ) )
    {
      // If we don't have the affiliate id yet, let's try
      // to determine it from the subscription object
      if($wafp_subscr->affiliate_id and is_numeric($wafp_subscr->affiliate_id))
        $affiliate_id = $wafp_subscr->affiliate_id;
      
      $recurring_purchase = true;
    }
    
    //need an amount
    if(is_null($amount) or empty($amount))
      return;
    
    //need an order_id/trans_num
    if(is_null($order_id) or empty($order_id))
      return;
    
    $existing_transaction = WafpTransaction::get_one_by_trans_num($order_id);
    
    // If we've already recorded this transaction then don't bother
    if($existing_transaction)
      return;
    
    // Override affiliate id with stored affiliate id or store the
    // affiliate_id with the usermeta if no stored meta is found
    if(isset($user_id) and !empty($user_id))
    {
      //TODO: Move this get_user_meta to the WafpUser object
      $stored_aff_id = get_user_meta($user_id, 'wafp-affiliate-referrer', true);
    
      // if get_usermeta returned something -- if not attempt to store it from cookie
      if($stored_aff_id)
        $affiliate_id = $stored_aff_id;
      else
      {
        if( $affiliate_id and is_numeric( $affiliate_id ) )
          update_user_meta($user_id, 'wafp-affiliate-referrer', $affiliate_id);
      }
    }
    
    // Short circuit this and don't pay commission if the user's id is == to the affiliate id
    get_currentuserinfo();
    if( isset($current_user) and isset($current_user->ID) )
    {
      if( $current_user->ID == $affiliate_id )
        unset($affiliate_id); // Transaction isn't eligible for commissions
    }
    
    if( $affiliate_id and is_numeric( $affiliate_id ) )
      $affiliate = new WafpUser($affiliate_id);
    
    if( isset($affiliate) and is_a($affiliate, 'WafpUser') and $affiliate->pay_commission($recurring_purchase) )
    {
      // Make sure the user is an affiliate ...
      $customer = new WafpUser($user_id);
      $affiliates = $affiliate->get_affiliates(true);

      $commission_percentage = $affiliate->get_commission_percentages_total(true);
      $commission_amount = $affiliate->calculate_commissions_total($amount,true);

      if((float)$commission_amount > 0.0)
      {
        $item_name = $product_id;
        $trans_num = $order_id;
        $trans_type = 'commission';
        $payment_status = 'complete';
        $payment_amount = (float)$amount;
        $customer_name = $customer->get_full_name();
        $customer_email = $customer->get_field('user_email');
        $wafp_subscr_paynum = ((!$wafp_subscr_id)?0:1);
    
        $transaction_id = WafpTransaction::create( $product_id,
                                                   $amount,
                                                   $commission_amount,
                                                   $order_id,
                                                   $trans_type,
                                                   $payment_status,
                                                   $response,
                                                   $affiliate_id,
                                                   '', '', 
                                                   $_SERVER['REMOTE_ADDR'],
                                                   $commission_percentage,
                                                   $wafp_subscr_id,
                                                   $wafp_subscr_paynum );
      }
      else
        $transaction_id = WafpTransaction::create( $product_id,
                                                   $amount,
                                                   '0.00',
                                                   $order_id,
                                                   'no_commission',
                                                   'complete',
                                                   $response,
                                                   '', '', '',
                                                   $_SERVER['REMOTE_ADDR'],
                                                   '' );
    }
    else
    {
      $transaction_id = WafpTransaction::create( $product_id,
                                                 $amount,
                                                 '0.00',
                                                 $order_id,
                                                 'no_commission',
                                                 'complete',
                                                 $response,
                                                 '', '', '',
                                                 $_SERVER['REMOTE_ADDR'],
                                                 '' );
    }
    
    return $transaction_id;
  }
  
  /** INSTANCE VARIABLES & METHODS **/
  var $rec;

  function WafpTransaction($id)
  {
    $this->rec = WafpTransaction::get_one($id);
  }
}
?>
