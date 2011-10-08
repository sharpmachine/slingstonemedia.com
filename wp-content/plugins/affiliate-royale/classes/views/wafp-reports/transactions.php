<div id="wafp-admin-affiliate-panel">
<h3><?php _e('Transactions', 'affiliate-royale'); ?></h3>
<form action="" method="post">
<input type="hidden" name="wafp-update-transactions" value="Y" />
<table class="widefat post fixed wafp-table" cellspacing="0">
<thead>
  <tr>
    <th class="manage-column wafp-trans-time-col"><?php _e('Time', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-trans-affiliate-col"><?php _e('Affiliate', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-trans-invoice-col"><?php _e('Invoice', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-trans-amount-col"><?php _e('Amount', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-trans-refund-col"><?php _e('Refund', 'affiliate-royale'); ?></th>
    <th class="manage-column wafp-trans-total-col"><?php _e('Total', 'affiliate-royale'); ?></th>
  </tr>
</thead>
<tbody>
<?php
  global $wafp_options;
  foreach($transactions as $row)
  {
  ?>
<tr>
  <td><?php echo $row->created_at; ?></td>
  <td><?php echo $row->user_login; ?></td>
  <td><?php echo $row->trans_num; ?></td>
  <td><?php echo WafpAppHelper::format_currency( (float)$row->sale_amount); ?></td>
  <td><?php echo $wafp_options->currency_symbol; ?><input id="wafp-refund[<?php echo $row->id; ?>]" name="wafp-refund[<?php echo $row->id; ?>]" value="<?php echo WafpAppHelper::format_currency( (float)$row->refund_amount, false ); ?>" style="width: auto;" /></td>
  <td><?php echo WafpAppHelper::format_currency( ((float)$row->sale_amount - (float)$row->refund_amount)); ?></td>
</tr>
<?php } ?>
</tbody>
</table>
<p class="wafp-trans-submit-wrap">
<input type="submit" class="wafp-trans-submit" value="<?php _e('Update Transactions', 'affiliate-royale'); ?>" name="submit" />
</p>
</form>
<?php
if($prev_page)
{
  ?>
<span style="float: right;"><a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_transactions','current',<?php echo $prev_page; ?>);"><?php _e('Newer Transactions', 'affiliate-royale'); ?></a>&nbsp;&raquo;</span>
  <?php
}

if($next_page)
{
  ?>
<span>&laquo;&nbsp;<a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_transactions','current',<?php echo $next_page; ?>);"><?php _e('Older Transactions', 'affiliate-royale'); ?></a></span>
  <?php
}
?>
</div>
