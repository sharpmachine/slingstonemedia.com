<div class="wrap">
  <h2 id="wafp_title" style="margin: 10px 0px 0px 0px; padding: 0px 0px 0px 122px; height: 64px; background: url(<?php echo WAFP_URL . "/images/affiliate_royale_logo_64.png"; ?>) no-repeat">&nbsp;&nbsp;<?php _e('Options', 'affiliate-royale'); ?></h2>

  <form name="wafp_options_form" id="wafp_options_form" method="post" action="">
    <input type="hidden" name="action" value="process-form">
    <script type="text/javascript">// <![CDATA[
      /* Modify the form action based on what header is selected */
      jQuery( document ).ready( function() { 
        jQuery( "#afroy-options" ).accordion({ header: "h4", 
                                               autoHeight: false,
                                               navigation: true,
                                               change: function(event, ui) {
                                                 jQuery('#wafp_options_form').attr( 'action', 
                                                   ui.newHeader.children('a').attr('href'));
                                               }
                                             });
                                           });
    // ]]></script>
    <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Update Options', 'affiliate-royale') ?>" />
    </p>
    <div id="afroy-options">
      <div id="pages">
      <?php wp_nonce_field('update-options'); ?>
      <h4><a href="#pages"><?php _e('Affiliate Program Pages', 'affiliate-royale'); ?></a></h4>
      <div>
        <strong><?php _e('Affiliate Pages', 'affiliate-royale'); ?></strong><br/>
        <span class="description"><?php _e('Configure the WordPress pages you will use for your Affiliate Program.', 'affiliate-royale'); ?></span>
        <table>
          <tr>
            <td><?php _e('Affiliate Signup Page*:', 'affiliate-royale'); ?>&nbsp;</td>
            <td><?php WafpOptionsHelper::wp_pages_dropdown( $wafp_options->signup_page_id_str, $wafp_options->signup_page_id, __('Affiliate Signup', 'affiliate-royale') ); ?></td>
          </tr>
          <tr>
            <td><?php _e('Affiliate Login Page*:', 'affiliate-royale'); ?>&nbsp;</td>
            <td><?php WafpOptionsHelper::wp_pages_dropdown( $wafp_options->login_page_id_str, $wafp_options->login_page_id, __('Affiliate Login', 'affiliate-royale') ); ?></td>
          </tr>
          <tr>
            <td><?php _e('Affiliate Dashboard Page*:', 'affiliate-royale'); ?>&nbsp;</td>
            <td><?php WafpOptionsHelper::wp_pages_dropdown( $wafp_options->dashboard_page_id_str, $wafp_options->dashboard_page_id, __('Affiliate Dashboard', 'affiliate-royale') ); ?></td>
          </tr>
        </table>
      </div>
      
      <h4><a href="#commission"><?php _e('Commission Settings', 'affiliate-royale'); ?></a></h4>
      <div>
        <strong><?php _e('Commission Levels', 'affiliate-royale'); ?></strong><br/>
        <span class="description"><?php _e('Configure what percentage you want to pay your affiliates per sale.', 'affiliate-royale'); ?></span><br/>
        <ul id="wafp_commission_levels">
          <?php foreach( $wafp_options->commission as $index => $commish ) {
            $level = $index + 1;
            ?>
            <li><?php printf(__('Level %d:', 'affiliate-royale'),$level); ?> <input id="<?php echo $wafp_options->commission_str; ?>_<?php echo $level; ?>" class="form-field" size="3" value="<?php printf('%0.2f',$commish); ?>" name="<?php echo $wafp_options->commission_str; ?>[]">%</li>
          <?php } ?>
        </ul>
        
        <a href="javascript:" id="wafp_add_commission_level" class="button"><?php _e('add level', 'affiliate-royale'); ?></a><span id="wafp_remove_commission_level" class="wafp-hidden">&nbsp;<a href="javascript:" class="button"><?php _e('remove level', 'affiliate-royale'); ?></a></span>
        <br/><br/>
        <strong><?php _e('Recurring Comissions', 'affiliate-royale'); ?></strong><br/>
        <div><label for="<?php echo $wafp_options->recurring_str; ?>"><input type="checkbox" name="<?php echo $wafp_options->recurring_str; ?>" id="<?php echo $wafp_options->recurring_str; ?>"<?php echo (($wafp_options->recurring)?' checked="checked"':''); ?>/>&nbsp;<?php _e('Pay commissions on recurring transactions.','affiliate-royale'); ?></label></div>
      </div>
      
      <h4><a href="#dashboard"><?php _e('Dashboard Settings', 'affiliate-royale'); ?></a></h4>
      <div>
        <strong><?php _e('Welcome Message', 'affiliate-royale'); ?></strong><br/>
        <span class="description">&nbsp;<?php _e('This is the customized message your affiliates will see on their Affiliate Dashboard welcome page.', 'affiliate-royale'); ?></span><br/>
        <div id="poststuff">
          <?php the_editor($wafp_options->custom_message,$wafp_options->custom_message_str,'',false); ?><br/>
        </div>
        <br/>
        <strong><?php _e('Dashboard Style', 'affiliate-royale'); ?></strong><br/>
        <div class="wafp-field-label"><?php _e('Affiliate Dashboard Page Width', 'affiliate-royale'); ?></span>:&nbsp;<input class="form-field" id="<?php echo $wafp_options->dash_css_width_str; ?>" name="<?php echo $wafp_options->dash_css_width_str; ?>" value="<?php echo $wafp_options->dash_css_width; ?>" size="3" />&nbsp;<?php _e('px', 'affiliate-royale'); ?></div>
      </div>
      
      <h4><a href="#affiliates"><?php _e('Affiliate Settings', 'affiliate-royale'); ?></a></h4>
      <div>
        <strong><?php _e('Affiliate Payment Method', 'affiliate-royale'); ?></strong><br/>
        <span class="description"><?php echo _e('What method will you use to pay your affiliates?', 'affiliate-royale'); ?></span>
        <div class="wafp-field-label"><?php _e('Pay Affiliates With', 'affiliate-royale'); ?>:&nbsp;<?php WafpOptionsHelper::payment_types_dropdown( $wafp_options->payment_type_str, $wafp_options->payment_type ); ?></div><br/>
        
        <strong><?php _e('Affiliate Registration', 'affiliate-royale'); ?></strong><br/>
        <span class="description"><?php echo _e('Additional affiliate registration settings', 'affiliate-royale'); ?></span><br/>
        <div><label for="<?php echo $wafp_options->show_address_fields_str; ?>"><input type="checkbox" name="<?php echo $wafp_options->show_address_fields_str; ?>" id="<?php echo $wafp_options->show_address_fields_str; ?>"<?php echo (($wafp_options->show_address_fields)?' checked="checked"':''); ?>/>&nbsp;<?php _e('Collect address information from your affiliates?','affiliate-royale'); ?></label></div>
        <div><label for="<?php echo $wafp_options->show_tax_id_fields_str; ?>"><input type="checkbox" name="<?php echo $wafp_options->show_tax_id_fields_str; ?>" id="<?php echo $wafp_options->show_tax_id_fields_str; ?>"<?php echo (($wafp_options->show_tax_id_fields)?' checked="checked"':''); ?>/>&nbsp;<?php _e('Collect Tax ID #\'s from your affiliates?','affiliate-royale'); ?></label></div>
        <div><label for="<?php echo $wafp_options->make_new_users_affiliates_str; ?>"><input type="checkbox" name="<?php echo $wafp_options->make_new_users_affiliates_str; ?>" id="<?php echo $wafp_options->make_new_users_affiliates_str; ?>"<?php echo (($wafp_options->make_new_users_affiliates)?' checked="checked"':''); ?>/>&nbsp;<?php _e('Automatically make each new user an Affiliate?','affiliate-royale'); ?></label></div>
        <br/>
        <strong><?php _e('Affiliate Cookie Settings', 'affiliate-royale'); ?></strong><br/>
        <div class="wafp-field-label"><?php _e('Expire Cookie After', 'affiliate-royale'); ?></span>:&nbsp;<input class="form-field" id="<?php echo $wafp_options->expire_after_days_str; ?>" name="<?php echo $wafp_options->expire_after_days_str; ?>" value="<?php echo $wafp_options->expire_after_days; ?>" size="3" />&nbsp;<?php _e('Days', 'affiliate-royale'); ?></div>
      </div>
      
      <h4><a href="#integration"><?php _e('Payment Integration', 'affiliate-royale'); ?></a></h4>
      <div>
        <span class="description"><?php echo _e('What system do you use to accept payments?', 'affiliate-royale'); ?></span><br/>
        <div class="wafp-field-label"><?php _e('You Accept Payments Via', 'affiliate-royale'); ?>:&nbsp;
          <?php if(is_plugin_active('memberpress/memberpress.php')) { ?>
            <strong><?php _e('MemberPress', 'affiliate-royale'); ?></strong>
            <input type="hidden" name="<?php echo $wafp_options->integration_str; ?>" id="<?php echo $wafp_options->integration_str; ?>" value="memberpress" />
          <?php } else { ?>
            <?php
            $integration_str = 'general';
            $general_class  = ' wafp-hidden';
            $general_selected = '';
            
            $paypal_class  = ' wafp-hidden';
            $paypal_selected = '';
            
            $wishlist_class = ' wafp-hidden';
            $wishlist_selected = '';
            
            $shopp_class = ' wafp-hidden';
            $shopp_selected = '';
            
            $cart66_class = ' wafp-hidden';
            $cart66_selected = '';
            
            $authorize_class = ' wafp-hidden';
            $authorize_selected = '';
            
            if($_POST[$wafp_options->integration_str] == 'paypal' or $wafp_options->integration == 'paypal')
            {
              $paypal_class  = '';
              $paypal_selected = ' selected';
              $integration_str = 'paypal';
            }
            else if($_POST[$wafp_options->integration_str] == 'wishlist' or $wafp_options->integration == 'wishlist')
            {
              $wishlist_class = '';
              $wishlist_selected = ' selected';
              $integration_str = 'wishlist';
            }
            else if($_POST[$wafp_options->integration_str] == 'shopp' or $wafp_options->integration == 'shopp')
            {
              $shopp_class = '';
              $shopp_selected = ' selected';
              $integration_str = 'shopp';
            }
            else if($_POST[$wafp_options->integration_str] == 'cart66' or $wafp_options->integration == 'cart66')
            {
              $cart66_class = '';
              $cart66_selected = ' selected';
              $integration_str = 'cart66';
            }
            else if($_POST[$wafp_options->integration_str] == 'authorize' or $wafp_options->integration == 'authorize')
            {
              $authorize_class = '';
              $authorize_selected = ' selected';
              $integration_str = 'authorize';
            }
            else
            {
              $general_class = '';
              $general_selected = ' selected';
              $integration_str = 'general';
            }
          ?>
          <select name="<?php echo $wafp_options->integration_str; ?>" class="wafp-integration-dropdown" onchange="wafp_integration_toggle()">
            <option value="general"<?php echo $general_selected; ?>><?php _e('Other', 'affiliate-royale'); ?>&nbsp;</option>
            <option value="paypal"<?php echo $paypal_selected; ?>><?php _e('PayPal', 'affiliate-royale'); ?>&nbsp;</option>
            <option value="wishlist"<?php echo $wishlist_selected; ?>><?php _e('Wishlist Member + PayPal', 'affiliate-royale'); ?>&nbsp;</option>
            <option value="shopp"<?php echo $shopp_selected; ?>><?php _e('Shopp Plugin', 'affiliate-royale'); ?>&nbsp;</option>
            <option value="cart66"<?php echo $cart66_selected; ?>><?php _e('Cart66 Plugin', 'affiliate-royale'); ?>&nbsp;</option>
            <option value="authorize"<?php echo $authorize_selected; ?>><?php _e('Authorize.Net ARB', 'affiliate-royale'); ?>&nbsp;</option>
            <?php do_action('wafp-integration-dropdown'); ?>
          </select>
          <a href="javascript:" class="wafp-show-integration-option button" integration-option="<?php echo $integration_str; ?>"><?php _e('Integration Instructions', 'affiliate-royale'); ?></a>
          <?php require( WAFP_VIEWS_PATH . '/wafp-options/general_integration.php' ); ?>
          <?php require( WAFP_VIEWS_PATH . '/wafp-options/wishlist_integration.php' ); ?>
          <?php require( WAFP_VIEWS_PATH . '/wafp-options/shopp_integration.php' ); ?>
          <?php require( WAFP_VIEWS_PATH . '/wafp-options/cart66_integration.php' ); ?>
          <?php require( WAFP_VIEWS_PATH . '/wafp-options/paypal_integration.php' ); ?>
          <?php require( WAFP_VIEWS_PATH . '/wafp-options/authorize_integration.php' ); ?>
          <?php } ?>
        </div>
      </div>
      
      <h4><a href="#emails"><?php _e('Email Notifications', 'affiliate-royale'); ?></a></h4>
      <div>
        <strong><?php _e('Affiliate Welcome Email', 'affiliate-royale'); ?></strong><br/>
        <p><label for="<?php echo $wafp_options->welcome_email_str; ?>"><input type="checkbox" name="<?php echo $wafp_options->welcome_email_str; ?>" id="<?php echo $wafp_options->welcome_email_str; ?>"<?php echo (($wafp_options->welcome_email)?' checked="checked"':''); ?>/>&nbsp;<?php _e('Send Welcome Email','affiliate-royale'); ?></label></p>
        <ul style="width: 100%;">
          <li style="width: 100%;">
            <span class="wafp-field-label"><?php _e('Welcome Email Subject', 'affiliate-royale'); ?></span><br/>
            <input style="width: 100%;" class="form-field" type="text" id="<?php echo $wafp_options->welcome_email_subject_str; ?>" name="<?php echo $wafp_options->welcome_email_subject_str; ?>" value="<?php echo $wafp_options->welcome_email_subject; ?>" />
          </li>
          <li style="width: 100%;">
            <span class="wafp-field-label"><?php _e('Welcome Email Body', 'affiliate-royale'); ?></span><br/>
            <textarea style="width: 100%; min-height: 150px;" class="form-field" id="<?php echo $wafp_options->welcome_email_body_str; ?>" name="<?php echo $wafp_options->welcome_email_body_str; ?>"><?php echo $wafp_options->welcome_email_body; ?></textarea>
          </li>
        </ul>
        <strong><?php _e('Admin Commission Notification Email', 'affiliate-royale'); ?></strong><br/>
        <p><label for="<?php echo $wafp_options->admin_email_str; ?>"><input type="checkbox" name="<?php echo $wafp_options->admin_email_str; ?>" id="<?php echo $wafp_options->admin_email_str; ?>"<?php echo (($wafp_options->admin_email)?' checked="checked"':''); ?>/>&nbsp;<?php _e('Send Admin Email','affiliate-royale'); ?></label></p>
        <ul style="width: 100%;">
          <li style="width: 100%;">
            <span class="wafp-field-label"><?php _e('Admin Email Subject', 'affiliate-royale'); ?></span><br/>
            <input style="width: 100%;" class="form-field" type="text" id="<?php echo $wafp_options->admin_email_subject_str; ?>" name="<?php echo $wafp_options->admin_email_subject_str; ?>" value="<?php echo $wafp_options->admin_email_subject; ?>" />
          </li>
          <li style="width: 100%;">
            <span class="wafp-field-label"><?php _e('Admin Email Body', 'affiliate-royale'); ?></span><br/>
            <textarea style="width: 100%; min-height: 150px;" class="form-field" id="<?php echo $wafp_options->admin_email_body_str; ?>" name="<?php echo $wafp_options->admin_email_body_str; ?>"><?php echo $wafp_options->admin_email_body; ?></textarea>
          </li>
        </ul>
        <strong><?php _e('Affiliate Commission Notification Email', 'affiliate-royale'); ?></strong><br/>
        <p><label for="<?php echo $wafp_options->affiliate_email_str; ?>"><input type="checkbox" name="<?php echo $wafp_options->affiliate_email_str; ?>" id="<?php echo $wafp_options->affiliate_email_str; ?>"<?php echo (($wafp_options->affiliate_email)?' checked="checked"':''); ?>/>&nbsp;<?php _e('Send Affiliate Email','affiliate-royale'); ?></label></p>
        <ul>
          <li>
            <span class="wafp-field-label"><?php _e('Affiliate Email Subject', 'affiliate-royale'); ?></span><br/>
            <input style="width: 100%;" class="form-field" type="text" id="<?php echo $wafp_options->affiliate_email_subject_str; ?>" name="<?php echo $wafp_options->affiliate_email_subject_str; ?>" value="<?php echo $wafp_options->affiliate_email_subject; ?>" />
          </li>
          <li>
            <span class="wafp-field-label"><?php _e('Affiliate Email Body', 'affiliate-royale'); ?></span><br/>
            <textarea style="width: 100%; min-height: 150px;" class="form-field" id="<?php echo $wafp_options->affiliate_email_body_str; ?>" name="<?php echo $wafp_options->affiliate_email_body_str; ?>"><?php echo $wafp_options->affiliate_email_body; ?></textarea>
          </li>
        </ul>
      </div>
      
      <h4><a href="#international"><?php _e('International Settings', 'affiliate-royale'); ?></a></h4>
      <div>
        <strong><?php _e('Currency Settings', 'affiliate-royale'); ?></strong><br/>
        <span class="description"><?php _e('Customize the display and type of the currency your affiliate program will use', 'affiliate-royale'); ?></span>
        <div class="wafp-field-label"><?php _e('Currency Code:', 'affiliate-royale'); ?></span>&nbsp;<?php WafpOptionsHelper::payment_currency_code_dropdown($wafp_options->currency_code_str, $wafp_options->currency_code); ?></div>
        <div class="wafp-field-label"><?php _e('Currency Symbol:', 'affiliate-royale'); ?></span>&nbsp;<?php WafpOptionsHelper::payment_currencies_dropdown($wafp_options->currency_symbol_str, $wafp_options->currency_symbol); ?></div>
        <div class="wafp-field-label"><?php _e('Currency Format:', 'affiliate-royale'); ?></span>&nbsp;<?php WafpOptionsHelper::payment_format_dropdown($wafp_options->number_format_str, $wafp_options->number_format); ?></div>
      </div>
    </div>
  
    <?php do_action('wafp_display_options'); ?>
    
  </div>
    
  <p class="submit">
    <input type="submit" name="Submit" value="<?php _e('Update Options', 'affiliate-royale') ?>" />
  </p>
    
</form>
</div>