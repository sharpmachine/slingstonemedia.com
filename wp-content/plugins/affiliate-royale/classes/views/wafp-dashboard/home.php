<h3><?php _e('My Affiliate Dashboard', 'affiliate-royale'); ?></h3>
<?php echo $wafp_options->custom_message; ?>
<br/><br/>
<h4><?php _e('Affiliate Profile', 'affiliate-royale'); ?>:</h4>
<form action="" method="post">
<input type="hidden" name="wafp_process_profile" value="Y" />
<table class="wafp-frontend-table">
  <tr>
    <td class="wafp-frontend-label-col"><?php _e('First Name', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_first_name" value="<?php echo $wafp_dashboard_first_name; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('Last Name', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_last_name" value="<?php echo $wafp_dashboard_last_name; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('PayPal Email', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_paypal" value="<?php echo $wafp_dashboard_paypal; ?>" /></td>
  </tr>
  <?php
    if ($wafp_options->show_address_fields) {
  ?>
  <tr>
    <td><?php _e('Address Line 1', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_address_one" value="<?php echo $wafp_dashboard_address_one; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('Address Line 2', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_address_two" value="<?php echo $wafp_dashboard_address_two; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('City', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_city" value="<?php echo $wafp_dashboard_city; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('State/Province', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_state" value="<?php echo $wafp_dashboard_state; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('Zip/Postal Code', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_zip" value="<?php echo $wafp_dashboard_zip; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('Country', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_country" value="<?php echo $wafp_dashboard_country; ?>" /></td>
  </tr>
  <?php
    }
    if($wafp_options->show_tax_id_fields) {
  ?>
  <tr>
    <td><?php _e('SSN / Tax ID', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_tax_id_us" value="<?php echo $wafp_dashboard_tax_id_us; ?>" />&nbsp;<em><?php _e('US Residents', 'affiliate-royale'); ?></em></td>
  </tr>
  <tr>
    <td><?php _e('Intern\'l Tax ID', 'affiliate-royale'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_tax_id_int" value="<?php echo $wafp_dashboard_tax_id_int; ?>" />&nbsp;<em><?php _e('Non-US Residents', 'affiliate-royale'); ?></em></td>
  </tr>
  <?php
    }
  ?>
</table>
<input type="submit" value="<?php _e('Save Profile', 'affiliate-royale'); ?>" name="submit" />
</form>
</div> <!--END MAIN DASHBOARD WRAPPER-->
