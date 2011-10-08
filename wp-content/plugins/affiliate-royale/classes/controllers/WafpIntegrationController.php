<?php
class WafpIntegrationController {
  function display_form() {
    global $wafp_options;
    require( WAFP_VIEWS_PATH . "/wafp-integration/form.php" );
  }
}
?>
