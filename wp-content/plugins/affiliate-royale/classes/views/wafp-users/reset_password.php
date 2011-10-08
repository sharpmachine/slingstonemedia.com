<h3><?php _e('Enter your new password', 'affiliate-royale'); ?></h3>
<form name="wafp_reset_password_form" id="wafp_reset_password_form" action="" method="post">
  <p>
    <label><?php _e('Password', 'affiliate-royale'); ?>:<br/>
    <input type="password" name="wafp_user_password" id="wafp_user_password" class="input wafp_signup_input" tabindex="700"/></label>
  </p>
  <p>
    <label><?php _e('Password Confirmation', 'affiliate-royale'); ?>:<br />
    <input type="password" name="wafp_user_password_confirm" id="wafp_user_password_confirm" class="input wafp_signup_input" tabindex="710"/></label>
  </p>
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button-primary wafp-share-button" value="<?php _e('Reset Password', 'affiliate-royale'); ?>" tabindex="720" />
		<input type="hidden" name="action" value="wafp_process_reset_password_form" />
		<input type="hidden" name="wafp_screenname" value="<?php echo $wafp_screenname; ?>" />
  	<input type="hidden" name="wafp_key" value="<?php echo $wafp_key; ?>" />
	</p>
</form>