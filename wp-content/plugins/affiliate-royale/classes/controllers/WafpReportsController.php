<?php
class WafpReportsController
{
  function overview()
  {
    if(isset($_POST['wafp-update-transactions']) and $_POST['wafp-update-transactions'] == 'Y')
      $this->process_update_transactions();
    else if(isset($_POST['wafp-update-payments']) and $_POST['wafp-update-payments'] == 'Y')
      $this->process_update_payments();
    else
      require( WAFP_VIEWS_PATH . "/wafp-reports/overview.php" );
  }
  
  function admin_affiliate_stats($period='current')
  {
    if( $period=='current' or empty($period) )
      $period = mktime(0, 0, 0, date('n'), 1, date('Y'));

    $stats =& WafpReport::affiliate_stats( $period );
    require( WAFP_VIEWS_PATH . "/wafp-reports/stats.php" );
  }
  
  function admin_affiliate_clicks( $page=1, $page_size=25 )
  {
    if( $page=='current' or empty($page) )
      $page=1;

    $clicks =& WafpReport::affiliate_clicks( $page, $page_size );
    $click_count = WafpClick::get_count();
    $num_pages   = $click_count / $page_size;
    
    $prev_page = false;
    $next_page = false;

    if($page > 1)
      $prev_page = $page - 1;
    
    if($page < $num_pages)
      $next_page = $page + 1;

    require( WAFP_VIEWS_PATH . "/wafp-reports/clicks.php" );
  }
  
  function admin_affiliate_top($period='current', $page=1, $page_size=25 )
  {
    if( $period=='current' or empty($period) )
      $period = mktime(0, 0, 0, date('n'), 1, date('Y'));

    if( !isset($page) or empty($page) )
      $page=1;

    $top_affiliates =& WafpReport::top_referring_affiliates( $period, $page, $page_size );
    $aff_count = WafpReport::get_user_count();
    $num_pages = $aff_count / $page_size;
    
    $prev_page = false;
    $next_page = false;

    if($page > 1)
      $prev_page = $page - 1;
    
    if($page < $num_pages)
      $next_page = $page + 1;

    require( WAFP_VIEWS_PATH . "/wafp-reports/top.php" );
  }
  
  function admin_affiliate_transactions($page=1, $page_size=25)
  {
    if( $page=='current' or empty($page) )
      $page=1;

    $transactions =& WafpReport::affiliate_transactions( $page, $page_size );
    $transaction_count = WafpTransaction::get_count();
    $num_pages   = $transaction_count / $page_size;
    
    $prev_page = false;
    $next_page = false;

    if($page > 1)
      $prev_page = $page - 1;
    
    if($page < $num_pages)
      $next_page = $page + 1;

    require( WAFP_VIEWS_PATH . "/wafp-reports/transactions.php" );
  }
  
  function admin_affiliate_payments($period='current')
  {
    global $wafp_options;
    
    if( $period=='current' or empty($period) )
      $period = mktime(0, 0, 0, date('n'), 1, date('Y'));

    $payments =& WafpReport::affiliate_payments( $period );
    
    extract($payments);

    require( WAFP_VIEWS_PATH . "/wafp-reports/payments.php" );
  }
  
  function process_update_transactions()
  {
    global $wafp_options;
    
    if (!empty($_POST['wafp-refund']))
    {
      foreach( $_POST['wafp-refund'] as $affiliate_id => $value ) {
        if( $wafp_options->number_format == '#.###,##' )
          $value = str_replace(',','.',$value);
        WafpTransaction::update_refund( $affiliate_id, $value );
      }

      require(WAFP_VIEWS_PATH . "/wafp-reports/title.php");
      require( WAFP_VIEWS_PATH . "/wafp-reports/nav.php" );
      $this->admin_affiliate_transactions();
    }
  }
  
  
  function process_update_payments()
  {
    require(WAFP_VIEWS_PATH . "/wafp-reports/title.php");
    require( WAFP_VIEWS_PATH . "/wafp-reports/nav.php" );

    if (!empty($_POST['wafp-payment-paid'])) //Paul added this fix
    {
      $payment_ids = array();
      foreach( $_POST['wafp-payment-paid'] as $affiliate_id => $value )
      {
        $payment_id = WafpPayment::create( $affiliate_id, $_POST['wafp-payment-amount'][$affiliate_id] );
        WafpPayment::update_transactions( $payment_id, $affiliate_id, $_POST['wafp-period'] );
        
        if( !empty($payment_id) and $payment_id )
          $payment_ids[] = $payment_id;
      }

      $payment_ids = implode(',', $payment_ids);

      $this->admin_affiliate_payment_receipt($payment_ids);
    }
    else
      $this->admin_affiliate_payments();
  }

  function admin_affiliate_payment_receipt($payment_ids=null)
  {
    global $wafp_options;
    
    require( WAFP_VIEWS_PATH . "/wafp-reports/payment_receipt.php" );
  }

  function admin_paypal_bulk_file($payment_id)
  {
    global $wafp_options, $wafp_blogname;

    $bulk_totals = WafpReport::affiliate_paypal_bulk_file_totals($payment_id);
    
    require( WAFP_VIEWS_PATH . "/wafp-reports/paypal_bulk_file.php" );
  }
}
?>
