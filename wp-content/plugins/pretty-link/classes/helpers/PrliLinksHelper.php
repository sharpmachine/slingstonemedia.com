<?php

class PrliLinksHelper {

  public static function groups_dropdown($fieldname, $value='', $extra_options=array(), $classes='') {
    global $prli_group;
    $groups = $prli_group->getAll();

    $idname = preg_match('#^.*\[(.*?)\]$#',$fieldname,$matches)?$matches[1]:$fieldname;

    ?>
    <select id="<?php echo esc_html($idname); ?>" name="<?php echo esc_html($fieldname); ?>" class="<?php echo $classes; ?>">
      <?php if( empty($extra_options) ): ?>
        <option><?php _e('None', 'pretty-link'); ?>&nbsp;</option>
      <?php else:
              foreach($extra_options as $exoptkey => $exoptval): ?>
                <option value="<?php echo $exoptval; ?>"><?php echo $exoptkey; ?>&nbsp;</option>
      <?php   endforeach;
            endif; ?>
      <?php foreach($groups as $group): ?>
        <?php $selected = ($value==$group->id)?' selected="selected"':''; ?>
        <option value="<?php echo $group->id; ?>"<?php echo $selected; ?>><?php echo $group->name; ?>&nbsp;</option>
      <?php endforeach; ?>
    </select>
    <?php
  }

  public static function redirect_type_dropdown($fieldname, $value='', $extra_options=array(), $classes='') {
    $selected = ' selected="selected"';
    $idname = preg_match('#^.*\[(.*?)\]$#',$fieldname,$matches)?$matches[1]:$fieldname;
    ?>
    <select id="<?php echo $idname; ?>" name="<?php echo $fieldname; ?>" class="<?php echo $classes; ?>">
      <?php if( !empty($extra_options) ):
              foreach($extra_options as $exoptkey => $exoptval): ?>
                <option value="<?php echo $exoptval; ?>"><?php echo $exoptkey; ?>&nbsp;</option>
      <?php   endforeach;
            endif; ?>
      <option value="307"<?php echo ($value==307)?$selected:''; ?>><?php _e("307 (Temporary)", 'pretty-link') ?>&nbsp;</option>
      <option value="301"<?php echo ($value==301)?$selected:''; ?>><?php _e("301 (Permanent)", 'pretty-link') ?>&nbsp;</option>
      <?php do_action('prli_redirection_types', array()); ?>
    </select>
    <?php
  }

  public static function bulk_action_dropdown() {
    ?>
    <div class="prli_bulk_action_dropdown">
      <select class="prli_bulk_action">
        <option value="-1"><?php _e('Bulk Actions', 'pretty-link'); ?>&nbsp;</option>
        <option value="edit"><?php _e('Edit', 'pretty-link'); ?>&nbsp;</option>
        <option value="delete"><?php _e('Delete', 'pretty-link'); ?>&nbsp;</option>
      </select>
      <a href="javascript:" class="prli_bulk_action_apply button" data-confmsg="<?php _e('Are you sure you want to delete the selected links?', 'pretty-link'); ?>" data-url="<?php echo admin_url('admin.php'); ?>" data-wpnonce="<?php echo wp_create_nonce('prli_bulk_update'); ?>" ><?php _e('Apply', 'pretty-link'); ?></a>
    </div>
    <?php
  }

  public static function bulk_action_checkbox_dropdown($input_name, $input_title, $classes='') {
    $idname = preg_match('#^.*\[(.*?)\]$#',$input_name,$matches)?$matches[1]:$input_name;
    ?>
      <div class="bacheck-title"><?php echo $input_title; ?></div>
      <select name="<?php echo $input_name; ?>" class="<?php echo $classes; ?>" id="<?php echo $idname; ?>">
        <option value="##nochange##"><?php _e('- No Change -', 'pretty-link'); ?>&nbsp;</option>
        <option value="off"><?php _e('Off', 'pretty-link'); ?>&nbsp;</option>
        <option value="on"><?php _e('On', 'pretty-link'); ?>&nbsp;</option>
      </select>
    <?php
  }
}
