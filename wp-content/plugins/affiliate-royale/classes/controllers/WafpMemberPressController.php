<?php
/** This is a special controller that handles all of the MemberPress specific
  * functions for the Affiliate Program.
  */
class WafpMemberPressController {

  function WafpMemberPressController()
  {
    add_action('mepr-track-signup', array(&$this, 'track_signup'), 10, 4);
    add_action('mepr-track-transaction', array(&$this, 'track_transaction'), 10, 4);
  }

  // This sets the referring affiliate of this user in the database -- we'll
  // refer back to this later when the user's transaction is completed
  public function track_signup($product_price, $user, $product_id, $transaction_id)
  {
    global $wafp_options;

    $affiliate_id = $_COOKIE['wafp_click'];

    if(isset($affiliate_id) and !empty($affiliate_id))
      update_user_meta($user->ID, 'wafp-affiliate-referrer', $affiliate_id);
  }

  /* Tracks when a transaction completes */
  public function track_transaction($product_price, $transaction_id, $product_id, $user_id)
  {
    $product = get_post($product_id);
    WafpTransaction::track( $product_price, $transaction_id, ($product?$product->post_title:$product_id), $user_id );
  }
}
?>
