<?php
class WafpOptionsHelper
{
  function wp_pages_dropdown($field_name, $page_id=0, $auto_page='')
  {
    global $wafp_blogurl;
    $pages = WafpAppHelper::get_pages();
    $selected_page_id = (isset($_POST[$field_name])?$_POST[$field_name]:$page_id);

    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="wafp-dropdown wafp-pages-dropdown">
      <?php if(!empty($auto_page)) { ?>
        <option value="__auto_page:<?php echo $auto_page; ?>"><?php _e('- Auto Create New Page -', 'affiliate-royale'); ?>&nbsp;</option>
      <?php } else { ?>
        <option>&nbsp;</option>
      <?php
        }

        foreach($pages as $page)
        {    
          $selected = (((isset($_POST[$field_name]) and $_POST[$field_name] == $page->ID) or (!isset($_POST[$field_name]) and $page_id == $page->ID))?' selected="selected"':'');
          ?>
          <option value="<?php echo $page->ID; ?>" <?php echo $selected; ?>><?php echo $page->post_title; ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
    
    if($selected_page_id) {
        $permalink = get_permalink($selected_page_id);
    ?>
&nbsp;<a href="<?php echo $wafp_blogurl; ?>/wp-admin/post.php?post=<?php echo $selected_page_id; ?>&action=edit" target="_blank" class="button"><?php _e('Edit', 'affiliate-royale'); ?></a>
    <?php
    ?><a href="<?php echo $permalink; ?>" target="_blank" class="button"><?php _e('View', 'affiliate-royale'); ?></a>
    <?php
    }
  }
  
  function payment_types_dropdown($field_name, $payment_type)
  {
    $payment_types = array( 'paypal' => __('PayPal', 'affiliate-royale'),
                            'manual' => __('Other', 'affiliate-royale') );
    $field_value = isset($_POST[$field_name])?$_POST[$field_name]:null;
    
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="wafp-dropdown wafp-payment-types-dropdown">
      <?php
        foreach($payment_types as $curr_type => $curr_label)
        {
          ?>
          <option value="<?php echo $curr_type; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $curr_type) or (!isset($_POST[$field_name]) and $payment_type == $curr_type))?' selected="selected"':''); ?>><?php echo $curr_label; ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }
  
  function payment_currencies_dropdown($field_name, $payment_currency) {
    $payment_currencies = array( '$', '£', '€', '¥' );
    $field_value = isset($_POST[$field_name])?$_POST[$field_name]:null;
    
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="wafp-dropdown wafp-payment-currencies-dropdown">
      <?php
        foreach($payment_currencies as $curr_currency)
        {
          ?>
          <option value="<?php echo $curr_currency; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $curr_currency) or (!isset($_POST[$field_name]) and $payment_currency == $curr_currency))?' selected="selected"':''); ?>><?php echo $curr_currency; ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }
  
  function payment_format_dropdown($field_name, $format) {
    $payment_formats = array( '#,###.##', '#.###,##' );
    $field_value = isset($_POST[$field_name])?$_POST[$field_name]:null;
    
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="wafp-dropdown wafp-payment-formats-dropdown">
      <?php
        foreach($payment_formats as $curr_format)
        {
          ?>
          <option value="<?php echo $curr_format; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $curr_format) or (!isset($_POST[$field_name]) and $format == $curr_format))?' selected="selected"':''); ?>><?php echo $curr_format; ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }
  
  function payment_currency_code_dropdown($field_name, $code) {
    $codes = array( 'USD', 'GBP', 'EUR', 'JPY', 'AUD', 'CAD', 'HKD', 'NZD', 'SGD' );
    $field_value = isset($_POST[$field_name])?$_POST[$field_name]:null;
    
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="wafp-dropdown wafp-payment-formats-dropdown">
      <?php
        foreach($codes as $curr_code)
        {
          ?>
          <option value="<?php echo $curr_code; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $curr_code) or (!isset($_POST[$field_name]) and $code == $curr_code))?' selected="selected"':''); ?>><?php echo $curr_code; ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }
}
?>
