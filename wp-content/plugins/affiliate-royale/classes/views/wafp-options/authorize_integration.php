<div class="wafp-options-pane wafp-integration-option wafp-authorize-option<?php echo $authorize_class; ?>">
  <p><strong><?php _e('Setting up Affiliate Royale and Authorize.Net ARB to work together involves configuring Authorize\'s Silent Post URL and MD5 Hash. Here are the steps you\'ll need to follow:', 'affiliate-royale'); ?></strong></p>
  <h3>1. <?php _e('Put a Tracking Code on your Payment Thank You Pages:', 'affiliate-royale'); ?></h3>
  <div class="wafp-options-pane">
    <p><?php _e('You\'ll want to fill in a value for prod_id which can be the name of the product and the subscr_id which is the subscription id for the new ARB subscription.', 'affiliate-royale'); ?></p>
    <p><strong>&lt;img src="http://affiliateroyale.com/index.php?plugin=wafp&controller=transactions&action=track&prod_id=&subscr_id=" width="1px" height="1px" style="display: none;" /&gt;</strong></p>
  </div>
  <h3>2. <?php _e('Configure the Silent Post URL:', 'affiliate-royale'); ?></h3>
  <div class="wafp-options-pane">
    <p><?php _e('The Silent Post URL sends data back to Affiliate Royale about each transaction made through Authorize.Net:', 'affiliate-royale'); ?></p>
    <ol>
      <li><?php _e('Log on to the Merchant Interface at https://account.authorize.net', 'affiliate-royale'); ?></li>
      <li><?php _e('Click Settings under Account in the main menu on the left', 'affiliate-royale'); ?></li>
      <li><?php _e('Click Silent Post URL in the Transaction Format Settings section', 'affiliate-royale'); ?></li>
      <li><?php _e('Enter the following URL', 'affiliate-royale'); ?>:<br/><strong><?php global $wafp_authorize_controller; echo $wafp_authorize_controller->get_silent_post_url(); ?></strong></li>
      <li><?php _e('Click Submit', 'affiliate-royale'); ?></li>
    </ol>
  </div>
  <h3>3. <?php _e('Configure the MD5 Hash:', 'affiliate-royale'); ?></h3>
  <div class="wafp-options-pane">
    <p><?php _e('The MD5 Hash feature allows you to authenticate that transaction responses are securely received from Authorize.Net.', 'affiliate-royale'); ?></p>
    <ol>
      <li><?php _e('Log on to the Merchant Interface at https://account.authorize.net', 'affiliate-royale'); ?></li>
      <li><?php _e('Click Settings under Account in the main menu on the left', 'affiliate-royale'); ?></li>
      <li><?php _e('Click MD5-Hash in the Security Settings section', 'affiliate-royale'); ?></li>
      <li><?php _e('Enter the following value into the "New Hash Value" and "Confirm Hash Value" fields:', 'affiliate-royale'); ?><br/><strong><?php global $wafp_authorize_controller; echo $wafp_authorize_controller->hash_key; ?></strong></li>
      <li><?php _e('Click Submit', 'affiliate-royale'); ?>&nbsp;(<?php _e('Note: the MD5 Hash value is not displayed on the screen once submitted.', 'affiliate-royale'); ?>)</li>
    </ol>
  </div>
</div>