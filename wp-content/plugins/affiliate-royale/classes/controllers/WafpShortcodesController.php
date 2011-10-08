<?php

class WafpShortcodesController
{
  function WafpShortcodesController()
  {
    add_shortcode( 'wafp_ipn', array( &$this, 'get_ipn' ) );
    add_shortcode( 'wafp_custom_args', array( &$this, 'get_custom_args' ) );
    add_shortcode( 'ar_track', array( &$this, 'ar_track' ) );
  }
  
  function get_ipn($atts)
  {
    return WAFP_SCRIPT_URL . "&controller=paypal&action=ipn"; 
  }
  
  function get_custom_args($atts)
  {
    $custom_args = '';
  
    $affiliate_id = $_COOKIE['wafp_click'];
    if( isset( $affiliate_id ) and $affiliate_id and is_numeric( $affiliate_id ) )
      $custom_args .= "aff_id={$affiliate_id}";
  
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    if( isset( $ip_addr ) and $ip_addr )
    {
      if( !empty($custom_args) )
        $custom_args .= '&';

      $custom_args .= "ip_addr={$ip_addr}";
    }
  
    return $custom_args;
  }
  
  function ar_track($atts)
  {
    $use_params = (isset($atts['use_params'])?($atts['use_params'] == 'true'):false);
    
    $amount     = WafpUtils::with_default($atts['amount'],'');
    $order_id   = WafpUtils::with_default($atts['order_id'],'');
    $prod_id    = WafpUtils::with_default($atts['prod_id'],'');
    $aff_id     = WafpUtils::with_default($atts['aff_id'],'');
    $subscr_id  = WafpUtils::with_default($atts['subscr_id'],'');
    
    if($use_params)
    {
      $amount     = WafpUtils::with_default($_REQUEST[$amount],'');
      $order_id   = WafpUtils::with_default($_REQUEST[$order_id],''); // if using params we don't auto gen this value
      $prod_id    = WafpUtils::with_default($_REQUEST[$prod_id],'');
      $aff_id     = WafpUtils::with_default($_REQUEST[$aff_id],'');
      $subscr_id  = WafpUtils::with_default($_REQUEST[$subscr_id],'');
    }
    else
      $order_id   = WafpUtils::with_default($order_id,uniqid());
    
    if(!empty($order_id))
      WafpTransaction::track($amount, $order_id, $prod_id, $aff_id, $subscr_id);
  }
}

?>