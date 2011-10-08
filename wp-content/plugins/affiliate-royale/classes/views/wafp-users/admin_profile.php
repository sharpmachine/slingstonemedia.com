<h3><?php _e('Affiliate Settings', 'affiliate-royale'); ?></h3>
<?php
if($wafp_options->show_address_fields and $is_affiliate)
{
?>
  <table class="form-table">
    <tr>
      <th><label for="<?php echo WafpUser::$address_one_str; ?>"><?php _e('Address 1', 'affiliate-royale'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$address_one_str; ?>" id="<?php echo WafpUser::$address_one_str; ?>" class="regular-text" value="<?php echo $address_one; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$address_two_str; ?>"><?php _e('Address 2', 'affiliate-royale'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$address_two_str; ?>" id="<?php echo WafpUser::$address_two_str; ?>" class="regular-text" value="<?php echo $address_two; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$city_str; ?>"><?php _e('City', 'affiliate-royale'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$city_str; ?>" id="<?php echo WafpUser::$city_str; ?>" class="regular-text" value="<?php echo $city; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$state_str; ?>"><?php _e('State', 'affiliate-royale'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$state_str; ?>" id="<?php echo WafpUser::$state_str; ?>" class="regular-text" value="<?php echo $state; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$zip_str; ?>"><?php _e('Zip', 'affiliate-royale'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$zip_str; ?>" id="<?php echo WafpUser::$zip_str; ?>" class="regular-text" value="<?php echo $zip; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$country_str; ?>"><?php _e('Country', 'affiliate-royale'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$country_str; ?>" id="<?php echo WafpUser::$country_str; ?>" class="regular-text" value="<?php echo $country; ?>" /></td>
    </tr>
  </table>
  <?php
}
  ?>
<?php
  if ($wafp_options->show_tax_id_fields && $is_affiliate) {
?>
  <table class="form-table">
    <tr>
      <th><label for="<?php echo WafpUser::$tax_id_us_str; ?>"><?php _e('SSN / Tax ID', 'affiliate-royale'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$tax_id_us_str; ?>" id="<?php echo WafpUser::$tax_id_us_str; ?>" class="regular-text" value="<?php echo $tax_id_us; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$tax_id_int_str; ?>"><?php _e('Int\'l Tax ID', 'affiliate-royale'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$tax_id_int_str; ?>" id="<?php echo WafpUser::$tax_id_int_str; ?>" class="regular-text" value="<?php echo $tax_id_int; ?>" /></td>
    </tr>
  </table>
<?php
  }
?>
<table class="form-table">
  <tr>
    <th><?php _e('Affiliate Referrer', 'affiliate-royale'); ?></th>
    <?php if($affiliate) { ?>
      <td><a href="<?php echo $wafp_blogurl; ?>/wp-admin/user-edit.php?user_id=<?php echo $affiliate_id; ?>&wp_http_referer=%2Fwp-admin%2Fusers.php"><?php echo $affiliate->get_full_name(); ?></a></td>
    <?php } else { ?>
      <td><?php _e('None', 'affiliate-royale'); ?></td>
    <?php } ?>
  </tr>
  <tr>
    <th><label for="<?php echo WafpUser::$is_affiliate_str; ?>"><?php _e('User is an Affiliate', 'affiliate-royale'); ?></label></th>
    <td><input type="checkbox" name="<?php echo WafpUser::$is_affiliate_str; ?>" id="<?php echo WafpUser::$is_affiliate_str; ?>"<?php echo $affiliate_selected_str; ?> />&nbsp;<?php _e('Is this user an Affiliate?', 'affiliate-royale'); ?></td>
  </tr>
  <tr>
    <th><label for="mepr_paypal_enabled"><?php _e('Commission Override', 'affiliate-royale'); ?></label></th>
    <td><input type="checkbox" name="wafp_override_enabled" onclick="jQuery('#wafp_override_pane').slideToggle();" id="wafp_override_enabled"<?php echo $selected_str; ?> />&nbsp;<?php _e('Enable Commission Override for this User.', 'affiliate-royale'); ?></td>
  </tr>
  <tr>
    <td colspan="2">
      <div id="wafp_override_pane" class="wafp-options-pane<?php echo $hidden_str; ?>">
        <h3><?php _e('Affiliate Commission Override:', 'affiliate-royale'); ?></h3>
        <?php foreach( $wafp_options->commission as $index => $commish ) { 
                $level = $index + 1;
                $override = isset($wafp_override[$index])?$wafp_override[$index]:$commish;
        ?>
          <p><?php printf(__('Level %d:', 'affiliate-royale'),$level); ?> <input type="text" name="wafp_override[]" id="wafp_override_<?php echo $level; ?>" size="3" value="<?php printf('%0.2f',$override); ?>" />%</p>
        <?php } ?>
        <p><input type="checkbox" name="<?php echo WafpUser::$recurring_str; ?>" id="<?php echo WafpUser::$recurring_str; ?>"<?php echo $recurring_selected_str; ?> />&nbsp;<?php _e('Pay this user recurring commissions', 'affiliate-royale'); ?></p>
      </div>
    </td>
  </tr>
</table>
<table class="form-table">
  <tr><td><a class="button wafp-resend-welcome-email" href="javascript:" user-id="<?php echo $user->get_id(); ?>" wafp-nonce="<?php echo wp_create_nonce('wafp-resend-welcome-email'); ?>"><?php _e('Resend Affiliate Program Welcome Email', 'affiliate-royale'); ?></a>&nbsp;&nbsp;<img src="<?php echo WAFP_SITEURL . '/wp-admin/images/loading.gif'; ?>" alt="<?php _e('Loading...', 'affiliate-royale'); ?>" style="display: none;" class="wafp-resend-welcome-email-loader" />&nbsp;&nbsp;<span class="wafp-resend-welcome-email-message">&nbsp;</span></td></tr>
</table>