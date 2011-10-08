<?php if( isset($redirect_to) and !empty($redirect_to) ) { ?>
<script type="text/javascript">
  window.location.href="<?php echo $redirect_to; ?>";
</script>
<?php } ?>
<p class="wafp-already-logged-in"><?php printf( __('You\'re already logged in. %1$sLogout.%2$s', 'affiliate-royale'), '<a href="'. wp_logout_url($redirect_to) . '">', '</a>'); ?></p>
