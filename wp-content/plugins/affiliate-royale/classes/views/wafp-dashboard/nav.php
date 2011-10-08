<?php global $wafp_options; ?>
<div id="wafp-dash-wrapper">
<ul class="wafp-nav-bar">
  <li><a href="<?php echo $wafp_options->affiliate_page_url("action=home"); ?>"><?php _e('Home', 'affiliate-royale'); ?></a></li>
  <li><a href="<?php echo $wafp_options->affiliate_page_url("action=stats"); ?>"><?php _e('Stats', 'affiliate-royale'); ?></a></li>
  <li><a href="<?php echo $wafp_options->affiliate_page_url("action=links"); ?>"><?php _e('Links &amp; Banners', 'affiliate-royale'); ?></a></li>
  <li><a href="<?php echo $wafp_options->affiliate_page_url("action=payments"); ?>"><?php _e('Payment History', 'affiliate-royale'); ?></a></li>
  <?php do_action('wafp-affiliate-dashboard-nav'); ?>
  <li><a href="<?php echo wp_logout_url(get_permalink($wafp_options->login_page_id)); ?>"><?php _e('Logout', 'affiliate-royale'); ?></a></li>
</ul>
