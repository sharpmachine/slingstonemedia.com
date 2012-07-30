<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<table class="form-table">
  <tr class="form-field">
    <td width="125px" valign="top"><?php _e('Redirection Type*:', 'pretty-link'); ?></td>
    <td>
	  <select id="redirect_type" name="redirect_type" style="padding: 0px; margin: 0px;">
	    <option value="307"<?php echo esc_html($values['redirect_type']['307']); ?>><?php _e("307 (Temporary)", 'pretty-link') ?>&nbsp;</option>
	    <option value="301"<?php echo esc_html($values['redirect_type']['301']); ?>><?php _e("301 (Permanent)", 'pretty-link') ?>&nbsp;</option>
	    <?php do_action('prli_redirection_types', $values); ?>
	  </select>
	  <?php 
	    global $prli_update;
	    if(!$prli_update->pro_is_installed_and_authorized()) {
		  ?>
	      <p class="description"><?php printf(__("To Enable Cloaked, Meta-Refresh, Javascript, Pixel and Pretty Bar Redirection, upgrade to %sPretty Link Pro%s", 'pretty-link'),'<a href="http://prettylinkpro.com">',"</a>") ?></p>
	  <?php } ?>
	</td>
  </tr>
  <tr id="prli_target_url" class="form-field ">
    <td valign="top"><?php _e("Target URL*:", 'pretty-link'); ?> </td>
    <td><textarea style="height: 50px;" name="url"><?php echo esc_html(htmlentities($values['url'],ENT_COMPAT,'UTF-8')); ?></textarea></td>
  </tr>
  <tr>
    <td valign="top"><?php _e("Pretty Link*:", 'pretty-link'); ?> </td>
    <td><strong><?php global $prli_blogurl; echo esc_html($prli_blogurl); ?></strong>/<input type="text" name="slug" value="<?php echo esc_attr($values['slug']); ?>" size="50"/></td>
  </tr>
  <tr class="form-field">
    <td width="75px" valign="top"><?php _e("Title:", 'pretty-link'); ?> </td>
    <td><input type="text" name="name" value="<?php echo esc_attr($values['name']); ?>" /></td>
  </tr>
</table>
<br/>
<h2 id="link-options-tabs" class="nav-tab-wrapper">
	<a href="#options-table" class="nav-tab nav-tab-active"><?php _e( 'Options', 'pretty-link' ) ?></a>
	<a href="#pro-options-table" class="nav-tab"><?php _e( 'Advanced', 'pretty-link' ) ?></a>
</h2>
<table id="options-table">
  <tr>
    <td valign="top" width="50%">
      <h3><?php _e("Group", 'pretty-link') ?></h3>
      <div class="pane">
        <select name="group_id" id="group_dropdown" style="padding: 0px; margin: 0px;">
          <option><?php _e("None", 'pretty-link') ?></option>
          <?php
            foreach($values['groups'] as $group)
            {
          ?>
              <option value="<?php echo esc_attr($group['id']); ?>"<?php echo esc_html($group['value']); ?>><?php echo esc_html($group['name']); ?>&nbsp;</option>
          <?php
            }
          ?>
        </select>
        <input class="defaultText" id="add_group_textbox" title="<?php _e('Add a New Group', 'pretty-link') ?>" type="text" prli_nonce="<?php echo wp_create_nonce('prli-add-new-group'); ?>" /><div id="add_group_message"></div>
        <p class="description"><?php _e('Select a Group for this Link', 'pretty-link') ?></p>
      </div>
      <br/>
      <h3><?php _e("SEO Options", 'pretty-link') ?></h3>
      <div class="pane">
        <input type="checkbox" name="nofollow" <?php echo esc_html($values['nofollow']); ?>/>&nbsp; <?php _e("'Nofollow' this Link", 'pretty-link') ?>
        <p class="description"><?php _e('Add a nofollow and noindex to this link\'s http redirect header', 'pretty-link')?>
      </div>
      <div id="prli_time_delay" style="display: none">
        <br/>
        <h3><?php _e('Delay Redirect (Seconds):', 'pretty-link'); ?></h3>
        <div class="pane">
          <input type="text" name="delay" value="<?php echo esc_attr($values['delay']); ?>" />
          <p class="description"><?php _e('Time in seconds to wait before redirecting', 'pretty-link') ?></p>
        </div>
      </div>
    </td>
    <td valign="top" width="50%">
      <h3><?php _e("Parameter Forwarding", 'pretty-link') ?></h3>
      <div class="pane">
        <input type="checkbox" name="param_forwarding" id="param_forwarding" <?php echo esc_html($values['param_forwarding']); ?>/>&nbsp;<?php _e("Parameter Forwarding Enabled", 'pretty-link') ?>
        <p class="description"><?php _e('Forward parameters passed to this link onto the Target URL', 'pretty-link') ?></p>
      </div><br/>
      <h3><?php _e("Tracking Options", 'pretty-link') ?></h3>
      <div class="pane">
        <input type="checkbox" name="track_me" <?php echo esc_html($values['track_me']); ?>/>&nbsp; <?php _e("Track Hits on this Link", 'pretty-link') ?>
        <p class="description"><?php _e('Enable Pretty Link\'s built-in hit (click) tracking', 'pretty-link') ?></p>
        <div id="prli_google_analytics" style="display: none">
          <input type="checkbox" name="google_tracking" <?php echo esc_attr($values['google_tracking']); ?>/>&nbsp; <?php _e('Enable Google Analytics Tracking on this Link', 'pretty-link') ?>
          <p class="description"><?php _e('Requires the Google Analyticator, Google Analytics for WordPress or Google Analytics Plugin installed and configured for this to work.', 'pretty-link') ?></p>
          <?php
          global $prli_update;
          if($prli_update->pro_is_installed_and_authorized()):
            if($ga_info = PrliProUtils::ga_installed()):
              ?>
              <p class="description"><?php printf(__('It appears that <strong>%s</strong> is currently installed. Pretty Link will attempt to use its settings to track this link.', 'pretty-link'), $ga_info['name']); ?></p>
              <?php
            else:
              ?>
                <p class="description"><strong><?php _e('No Google Analytics Plugin is currently installed. Pretty Link cannot track links using Google Analytics until one is.', 'pretty-link'); ?></strong></p>
              <?php
            endif;
          endif;
          ?>
        </div>
      </div><br/>
    </td>
  </tr>
</table>

<table id="pro-options-table">
<?php
  global $prli_update;
  if($prli_update->pro_is_installed_and_authorized()) {
    // Add stuff to the form here
    do_action('prli_link_fields',$id);
  }
  else {
?>
  <tr><td colspan="2"><h3><?php printf(__('To enable Double Redirection, Keyword Replacements, URL Replacements, URL Rotations, Split Tests, and more, %sUpgrade to Pretty Link Pro%s today!', 'pretty-link'), '<a href="http://prettylinkpro.com">', '</a>') ?></h3></td></tr>
<?php
  }
?>
</table>
