<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');
?>
<script type="text/javascript">
  window.location = '<?php echo esc_url_raw($redirect_url); ?>';
</script>
