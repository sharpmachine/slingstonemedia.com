<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');
?>

<div class="wrap">
<?php echo PrliAppHelper::page_title(__('Edit Link', 'pretty-link')); ?>

<?php
  require(PRLI_VIEWS_PATH.'/shared/errors.php');
?>

<form name="form1" method="post" action="<?php echo admin_url('admin.php?page=pretty-link'); ?>">
<input type="hidden" name="action" value="update">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<?php wp_nonce_field('update-options'); ?>

<?php
  require(PRLI_VIEWS_PATH.'/prli-links/form.php');
?>

<p class="submit">
<input type="submit" name="Submit" value="Update" />&nbsp;or&nbsp;<a href="<?php echo admin_url('admin.php?page=pretty-link'); ?>">Cancel</a>
</p>

</form>
</div>