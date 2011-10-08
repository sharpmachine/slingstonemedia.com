<div class="wafp-options-pane wafp-integration-option wafp-paypal-option<?php echo $paypal_class; ?>">
  <p><strong><?php _e('Here are the steps you\'ll need to follow to integrate Affiliate Royale with PayPal', 'affiliate-royale'); ?>:</strong></p>
  <h3><?php _e('To create your payment button:', 'affiliate-royale'); ?></h3>
  <ol>
    <li><?php _e('Log Into Your PayPal Account', 'affiliate-royale'); ?></li>
    <li><?php _e('Go to "Merchant Services" -> "Create Buttons"', 'affiliate-royale'); ?></li>
    <li><?php _e('Create either a "Buy Now" or "Subscribe" Button', 'affiliate-royale'); ?></li>
    <li><?php _e('Make Sure You Uncheck "Save Button at PayPal" in Step 2', 'affiliate-royale'); ?></li>
    <li><?php _e('Make Sure "Add advanced variables" is checked in Step 3 and add the following text into the "Advanced Variables" text area:', 'affiliate-royale'); ?><br/>
      <pre><strong>notify_url=[wafp_ipn]<br/>custom=[wafp_custom_args]</strong></pre>
    </li>
    <li><?php _e('Click "Create Button"', 'affiliate-royale'); ?></li>
    <li><?php _e('Click "Remove code protection"', 'affiliate-royale'); ?></li>
    <li><?php _e('Now you can copy your button\'s code and paste it somewhere on this site. Note: the button must reside on this site in a page or post in order for the affiliate tracking to work properly.', 'affiliate-royale'); ?></li>
  </ol>
  <h3><?php _e('(Optional) Setup Affiliate Royale to automatically record refunds and process recurring payments:', 'affiliate-royale'); ?></h3>
  <ol>
    <li><?php _e('Go to "My Account" -> "Profile" -> "Instant Payment Notification Preferences" in PayPal', 'affiliate-royale'); ?></li>
    <li><?php _e('Make sure IPN is enabled', 'affiliate-royale'); ?></li>
    <li><?php _e('Paste the following URL into the Notification URL text field and hit save:', 'affiliate-royale'); ?><br/>
      <pre><strong><?php echo WAFP_SCRIPT_URL . "&controller=paypal&action=ipn"; ?></strong></pre>
    </li>
  </ol>
</div>