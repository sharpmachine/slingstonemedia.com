<h3><?php _e('My Payment History', 'affiliate-royale'); ?></h3>
<p>
<strong><?php _e('Your Current Balance', 'affiliate-royale'); ?></strong>:&nbsp;<?php echo WafpAppHelper::format_currency( $owed ); ?><br/>
<strong><?php _e('Amount Paid To Date', 'affiliate-royale'); ?></strong>:&nbsp;<?php echo WafpAppHelper::format_currency( $paid ); ?>
</p>
<table class="wafp-stats-table" cellspacing="0">
  <thead>
    <tr>
      <th class="manage-column wafp-front-pay-date-col"><?php _e('Date', 'affiliate-royale'); ?></th>
      <th class="manage-column wafp-front-pay-sale-col"><?php _e('Sale', 'affiliate-royale'); ?></th>
      <th class="manage-column wafp-front-pay-level-col"><?php _e('Level', 'affiliate-royale'); ?></th>
      <th class="manage-column wafp-front-pay-commission-col"><?php _e('Commission', 'affiliate-royale'); ?></th>
      <th class="manage-column wafp-front-pay-correction-col"><?php _e('Correction', 'affiliate-royale'); ?></th>
      <th class="manage-column wafp-front-pay-sale-col"><?php _e('Payment', 'affiliate-royale'); ?></th>
      <th class="manage-column wafp-front-pay-owed-col"><?php _e('Owed', 'affiliate-royale'); ?></th>
      <th class="manage-column wafp-front-pay-paid-col"><?php _e('Paid', 'affiliate-royale'); ?></th>
    </tr>
  </thead>
<?php
foreach($payments as $payment)
{
  if($payment->trans_type == 'payment')
  {
    $total_amount = "0.00";
    $row_class = 'wafp-payment-row';
    $paid_label = '<span class="wafp-payment-label">' . _('PAYMENT', 'affiliate-royale') . '</span>';
  }
  else if($payment->payment_id > 0)
  {
    $total_amount = "0.00";
    $row_class = 'wafp-transaction-row';
    $paid_label = '<span class="wafp-paid-label">' . _('PAID', 'affiliate-royale') . '</span>';
  }
  else
  {
    $total_amount = WafpAppHelper::format_currency( (float)$payment->total_amount );
    $row_class = 'wafp-transaction-row';
    $paid_label = '<span class="wafp-owed-label">' . _('PENDING', 'affiliate-royale') . '</span>';
  }
  
  if((float)$payment->correction_amount > 0.00)
    $correction_amount = "<span class=\"wafp-red-text\">(" . WafpAppHelper::format_currency( (float)$payment->correction_amount ) . ")</span>";
  else
    $correction_amount = "$0.00";

    ?>
    <tr class="<?php echo $row_class; ?>">
      <td><?php echo $payment->timestamp; ?></td>
      <td><?php echo WafpAppHelper::format_currency( (float)$payment->sale_amount ); ?></td>
      <td><?php echo ($payment->trans_type == 'payment') ? "" : (int)$payment->commission_level + 1; ?></td>
      <td><?php echo WafpAppHelper::format_currency( (float)$payment->commission_amount ); ?></td>
      <td><?php echo $correction_amount; ?></td>
      <td><?php echo WafpAppHelper::format_currency( (float)$payment->payment_amount ); ?></td>
      <td><?php echo $total_amount; ?></td>
      <td><?php echo $paid_label; ?></td>
    </tr>
    <?php
}
?>
</table>
</div> <!--END MAIN DASHBOARD WRAPPER-->