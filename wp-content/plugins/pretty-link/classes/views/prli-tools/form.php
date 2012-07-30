<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');
?>

<div class="wrap">
<script type="text/javascript">
function toggle_iphone_instructions()
{
  jQuery('.iphone_instructions').slideToggle();
}

</script>
<?php
  require(PRLI_VIEWS_PATH.'/shared/nav.php');
?>
  <?php echo PrliAppHelper::page_title(__('Tools', 'pretty-link')); ?>
  <h3><?php _e('Bookmarklet:', 'pretty-link'); ?></h3>
  <p><strong><a href="<?php echo PrliLink::bookmarklet_link(); ?>">Get PrettyLink</a></strong><br/>
  <span class="description"><?php _e('Just drag this "Get PrettyLink" link to your toolbar to install the bookmarklet. As you browse the web, you can just click this bookmarklet to create a pretty link from the current url you\'re looking at.  <a href="http://blairwilliams.com/pretty-link-bookmarklet/">(more help)</a>', 'pretty-link'); ?></span>
  <br/><br/><a href="javascript:toggle_iphone_instructions()"><strong><?php _e('Show iPhone Bookmarklet Instructions', 'pretty-link'); ?></strong></a>
  <div class="iphone_instructions" style="display: none"><?php _e('<strong>Note:</strong> iPhone users can install this bookmarklet in their Safari to create Pretty Links with the following steps:', 'pretty-link'); ?><br/>
    <ol>
      <li>Copy this text:<br/><code><?php echo PrliLink::bookmarklet_link(); ?></code></li>
      <li>Tap the + button at the bottom of the screen</li>
      <li>Choose "Add Bookmark", rename your bookmark to "Get PrettyLink" (or whatever you want) and then "Save"</li>
      <li>Navigate through your Bookmarks folders until you find the new bookmark and click "Edit"</li>
      <li>Delete all the text from the address</li>
      <li>Paste the text you copied in Step 1 into the address field</li>
      <li>To save the changes hit "Bookmarks" and <strong>you're done!</strong> Now when you find a page you want to save off as a Pretty Link, just click the "Bookmarks" icon at the bottom of the screen and select your link.</li>
    </ol>
  </div>
<?php do_action('prli-add-tools'); ?>
</div>
