<form name="wafp_registerform" id="wafp_registerform" action="" method="post">
<input type="hidden" id="wafp-process-form" name="wafp-process-form" value="Y" />
<table class="affroy_signup_form">
  <tr>
    <td><label for="user_first_name"><?php _e('First Name', 'affiliate-royale'); ?>:&nbsp;</td></td>
    <td><input type="text" name="user_first_name" id="user_first_name" class="input wafp_signup_input" value="<?php echo (isset($user_first_name)?$user_first_name:''); ?>" tabindex="1000" /></td>
  </tr>
  <tr>
    <td><label for="user_last_name"><?php _e('Last Name', 'affiliate-royale'); ?>:&nbsp;</td></td>
    <td><input type="text" name="user_last_name" id="user_last_name" class="input wafp_signup_input" value="<?php echo (isset($user_last_name)?$user_last_name:''); ?>" tabindex="2000" /></td>
  </tr>
  <tr>
    <td><label><?php _e('Choose a Username', 'affiliate-royale'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="user_login" id="user_login" class="input wafp_signup_input" value="<?php echo (isset($user_login)?$user_login:''); ?>" tabindex="3000" /></td>
  </tr>
  <tr>
    <td><label><?php _e('E-mail', 'affiliate-royale'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="user_email" id="user_email" class="input wafp_signup_input" value="<?php echo (isset($user_email)?$user_email:''); ?>" tabindex="4000" /></td>
  </tr>
<?php
  if($wafp_options->show_address_fields) {
?>
  <tr>
    <td><label><?php _e('Address Line 1', 'affiliate-royale'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$address_one_str; ?>" id="<?php echo WafpUser::$address_one_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$address_one_str])?$_POST[WafpUser::$address_one_str]:''); ?>" tabindex="5000" /></td>
  </tr>
  <tr>
    <td><label><?php _e('Address Line 2', 'affiliate-royale'); ?>:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$address_two_str; ?>" id="<?php echo WafpUser::$address_two_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$address_two_str])?$_POST[WafpUser::$address_two_str]:''); ?>" tabindex="6000" /></td>
  </tr>
  <tr>
    <td><label><?php _e('City', 'affiliate-royale'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$city_str; ?>" id="<?php echo WafpUser::$city_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$city_str])?$_POST[WafpUser::$city_str]:''); ?>" tabindex="7000" /></td>
  </tr>
  <tr>
    <td><label><?php _e('State/Provice', 'affiliate-royale'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$state_str; ?>" id="<?php echo WafpUser::$state_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$state_str])?$_POST[WafpUser::$state_str]:''); ?>" tabindex="8000" /></td>
  </tr>
  <tr>
    <td><label><?php _e('Zip/Postal Code', 'affiliate-royale'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$zip_str; ?>" id="<?php echo WafpUser::$zip_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$zip_str])?$_POST[WafpUser::$zip_str]:''); ?>" tabindex="9000" /></td>
  </tr>
  <tr>
    <td><label><?php _e('Country', 'affiliate-royale'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$country_str; ?>" id="<?php echo WafpUser::$country_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$country_str])?$_POST[WafpUser::$country_str]:''); ?>" tabindex="9500" /></td>
  </tr>
<?php
  }
  if($wafp_options->show_tax_id_fields) {
?>
  <tr>
    <td><label><?php _e('SSN / Tax ID', 'affiliate-royale'); ?>:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$tax_id_us_str; ?>" id="<?php echo WafpUser::$tax_id_us_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$tax_id_us_str])?$_POST[WafpUser::$tax_id_us_str]:''); ?>" tabindex="10000" />&nbsp;<em><?php _e('US Residents (###-##-#### or ##-#######)', 'affiliate-royale'); ?></em></td>
  </tr>
  <tr>
    <td><label><?php _e('International Tax ID', 'affiliate-royale'); ?>:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$tax_id_int_str; ?>" id="<?php echo WafpUser::$tax_id_int_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$tax_id_int_str])?$_POST[WafpUser::$tax_id_int_str]:''); ?>" tabindex="11000" />&nbsp;<em><?php _e('Non-US Residents', 'affiliate-royale'); ?></em></td>
  </tr>
<?php
  }
  if($wafp_options->payment_type == 'paypal') {
?>
  <tr>
    <td><label><?php _e('PayPal E-mail', 'affiliate-royale'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$paypal_email_str; ?>" id="<?php echo WafpUser::$paypal_email_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$paypal_email_str])?$_POST[WafpUser::$paypal_email_str]:''); ?>" tabindex="12000" /></td>
  </tr>
<?php
  }
?>
  <tr>
    <td><label><?php _e('Create a Password', 'affiliate-royale'); ?>:&nbsp;</label></td>
    <td><input type="password" name="wafp_user_password" id="wafp_user_password" class="input wafp_signup_input" tabindex="13000"/></td>
  </tr>
  <tr>
    <td><label><?php _e('Password Confirmation', 'affiliate-royale'); ?>:&nbsp;</label></td>
    <td><input type="password" name="wafp_user_password_confirm" id="wafp_user_password_confirm" class="input wafp_signup_input" tabindex="13000"/></td>
  </tr>
</table>
  
  <!-- Extra signup fields show here -->
  <?php do_action('wafp-user-signup-fields'); ?>
  
  <br class="clear" />
  <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="wafp-share-button" value="<?php _e('Sign Up', 'affiliate-royale'); ?>" tabindex="13000" /></p>
</form>
