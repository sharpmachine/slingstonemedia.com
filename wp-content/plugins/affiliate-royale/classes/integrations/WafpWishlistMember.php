<?php
/** This is a special controller that handles all of the 
  * Wishlist Member specific functions for the Affiliate Program.
  */

require_once(WAFP_INTEGRATIONS_PATH . "/WafpPayPal.php");

class WafpWishlistMember extends WafpIntegration
{
  
  function __construct($sandbox=false,$debug=false)
  {
    //add_action('wlmem_paypal_display_custom_var', array(&$this,'custom_instructions'));
    add_action('wlmem_paypal_ipn_response', array(&$this,'process_ipn'));
  }

  public function custom_instructions()
  {
?>

<li><?php _e('Make Sure You Uncheck "Save Button at PayPal" in Step 2', 'affiliate-royale'); ?></li>
<li><?php _e('Make Sure "Add advanced variables" is checked in Step 3 and add the following text into the "Advanced Variables" text area:', 'affiliate-royale'); ?><br/>
<pre><strong>custom=[wafp_custom_args]</strong></pre>
</li>
<li><?php _e('Click "Create Button"', 'affiliate-royale'); ?></li>
<li><?php _e('Click "Remove code protection"', 'affiliate-royale'); ?></li>
<?php
  }

  function display_instructions()
  {
    
  }
  function wl_process_ipn()
  {
    $this->debug = true;
    $this->_email_status("Got Wishlist Member IPN:\n" . WafpUtils::array_to_string($_POST, true) . "\n");

    $this->_process_message();
  }
}
