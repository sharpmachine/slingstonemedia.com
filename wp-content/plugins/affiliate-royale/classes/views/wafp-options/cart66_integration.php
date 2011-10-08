<div class="wafp-options-pane wafp-integration-option wafp-cart66-option<?php echo $cart66_class; ?>">
  <p><strong><?php _e("Follow these steps to integrate with Cart66 using Affiliate Royale's iDevAffiliate comapatibility URL:", 'affiliate-royale'); ?></strong></p>
  <ol>
    <li><?php _e('Go to Cart66\'s options at "Cart66" -> "Settings" in your WordPress admin', 'affiliate-royale'); ?></li>
    <li><?php _e('Scroll down to "iDevAffiliate Settings"', 'affiliate-royale'); ?></li>
    <li>
      <?php _e('Copy & Paste the following URL into the "URL" field:', 'affiliate-royale'); ?>
      <pre><?php echo WAFP_SCRIPT_URL; ?>&controller=transactions&action=track&prod_id=Cart66&idev_saleamt=XXX&idev_ordernum=XXX</pre>
    </li>
    <li><?php _e('Click "Save"', 'affiliate-royale'); ?></li>
  </ol>
</div>