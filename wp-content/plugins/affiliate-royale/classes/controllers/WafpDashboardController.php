<?php
class WafpDashboardController {

  function display_become_affiliate()
  {
    require( WAFP_VIEWS_PATH . "/wafp-dashboard/become.php" );
  }

  function display_dashboard()
  {
    global $wafp_options, $current_user;
    WafpUtils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;

    extract($_POST);
    
    if(!isset($wafp_dashboard_first_name))
      $wafp_dashboard_first_name = $current_user->first_name;
    if(!isset($wafp_dashboard_last_name))
      $wafp_dashboard_last_name = $current_user->last_name;
    if(!isset($wafp_dashboard_paypal))
      $wafp_dashboard_paypal = $current_user->wafp_paypal_email;
    if ($wafp_options->show_address_fields)
    {
      if(!isset($wafp_dashboard_address_one))
        $wafp_dashboard_address_one = $current_user->wafp_user_address_one;
      if(!isset($wafp_dashboard_address_two))
        $wafp_dashboard_address_two = $current_user->wafp_user_address_two;
      if(!isset($wafp_dashboard_city))
        $wafp_dashboard_city = $current_user->wafp_user_city;
      if(!isset($wafp_dashboard_state))
        $wafp_dashboard_state = $current_user->wafp_user_state;
      if(!isset($wafp_dashboard_zip))
        $wafp_dashboard_zip = $current_user->wafp_user_zip;
      if(!isset($wafp_dashboard_country))
        $wafp_dashboard_country = $current_user->wafp_user_country;
    }
    if ($wafp_options->show_tax_id_fields)
    {
      if(!isset($wafp_dashboard_tax_id_us))
        $wafp_dashboard_tax_id_us = $current_user->wafp_user_tax_id_us;
      if(!isset($wafp_dashboard_tax_id_int))
        $wafp_dashboard_tax_id_int = $current_user->wafp_user_tax_id_int;
    }

    if( isset($wafp_process_profile) and $wafp_process_profile == "Y" )
    {
      update_user_meta( $affiliate_id, 'first_name', $wafp_dashboard_first_name );
      update_user_meta( $affiliate_id, 'last_name', $wafp_dashboard_last_name );
      update_user_meta( $affiliate_id, 'wafp_paypal_email', $wafp_dashboard_paypal );
      if ($wafp_options->show_address_fields)
      {
        update_user_meta( $affiliate_id, 'wafp_user_address_one', $wafp_dashboard_address_one );
        update_user_meta( $affiliate_id, 'wafp_user_address_two', $wafp_dashboard_address_two );
        update_user_meta( $affiliate_id, 'wafp_user_city', $wafp_dashboard_city );
        update_user_meta( $affiliate_id, 'wafp_user_state', $wafp_dashboard_state );
        update_user_meta( $affiliate_id, 'wafp_user_zip', $wafp_dashboard_zip );
        update_user_meta( $affiliate_id, 'wafp_user_country', $wafp_dashboard_country );
      }
      if ($wafp_options->show_tax_id_fields)
      {
        update_user_meta( $affiliate_id, 'wafp_user_tax_id_us', $wafp_dashboard_tax_id_us );
        update_user_meta( $affiliate_id, 'wafp_user_tax_id_int', $wafp_dashboard_tax_id_int );
      }
    }

    require( WAFP_VIEWS_PATH . "/wafp-dashboard/nav.php" );
    require( WAFP_VIEWS_PATH . "/wafp-dashboard/home.php" );
  }

  function display_links()
  { 
    global $current_user;
    $links =& WafpLink::get_all_objects('image, id');
    WafpUtils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;
	
    require( WAFP_VIEWS_PATH . "/wafp-dashboard/nav.php" );
    require( WAFP_VIEWS_PATH . "/wafp-dashboard/links.php" );
  }

  function display_stats($period='current', $show_nav=true)
  {
    global $current_user;
    WafpUtils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;

    if( $period=='current' or empty($period) )
      $period = mktime(0, 0, 0, date('n'), 1, date('Y'));

    $stats =& WafpReport::affiliate_stats( $period, $affiliate_id );

    if($show_nav)
      require( WAFP_VIEWS_PATH . "/wafp-dashboard/nav.php" );
	  
	require( WAFP_VIEWS_PATH . "/wafp-dashboard/stats.php" );
  }

  function display_payments()
  {
    global $current_user;
    WafpUtils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;

    $payments = WafpReport::affiliate_frontend_payments($affiliate_id);
    $pmt_totals =& WafpReport::affiliate_payment_totals($affiliate_id);
    
    extract($pmt_totals);

    require( WAFP_VIEWS_PATH . "/wafp-dashboard/nav.php" );
    require( WAFP_VIEWS_PATH . "/wafp-dashboard/payments.php" );
  }
}
?>
