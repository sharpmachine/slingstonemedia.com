<?php

class WafpSubscription
{
  static public function register()
  {
    add_action( 'init', 'WafpSubscription::register_post_type', 0 );
  }

  static public function register_post_type()
  {
    register_post_type( 'wafp-subscriptions',
      array(
        'labels' => array(
          'name' => __( 'Subscriptions' , 'affiliate-royale'),
          'singular_name' => __( 'Subscription' , 'affiliate-royale'),
          'add_new_item' => __('Add New Subscription', 'affiliate-royale'),
          'edit_item' => __('Edit Subscription', 'affiliate-royale'),
          'new_item' => __('New Subscription', 'affiliate-royale'),
          'view_item' => __('View Subscription', 'affiliate-royale'),
          'search_items' => __('Search Subscription', 'affiliate-royale'),
          'not_found' => __('No Subscription found', 'affiliate-royale'),
          'not_found_in_trash' => __('No Subscription found in Trash', 'affiliate-royale'),
          'parent_item_colon' => __('Parent Subscription:', 'affiliate-royale')
        ),
        /*
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => true,
        'register_meta_box_cb' => array(&$this, 'add_meta_boxes'),
        'rewrite' => false,
        'supports' => array('none'),
        'menu_icon' => MEPR_URL . "/images/memberpress-16.png"
        */
        'public' => false,
        'show_ui' => false,
        'capability_type' => 'post',
        'hierarchical' => true,
        'supports' => array('none')
      )
    );
  }

  static public function create($subscr_id, $subscr_type="generic", $affiliate_id=0, $title="Subscription", $ip_addr="")
  {
    if( $subscr = self::subscription_exists($subscr_id) )
      return $subscr->subscription->ID;

    $post_id = wp_insert_post(array('post_title' => $title, 'post_type' => 'wafp-subscriptions', 'post_status' => 'publish', 'comment_status' => 'closed'));

    add_post_meta( $post_id, 'wafp_subscr_id',   $subscr_id );
    add_post_meta( $post_id, 'wafp_subscr_type', $subscr_type );
    add_post_meta( $post_id, 'wafp_ip_addr',     $ip_addr );

    if($affiliate_id)
      add_post_meta( $post_id, 'wafp_affiliate_id', $affiliate_id );

    return $post_id;
  }

  static public function get_one_by_subscr_id($subscr_id)
  {
    global $wpdb;

    $sql = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key=%s and meta_value=%s";
    $sql = $wpdb->prepare($sql, 'wafp_subscr_id', $subscr_id);
    $post_id = $wpdb->get_var($sql);

    if($post_id)
      return new WafpSubscription($post_id);
    else
      return false;
  }

  static public function subscription_exists($subscr_id)
  {
    return self::get_one_by_subscr_id($subscr_id);
  }

  /** Instance Variables & Methods **/
  public $subscription;
  public $subscr_id;
  public $subscr_type;
  public $affiliate_id;
  public $ip_addr;

  public function __construct($id)
  {
    $this->subscription = get_post($id);
    $this->subscr_id    = get_post_meta( $id, 'wafp_subscr_id',    true );
    $this->subscr_type  = get_post_meta( $id, 'wafp_subscr_type',  true );
    $this->affiliate_id = get_post_meta( $id, 'wafp_affiliate_id', true );
    $this->ip_addr      = get_post_meta( $id, 'wafp_ip_addr',      true );
  }

  public function update()
  {
    $this->subscr_id    = get_post_meta( $this->subscription->ID, 'wafp_subscr_id',    $this->subscr_id );
    $this->subscr_type  = get_post_meta( $this->subscription->ID, 'wafp_subscr_type',  $this->subscr_type );
    $this->affiliate_id = get_post_meta( $this->subscription->ID, 'wafp_affiliate_id', $this->affiliate_id );
    $this->ip_addr      = get_post_meta( $this->subscription->ID, 'wafp_ip_addr',      $this->ip_addr );
  }
}
?>
