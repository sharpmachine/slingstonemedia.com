<?php
class WafpTransactionsController
{
  function track( $amount, $order_id, $product_id='', $user_id='', $subscription_id='', $response='' )
  {
    WafpTransaction::track($amount, $order_id, $product_id, $user_id, $subscription_id, $response);
    exit;
  }
}
?>