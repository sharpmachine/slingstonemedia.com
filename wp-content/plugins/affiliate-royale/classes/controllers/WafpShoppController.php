<?php
/** This is a special controller that handles all of the MemberPress specific
  * functions for the Affiliate Program.
  */
class WafpShoppController {

  function WafpShoppController()
  {
    add_action( 'shopp_order_success', array(&$this,'track_transaction') ); 
  }

  /* Tracks when a transaction completes */
  public function track_transaction($purchase)
  {
    WafpTransaction::track( $purchase->subtotal, $purchase->id, __('Shopp Purchase', 'affiliate-royale') );
  }
}
?>