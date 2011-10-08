<div class="wrap">
<h2 id="wafp_title" style="margin: 10px 0px 0px 0px; padding: 0px 0px 0px 122px; height: 64px; background: url(<?php echo WAFP_URL . "/images/affiliate_royale_logo_64.png"; ?>) no-repeat">&nbsp;&nbsp;<?php _e('Links &amp; Banners', 'affiliate-royale'); ?></h2>
<span class="description"><?php _e('To create a banner just hit "Add New" enter a Target URL and hit "Update Links &amp; Banners." To create a simple link just hit "Add New," enter a Target URL &amp; an image file and then hit "Update Links &amp; Banners."', 'affiliate-royale'); ?></span>
<br/>
<form name="wafp_options_form" method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="action" value="process-form">
<?php wp_nonce_field('update-links'); ?>
<table class="widefat post fixed">
  <thead>
    <tr>
      <th class="manage-column" width="10%"><?php _e('Link Type', 'affiliate-royale'); ?></th>
      <th class="manage-column" width="35%"><?php _e('Target URL', 'affiliate-royale'); ?></th>
      <th class="manage-column" width="55%"><?php _e('Image', 'affiliate-royale'); ?></th>
    </tr>
  </thead>
  <tbody>
  <?php
  foreach($links as $link)
  {
    ?>
    <tr id="wafp-link-<?php echo $link->rec->id; ?>">
      <td><strong><?php echo (isset($link->rec->image) and !empty($link->rec->image))?"Banner":"Text"; ?></strong></td>
      <td valign="bottom">
        <input type="text" id="wafp_link_url[<?php echo $link->rec->id; ?>]" name="wafp_link_url[<?php echo $link->rec->id; ?>]" style="width: 100%;" value="<?php echo $link->rec->target_url; ?>" /></td>
      <td valign="bottom">
          <a href="javascript:wafp_delete_link( <?php echo $link->rec->id; ?>, '<?php _e('Are you sure you want to delete this link?', 'affiliate-royale'); ?>' );" style="float: right;"><img src="<?php echo WAFP_IMAGES_URL . "/remove.png"; ?>" width="16" height="16" /></a>
        <?php
          if(isset($link->rec->image) and !empty($link->rec->image))
          {
            ?>
              <img src="<?php echo $link->image_url(); ?>" style="max-width: 400px; max-height: 400px;" />
              <span>(<?php echo $link->rec->width; ?>x<?php echo $link->rec->height; ?>)</span><br/>
              <input type="file" id="wafp_link_image_<?php echo $link->rec->id; ?>" name="wafp_link_image[<?php echo $link->rec->id; ?>]" value="<?php echo $link->image_path(); ?>" /></td>
            <?php
          }
        ?>
    </tr>
    <?php
  }
  ?>
    <tr class="wafp-new-link wafp-hidden">
      <td><select onchange="jQuery('#wafp_new_link_image').toggle()"><option value="text">Text&nbsp;</option><option value="banner">Banner</option></select></td>
      <td valign="bottom"><input type="text" id="wafp_new_link_url" name="wafp_new_link_url" style="width: 100%;" value="" /></td>
      <td valign="bottom">
        <a href="javascript:wafp_toggle_new_form();" style="float: right;"><img src="<?php echo WAFP_IMAGES_URL . "/remove.png"; ?>" width="16" height="16" /></a>
        <input type="file" id="wafp_new_link_image" name="wafp_new_link_image" value="" style="display:none;" />
      </td>
    </tr>
  </tbody>
</table>
<p class="wafp-display-new-form"><a href="javascript:wafp_toggle_new_form();"><strong><?php _e('Add New', 'affiliate-royale'); ?></strong></a></p>
<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Links &amp; Banners', 'affiliate-royale') ?>" />
</p>
</form>
