<?php

class WafpOptionsController
{
  function __construct()
  {
    add_action('admin_head', array(&$this,'add_commission_level'));
    add_action('wp_ajax_add_commission_level', array(&$this,'add_commission_level_callback'));
    
    if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'affiliate-royale-options')
    {
      add_action('admin_print_scripts', array(&$this,'add_tinymce_js') );
      add_action('admin_print_styles', array(&$this,'add_tinymce_css') );
    }
  }
  
  function add_tinymce_js()
  {
    add_thickbox();
    //wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('jquery-ui-accordion', WAFP_URL . '/js/jquery.ui.accordion.min.js', array('jquery-ui-core','jquery-ui-widget'), '1.8.12');
    
  }
  
  function add_tinymce_css()
  {
    wp_enqueue_style( 'jquery-ui', WAFP_URL . '/css/jquery-ui/jquery-ui-1.8.16.custom.css', '1.8.16');
  }

  function add_commission_level() {
  ?>
    <script type="text/javascript" >
      jQuery(document).ready(function($) {
        jQuery('#wafp_add_commission_level').click( function() {
          var data = {
            action: 'add_commission_level',
            level: jQuery('#wafp_commission_levels').children().length + 1
          };
      
          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
          jQuery.post(ajaxurl, data, function(response) {
            jQuery('#wafp_commission_levels').append(response);
            jQuery('#wafp_commission_levels li:last').slideDown('fast');
            jQuery('#wafp_remove_commission_level').show();
          });
        });
        
        if( jQuery('#wafp_commission_levels' ).children().length > 1 ) {
          jQuery('#wafp_remove_commission_level').show();
        }
        
        jQuery('#wafp_remove_commission_level').click( function() {
          jQuery('#wafp_commission_levels li:last').slideUp('fast',function() {
            jQuery('#wafp_commission_levels li:last').remove();
            
            if( jQuery('#wafp_commission_levels' ).children().length < 2 ) {
              jQuery('#wafp_remove_commission_level').hide();
            }
          });
        });
      });
    </script>
  <?php
  }
  
  function add_commission_level_callback() {
    global $wafp_options;
    $level = $_REQUEST['level'];
    
    ?>
    <li class="wafp-hidden" id="wafp-level-<?php echo $level; ?>"><?php printf(__('Level %d:', 'affiliate-royale'),$level); ?> <input id="<?php echo $wafp_options->commission_str; ?>_<?php echo $level; ?>" class="form-field" size="3" value="<?php printf('%0.2f', $wafp_options->commission[$level-1]); ?>" name="<?php echo $wafp_options->commission_str; ?>[]">%</li>
    <?php
    
    die(); // this is required to return a proper result
  }
  
  function route()
  {
    $action = (isset($_REQUEST['action'])?$_REQUEST['action']:null);
    if($action=='process-form')
      return $this->process_form();
    else
      return $this->display_form();
  }

  function display_form()
  {
    global $wafp_options;
    if(WafpUtils::is_logged_in_and_an_admin())
    {    
      if(!$wafp_options->setup_complete)
        require(WAFP_VIEWS_PATH . '/shared/must_configure.php');
        
      //if(!get_option('users_can_register'))
      //  require(WAFP_VIEWS_PATH . '/shared/wp_cant_register.php');
      
      require(WAFP_VIEWS_PATH . '/wafp-options/form.php');
    }
  }

  function process_form()
  {
    global $wafp_options;
    
    if(WafpUtils::is_logged_in_and_an_admin())
    {
      $errors = array();
      
      $errors = apply_filters('wafp_validate_options', $wafp_options->validate($_POST,$errors));
      
      $wafp_options->update($_POST);
      
      if( count($errors) > 0 )
        require(WAFP_VIEWS_PATH . '/shared/errors.php');
      else
      {
        do_action('wafp_process_options');
        $wafp_options->store();
        require(WAFP_VIEWS_PATH . '/wafp-options/options_saved.php');
      }
      
      if(!$wafp_options->setup_complete)
        require(WAFP_VIEWS_PATH . '/shared/must_configure.php');
        
      //if(!get_option('users_can_register'))
      //  require(WAFP_VIEWS_PATH . '/shared/wp_cant_register.php');

      require(WAFP_VIEWS_PATH . '/wafp-options/form.php');
    }
  }
}
?>
