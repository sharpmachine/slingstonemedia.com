<form name="wafp_loginform" id="wafp_loginform" action="" method="post">
	<p>
		<label><strong><?php _e('Username', 'affiliate-royale'); ?></strong><br/>
		<input type="text" name="log" id="user_login" class="input" value="<?php echo (isset($_POST['log'])?$_POST['log']:''); ?>" tabindex="500" /></label><br/>
		<label><strong><?php _e('Password', 'affiliate-royale'); ?></strong><br/>
		<input type="password" name="pwd" id="user_pass" class="input" value="<?php echo (isset($_POST['pwd'])?$_POST['pwd']:''); ?>" tabindex="510" /></label><br/>
	  <label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="520"<?php echo (isset($_POST['rememberme'])?' checked="checked"':''); ?> /> <?php _e('Remember Me', 'affiliate-royale'); ?></label>
	</p>
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button-primary wafp-share-button" value="<?php _e('Log In', 'affiliate-royale'); ?>" tabindex="530" />
		<input type="hidden" name="redirect_to" value="<?php echo $redirect_to; ?>" />
		<input type="hidden" name="testcookie" value="1" />
		<input type="hidden" name="wafp_process_login_form" value="true" />
	</p>
</form>
<p class="wafp-login-actions">
  <a href="<?php echo $signup_url; ?>"><?php _e('Register', 'affiliate-royale'); ?></a>&nbsp;|
  <a href="<?php echo $forgot_password_url; ?>"><?php _e('Lost Password?', 'affiliate-royale'); ?></a>
</p>
