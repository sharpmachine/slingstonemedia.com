<?php
/*
Integration of Aweber into Affiliate Royale
*/

  class WafpAweberController
  {
    function WafpAweberController()
    {
      add_action('wafp_display_options',    array( &$this, 'display_option_fields' ));
      //add_filter('wafp_validate_options', array( &$this, 'validate_option_fields'));
      //add_action('wafp_update_options',   array( &$this, 'update_option_fields'));
      add_action('wafp_process_options',    array( &$this, 'store_option_fields'));

      add_action('wafp-user-signup-fields', array( &$this, 'display_signup_field' ));
      //add_filter('wafp-validate-signup',    array( &$this, 'validate_signup_fields' ));
      add_action('wafp-process-signup',     array( &$this, 'process_signup' ));

      add_action('wafp_signup_thankyou_message', array( &$this, 'thank_you_message'));
    }

    function display_option_fields()
    {
      if(isset($_POST['wafpaweber_listname']) and !empty($_POST['wafpaweber_listname']))
        $aweber_listname = $_POST['wafpaweber_listname'];
      else
      {
        $aweber_listname = get_option('wafpaweber_listname');
      }
        
      if(isset($_POST['wafpaweber_text']) and !empty($_POST['wafpaweber_text']))
        $aweber_text = $_POST['wafpaweber_text'];
      else
      {
        $aweber_text = get_option('wafpaweber_text');
      }

      ?>
        <h4><a href="#aweber"><?php _e('AWeber Signup Integration', 'affiliate-royale'); ?></a></h4>
        <div>
          <p>
            <label><?php _e('AWeber List Name', 'affiliate-royale'); ?>:&nbsp;
            <input type="text" name="wafpaweber_listname" id="wafpaweber_listname" value="<?php echo $aweber_listname; ?>" class="wafp-text-input form-field" size="20" tabindex="19" /></label><br/>
            <span class="description"><?php _e('Enter the AWeber mailing list name that you want users signed up for when they sign up for Affiliate Royale.', 'affiliate-royale'); ?></span>
          </p>
          <p>
            <label><?php _e('Signup Checkbox Label', 'affiliate-royale'); ?>:&nbsp;
            <input type="text" name="wafpaweber_text" id="wafpaweber_text" value="<?php echo $aweber_text; ?>" class="form-field" size="75" tabindex="20" /></label><br/>
            <span class="description"><?php _e('This is the text that will display on the signup page next to your mailing list opt-out checkbox.', 'affiliate-royale'); ?></span>
          </p>
        </div>
      <?php
    }
    
    function validate_option_fields($errors)
    {
      // Nothing to validate yet -- if ever
    }
    
    function update_option_fields()
    {
      // Nothing to do yet -- if ever
    }
    
    function store_option_fields()
    {
      update_option('wafpaweber_listname', $_POST['wafpaweber_listname']);
      update_option('wafpaweber_text', stripslashes($_POST['wafpaweber_text']));
    }
    
    function display_signup_field()
    {
      global $wafp_user, $wafp_blogname;
      
      $listname = get_option('wafpaweber_listname');
      if (!empty($listname))
      {
        if(isset($_POST['wafpaweber_opt_in_set']))
          $checked = isset($_POST['wafpaweber_opt_in'])?' checked="checked"':'';
        else
          $checked = ' checked="checked"';

        $message = get_option('wafpaweber_text');
        
        if(!$message or empty($message))
          $message = sprintf(__('Sign Up for the %s Newsletter', 'affiliate-royale'), $wafp_blogname);

        ?>
        <tr>
          <td valign="top" colspan="2">
            <input type="hidden" name="wafpaweber_opt_in_set" value="Y" />
            <input type="checkbox" name="wafpaweber_opt_in" style="width: 25px;" id="wafpaweber_opt_in"<?php echo $checked; ?>/><?php echo $message; ?><br/><small><a href="http://www.aweber.com/permission.htm" target="_blank"><?php _e('We Respect Your Privacy', 'affiliate-royale'); ?></a></small><br/>
          </td>
        </tr>
        <?php
       }
    }
    
    function validate_signup_field($errors)
    {
      // Nothing to validate -- if ever
    }
    
    function process_signup($user_id)
    {
      if(isset($_POST['wafpaweber_opt_in']))
      {
        $aweber_listname = get_option('wafpaweber_listname');
        $aweber_url      = "http://www.aweber.com/scripts/addlead.pl";
        $user            = new WafpUser($user_id);
        
        if( !class_exists( 'WP_Http' ) )
          include_once( ABSPATH . WPINC. '/class-http.php' );

        $aweber_body = array(
          'listname' => $aweber_listname,
          'redirect' => 'http://www.aweber.com/thankyou-coi.htm?m=text',
          'meta_adtracking' => 'affiliate-royale',
          'meta_message' => '1',
          'meta_forward_vars' => '1',
          'name'  => $user->get_full_name(),
          'email' => $user->get_field('user_email')
        );

        $request = new WP_Http();
        $result = $request->request( $aweber_url, array( 'method' => 'POST', 'body' => $aweber_body) );
      }
      
      // $result['response'] -- nothing really to do with this either -- right?
    }
  
    function thank_you_message()
    {
      if(isset($_POST['wafpaweber_opt_in']))
      {
      ?>
        <h3><?php _e("You're Almost Done - Activate Your Newsletter Subscription!", 'affiliate-royale'); ?></h3>
        <p><?php _e("You've just been sent an email that contains a <strong>confirm link</strong>.", 'affiliate-royale'); ?></p>
        <p><?php _e("In order to activate your subscription, check your email and click on the link in that email.
           You will not receive your subscription until you <strong>click that link to activate it</strong>.", 'affiliate-royale'); ?></p>
        <p><?php _e("If you don't see that email in your inbox shortly, fill out the form again to have another copy of it sent to you.", 'affiliate-royale'); ?></p>
      <?php
      }
    }
  } //END CLASS
?>
