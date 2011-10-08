<img style="padding: 2px 5px 5px 2px; vertical-align: top;" src="<?php echo WAFP_IMAGES_URL; ?>/affiliate_royale_logo_small.png" /><span class="description" style="vertical-align: top; height: 55px; min-height: 55px;"><?php _e('Your 7 day Affiliate activity:', 'affiliate-royale'); ?></span>
<?php
  $chart_height = "200px";
  $chart_id = "wafp-weekly-stats";
?>
<?php require(WAFP_VIEWS_PATH . '/shared/flot_chart.php'); ?>
<p><a href="<?php echo admin_url('admin.php?page=affiliate-royale-reports'); ?>" class="button"><?php _e('View More Affiliate Royale Reports', 'affiliate-royale'); ?></a></p>
