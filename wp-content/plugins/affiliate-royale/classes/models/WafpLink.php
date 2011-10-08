<?php
class WafpLink
{
  /** STATIC CRUD METHODS **/
  function create( $target_url, $image='', $width=0, $height=0 )
  {
    global $wafp_db;
    $args = compact( 'target_url', 'image', 'width', 'height' );
    return $wafp_db->create_record($wafp_db->links, $args);
  }

  function update( $id, $target_url, $image='', $width=0, $height=0 )
  {
    global $wafp_db;

    $args = compact( 'target_url', 'image', 'width', 'height' );
    return $wafp_db->update_record($wafp_db->links, $id, $args);
  }

  function update_image( $id, $image, $width, $height )
  {
    global $wafp_db;

    $args = compact( 'image', 'width', 'height' );
    return $wafp_db->update_record($wafp_db->links, $id, $args);
  }
  
  function update_target_url( $id, $target_url )
  {
    global $wafp_db;

    $args = compact( 'target_url' );
    return $wafp_db->update_record($wafp_db->links, $id, $args);
  }
  
  
  function delete( $id )
  {
    global $wafp_db;
    
    $link =& WafpLink::get_stored_object($id);
    
    if(!empty($link->rec->image))
      @unlink($link->image_path());

    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->links, $args);
  }
  
  function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->links, $args);
  }
  
  function get_count($id)
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->links);
  }
  
  function add_file( $ufile )
  {
    $target_path_array = wp_upload_dir();
    $target_path = $target_path_array['basedir'];
    
    if(!file_exists($target_path))
      @mkdir($target_path."/");
  
    $target_path = $target_path . "/affiliate-royale";
    if(!file_exists($target_path))
      @mkdir($target_path."/");

    $target_path = $target_path . "/banners";
    if(!file_exists($target_path))
      @mkdir($target_path."/");
    
    // Using WordPress' built in resize capabilies using GD
    require_once(ABSPATH.'wp-admin/includes/image.php');
    require_once(ABSPATH.'wp-includes/media.php');
    $image_path = $target_path . '/' . time() . '_' . $ufile['name'];
    move_uploaded_file($ufile['tmp_name'], $image_path);

    $image_meta = getimagesize($image_path);

    return array( 'image'  => $image_path,
                  'width'  => $image_meta[0],
                  'height' => $image_meta[1] );
  }

  function get_all($order_by='', $limit='')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->links, array(), $order_by, $limit);
  }
  
  function &get_all_objects($order_by='', $limit='', $force=false)
  {
    $all_records =& WafpLink::get_all($order_by, $limit);
  
    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] =& WafpLink::get_stored_object($record->id, $force);
  
    return $my_objects;
  }

  function &get_stored_object($id, $force=false)
  { 
    static $my_objects;

    if( !isset($my_objects) )
      $my_objects = array();

    if( $force or
        !isset($my_objects[$id]) or
        empty($my_objects[$id]) or
        !is_object(&$my_objects[$id]) )
      $my_objects[$id] =& new WafpLink($id);
    
    return $my_objects[$id];
  }
  
  function validate($target_url, &$errors, $allow_blank=false)
  {
    if( !$allow_blank and ( $target_url == null or $target_url == '' ) )
      $errors[] = __("Target URL can't be blank", 'affiliate-royale') . ": " . $target_url;

    if( !empty($target_url) and
        !preg_match('/^http.?:\/\/.*\..*$/', $target_url ) and
        !preg_match('!^(http|https)://(localhost|127\.0\.0\.1)(:\d+)?(/[\w- ./?%&=]*)?!', $target_url ) )
      $errors[] = __("Target URL must be a valid URL", 'affiliate-royale') . ": " . $target_url;
  }
  
  /** INSTANCE VARIABLES & METHODS **/
  var $rec;

  function WafpLink($id)
  {
    $this->rec         = WafpLink::get_one($id);
    $target_path_array = wp_upload_dir();
    $this->upload_url  = "{$target_path_array['baseurl']}/affiliate-royale/banners";
    $this->upload_path = "{$target_path_array['basedir']}/affiliate-royale/banners";
  }
  
  function display_url($affiliate_id)
  {
    return WAFP_SCRIPT_URL . "&controller=links&action=redirect&l={$this->rec->id}&a={$affiliate_id}";
  }
  
  function link_code($affiliate_id)
  {
    if( isset($this->rec->image) and !empty($this->rec->image))
      return "<a href=\"". $this->display_url($affiliate_id) . "\"><img src=\"" . $this->image_url() . "\" width=\"{$this->rec->width}px\" height=\"{$this->rec->height}\" /></a>";
    else
      return $this->display_url($affiliate_id);
  }
  
  function image_url()
  {
    return apply_filters('wafp_image_url',"{$this->upload_url}/" . basename($this->rec->image));
  }
  
  function image_path()
  {
    return "{$this->upload_path}/" . basename($this->rec->image);
  }

  public static function track($affiliate_id, $link_id=0)
  {
    global $wpdb, $wafp_options;
    
    $user = new WafpUser( $affiliate_id );
    if( $user->is_affiliate() )
    {
      $first_click = 0;
      
      $click_ip = $_SERVER['REMOTE_ADDR'];
      $click_referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
      
      $click_uri = $_SERVER['REQUEST_URI'];
      $click_user_agent = $_SERVER['HTTP_USER_AGENT'];
     
      $cookie_name = "wafp_click";
      $cookie_expire_time = time()+60*60*24* $wafp_options->expire_after_days; // Expire in 60 days
      
      $old_cookie = isset($_COOKIE[$cookie_name])?$_COOKIE[$cookie_name]:false;
      if( $old_cookie )
        $first_click = (((int)$old_cookie != (int)$affiliate_id)?1:0);
      else
        $first_click = 1;
     
      // Set cookie -- overwrite the cookie if it's already there -- we'll employ a "last touch" methodology
      setcookie($cookie_name,$affiliate_id,$cookie_expire_time,'/');
      
      return WafpClick::create( $click_ip, $click_user_agent, $click_referrer, $click_uri, $link_id, $affiliate_id, $first_click );
    }

    return false;
  }
  
  function track_and_redirect($affiliate_id)
  {
    self::track($affiliate_id, $this->rec->id);

    // Merchant should retain as much link juice as possible so 301 redirect is the way to go
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: {$this->rec->target_url}");
    exit;
  }
}
?>
