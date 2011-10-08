<?php
class WafpPayment
{
  /** STATIC CRUD METHODS **/
  function create( $affiliate_id, $amount )
  {
    global $wafp_db;
    $args = compact( 'affiliate_id', 'amount' );
    return $wafp_db->create_record($wafp_db->payments, $args);
  }

  function update( $id, $affiliate_id, $amount )
  {
    global $wafp_db;
    $args = compact( 'affiliate_id', 'amount' );
    return $wafp_db->update_record($wafp_db->payments, $id, $args);
  }
  
  function update_transactions($payment_id, $affiliate_id, $period)
  {
    global $wpdb, $wafp_db;

    $num_days_in_month = (int)(date( 't', $period )) - 1;
    $seconds_in_month  = 60*60*24*(int)$num_days_in_month;

    $day_start = date( 'Y-m-d 00:00:00', $period );
    $day_end   = date( 'Y-m-d 23:59:59', ( $period + $seconds_in_month ) );

    $query_str = "UPDATE {$wafp_db->commissions} SET payment_id=%d WHERE affiliate_id=%d AND payment_id=0 AND created_at <= %s";
    $query = $wpdb->prepare( $query_str, $payment_id, $affiliate_id, $day_end );
    
    return $wpdb->query($query);
  }
  
  function delete( $id )
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->payments, $args);
  }
  
  function delete_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    $args = compact( 'affiliate_id' );
    return $wafp_db->delete_records($wafp_db->payments, $args);
  }
  
  function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->payments, $args);
  }
  
  function get_count($id)
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->payments);
  }
  
  function get_count_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->payments, compact('affiliate_id'));
  }
  
  function &get_all($order_by='', $limit='')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->payments, array(), $order_by, $limit);
  }

  function &get_all_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wafp_db;
    $args = compact('affiliate_id');
    return $wafp_db->get_records($wafp_db->payments, $args, $order_by, $limit);
  }

  function get_all_ids_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wpdb;
    $query = "SELECT id FROM {$wafp_db->payments} WHERE affiliate_id=%d {$order_by}{$limit}";
    $query = $wpdb->prepare($query, $affiliate_id);
    return $wpdb->get_col($query);
  }

  function &get_all_objects_by_affiliate_id( $affiliate_id, $order_by='', $limit='')
  {
    $all_records =& WafpPayment::get_all_by_affiliate_id($affiliate_id, $order_by, $limit);
  
    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] =& WafpPayment::get_stored_object($record->id);
  
    return $my_objects;
  }
  
  function &get_all_objects($order_by='', $limit='')
  {
    $all_records =& WafpPayment::get_all($order_by, $limit);
  
    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] =& WafpPayment::get_stored_object($record->id);
  
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
      $my_objects[$id] =& new WafpPayment($id);
    
    return $my_objects[$id];
  }
  
  /** INSTANCE VARIABLES & METHODS **/
  var $rec;

  function WafpPayment($id)
  {
    $this->rec = WafpPayment::get_one($id);
  }
}
?>
