<div class="wrap">
<?php require(WAFP_VIEWS_PATH . "/wafp-reports/title.php"); ?>
<?php require(WAFP_VIEWS_PATH . "/wafp-reports/nav.php"); ?>
<?php
global $wafp_reports_controller;

$action = isset($_GET['action'])?$_GET['action']:'';

if($action=='admin_affiliate_clicks')
  $wafp_reports_controller->admin_affiliate_clicks();
else if($action=='admin_affiliate_top')
  $wafp_reports_controller->admin_affiliate_top();
else if($action=='admin_affiliate_payments')
  $wafp_reports_controller->admin_affiliate_payments();
else
  $wafp_reports_controller->admin_affiliate_stats();

?>
</div>