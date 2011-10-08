<?php
class WafpCommission
{
  /** STATIC CRUD METHODS **/
  function create( $affiliate_id, $transaction_id, $commission_level, $commission_percentage, $commission_amount, $payment_id=0, $correction_amount=0.00 )
  {
    global $wafp_db;
    $args = compact( 'affiliate_id', 'transaction_id', 'commission_level', 'commission_percentage', 'commission_amount', 'payment_id', 'correction_amount' );

    return $wafp_db->create_record($wafp_db->commissions, $args);
  }

  function update( $id, $affiliate_id, $transaction_id, $commission_level, $commission_percentage, $commission_amount, $payment_id=0, $correction_amount=0.00 )
  {
    global $wafp_db;
    $args = compact( 'affiliate_id', 'transaction_id', 'commission_level', 'commission_percentage', 'commission_amount', 'payment_id', 'correction_amount' );
    return $wafp_db->update_record($wafp_db->commissions, $id, $args);
  }
  
  function update_refund( $id, $refund_amount, $correction_amount="" )
  {
    global $wafp_db;
    
    if(!isset($correction_amount) or empty($correction_amount))
    {
      $record = WafpCommission::get_one($id);
      
      if($record)
        $correction_amount = (float)( (float)$refund_amount * ( (float)$record->commission_percentage / 100.0 ) );
    }

    $args = compact( 'correction_amount' );
    return $wafp_db->update_record($wafp_db->commissions, $id, $args);
  }
  function delete( $id )
  {
    global $wafp_db;

    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->commissions, $args);
  }
  
  function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->commissions, $args);
  }

  function get_all_by_affiliate_id($affiliate_id, $order_by='', $limit='')
  {
    global $wafp_db;
    $args = compact( 'affiliate_id' );
    return $wafp_db->get_records($wafp_db->commissions, $args, $order_by, $limit);
  }

  function get_all_by_transaction_id($transaction_id, $order_by='', $limit='')
  {
    global $wafp_db;
    $args = compact( 'transaction_id' );
    return $wafp_db->get_records($wafp_db->commissions, $args, $order_by, $limit);
  }

  function get_count()
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->commissions);
  }
  
  function get_all($order_by='', $limit='')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->commissions, array(), $order_by, $limit);
  }

  function get_all_objects_by_affiliate_id( $affiliate_id, $order_by='', $limit='')
  {
    $all_records = WafpCommission::get_all_by_affiliate_id($affiliate_id, $order_by, $limit);
  
    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = WafpCommission::get_stored_object($record->id);
  
    return $my_objects;
  }
  
  function get_all_objects($order_by='', $limit='')
  {
    $all_records =& WafpCommission::get_all($order_by, $limit);
  
    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = WafpCommission::get_stored_object($record->id);
  
    return $my_objects;
  }

  function get_stored_object($id)
  { 
    static $my_objects;

    if( !isset($my_objects) )
      $my_objects = array();

    if( !isset($my_objects[$id]) or
        empty($my_objects[$id]) or
        !is_object($my_objects[$id]) )
      $my_objects[$id] = new WafpCommission($id);
    
    return $my_objects[$id];
  }
  
  /** INSTANCE VARIABLES & METHODS **/
  var $rec;

  public function WafpCommission($id)
  {
    $this->rec = WafpCommission::get_one($id);
  }
}
?>
