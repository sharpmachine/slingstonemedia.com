<div id="wafp-admin-affiliate-panel">
<h3><?php _e('Pay Your Affiliates', 'affiliate-royale'); ?></h3>
<form action="" method="post">
<input type="hidden" name="wafp-update-payments" value="Y" />
<input type="hidden" name="wafp-period" value="<?php echo $period; ?>" />
<p><?php _e('Select the period you want to view', 'affiliate-royale'); ?>:<br/><?php WafpReportsHelper::periods_dropdown('wafp-report-period', $period, 'javascript:wafp_view_admin_affiliate_page( \'admin_affiliate_payments\', this.value, 1);'); ?></p>
<table class="widefat post fixed wafp-table" cellspacing="0">
<thead>
  <tr>
    <th class="manage-column wafp-pay-affiliate-col"><?php _e('Affiliate', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-pay-name-col"><?php _e('Name', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-pay-paypal-col"><?php _e('PayPal Email', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-pay-commissions-col"><?php _e('Commissions', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-pay-corrections-col"><?php _e('Corrections', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-pay-payment-col"><?php _e('To Payout', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-pay-paid-col"><?php _e('Paid', 'affiliate-royale'); ?></th>
  </tr>
</thead>
<tbody>
<?php
  foreach($totals as $key => $total)
  {
    $row = $results[$key];
    
    $paypal_email = get_user_meta($row->aff_id, 'wafp_paypal_email', true);
    $first_name   = get_user_meta($row->aff_id, 'first_name', true);
    $last_name    = get_user_meta($row->aff_id, 'last_name', true);
    $address_one  = get_user_meta($row->aff_id, 'wafp_user_address_one', true);
    $address_two  = get_user_meta($row->aff_id, 'wafp_user_address_two', true);
    $city         = get_user_meta($row->aff_id, 'wafp_user_city', true);
    $state        = get_user_meta($row->aff_id, 'wafp_user_state', true);
    $zip          = get_user_meta($row->aff_id, 'wafp_user_zip', true);
    $country      = get_user_meta($row->aff_id, 'wafp_user_country', true);

    if((float)$row->correction_amount > 0.00)
      $correction = "<span style=\"color: red\">(" . WafpAppHelper::format_currency( (float)$row->correction_amount) . ")</span>";
    else
      $correction = WafpAppHelper::format_currency( (float)$row->correction_amount);
  ?>
<tr>
  <td><?php echo $row->aff_login; ?></td>
  <td><?php echo "{$first_name} {$last_name}"; ?></td>
  <td><?php echo ( !$paypal_email or empty($paypal_email) )?__("none", 'affiliate-royale'):$paypal_email; ?></td>
  <td><?php echo WafpAppHelper::format_currency( (float)$row->commission_amount ); ?></td>
  <td><?php echo $correction; ?></td>
  <td><?php echo WafpAppHelper::format_currency( (float)($total)); ?></td>
  <td><input type="hidden" name="wafp-payment-amount[<?php echo $row->aff_id; ?>]" value="<?php printf('%0.2f', (float)($total)); ?>" /><input type="checkbox" name="wafp-payment-paid[<?php echo $row->aff_id; ?>]" /></td>
</tr>
<?php } ?>
</tbody>
</table>
<p class="wafp-trans-submit-wrap">
<input type="submit" class="wafp-trans-submit" value="<?php _e('Mark Checked Commissions as Paid', 'affiliate-royale'); ?>" name="submit" />
</p>
</form>
<?php
if(isset($prev_page))
{
  ?>
<span style="float: right;"><a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_payments',<?php echo $period; ?>,<?php echo $prev_page; ?>);"><?php _e('Newer Clicks', 'affiliate-royale'); ?></a>&nbsp;&raquo;</span>
  <?php
}

if(isset($next_page))
{
  ?>
<span>&laquo;&nbsp;<a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_payments',<?php echo $period; ?>,<?php echo $next_page; ?>);"><?php _e('Older Clicks', 'affiliate-royale'); ?></a></span>
  <?php
}
?>
</div>
