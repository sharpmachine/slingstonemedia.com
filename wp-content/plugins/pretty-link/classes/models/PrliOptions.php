<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

class PrliOptions
{
  public $prli_exclude_ips;
  public $whitelist_ips;
  public $filter_robots;
  public $extended_tracking;
  public $prettybar_image_url;
  public $prettybar_background_image_url;
  public $prettybar_color;
  public $prettybar_text_color;
  public $prettybar_link_color;
  public $prettybar_hover_color;
  public $prettybar_visited_color;
  public $prettybar_show_title;
  public $prettybar_show_description;
  public $prettybar_show_share_links;
  public $prettybar_show_target_url_link;
  public $prettybar_title_limit;
  public $prettybar_desc_limit;
  public $prettybar_link_limit;

  public $link_redirect_type;
  public $link_redirect_action;
  public $link_prefix;
  public $link_track_me;
  public $link_nofollow;

  public $bookmarklet_auth;

  function __construct($options_array=array())
  {
    // Set values from array
    foreach($options_array as $key => $value)
      $this->{$key} = $value;
    
    $this->set_default_options();
  }

  function set_default_options()
  {
    // Must account for the Legacy Options
    $prli_exclude_ips  = 'prli_exclude_ips';
    $prettybar_image_url  = 'prli_prettybar_image_url';
    $prettybar_background_image_url  = 'prli_prettybar_background_image_url';
    $prettybar_color  = 'prli_prettybar_color';
    $prettybar_text_color  = 'prli_prettybar_text_color';
    $prettybar_link_color  = 'prli_prettybar_link_color';
    $prettybar_hover_color  = 'prli_prettybar_hover_color';
    $prettybar_visited_color  = 'prli_prettybar_visited_color';
    $prettybar_show_title  = 'prli_prettybar_show_title';
    $prettybar_show_description  = 'prli_prettybar_show_description';
    $prettybar_show_share_links  = 'prli_prettybar_show_share_links';
    $prettybar_show_target_url_link  = 'prli_prettybar_show_target_url_link';
    $prettybar_title_limit = 'prli_prettybar_title_limit';
    $prettybar_desc_limit = 'prli_prettybar_desc_limit';
    $prettybar_link_limit = 'prli_prettybar_link_limit';
    $link_show_prettybar = 'prli_link_show_prettybar';
    $link_ultra_cloak = 'prli_link_ultra_cloak';
    $link_track_me = 'prli_link_track_me';
    $link_prefix = 'prli_link_prefix';
    $link_track_as_pixel = 'prli_link_track_as_pixel';
    $link_nofollow = 'prli_link_nofollow';
    $link_redirect_type = 'prli_link_redirect_type';
    $link_redirect_action = 'prli_link_redirect_action';


    if(!isset($this->prettybar_show_title)) {
      if($var = get_option( $prettybar_show_title )) {
        $this->prettybar_show_title = $var;
        delete_option( $prettybar_show_title );
      }
      else
        $this->prettybar_show_title = '1';
    }

    if(!isset($this->prettybar_show_description)) {
      if($var = get_option( $prettybar_show_description )) {
        $this->prettybar_show_description = $var;
        delete_option( $prettybar_show_description );
      }
      else
        $this->prettybar_show_description = '1';
    }

    if(!isset($this->prettybar_show_share_links)) {
      if($var = get_option( $prettybar_show_share_links )) {
        $this->prettybar_show_share_links = $var;
        delete_option( $prettybar_show_share_links );
      }
      else
        $this->prettybar_show_share_links = '1';
    }

    if(!isset($this->prettybar_show_target_url_link)) {
      if($var = get_option( $prettybar_show_target_url_link )) {
        $this->prettybar_show_target_url_link = $var;
        delete_option( $prettybar_show_target_url_link );
      }
      else
        $this->prettybar_show_target_url_link = '1';
    }

    if(!isset($this->link_track_me)) {
      if($var = get_option( $link_track_me )) {
        $this->link_track_me = $var;
        delete_option( $link_track_me );
      }
      else
        $this->link_track_me = '1';
    }

    if(!isset($this->link_prefix))
        $this->link_prefix = 0;

    if(!isset($this->link_nofollow)) {
      if($var = get_option( $link_nofollow )) {
        $this->link_nofollow = $var;
        delete_option( $link_nofollow );
      }
      else
        $this->link_nofollow = '0';
    }

    if(!isset($this->link_redirect_type)) {
      if($var = get_option( $link_track_as_pixel )) {
        $this->link_redirect_type = 'pixel';
        delete_option( $link_show_prettybar );
        delete_option( $link_ultra_cloak );
        delete_option( $link_track_as_pixel );
        delete_option( $link_redirect_type );
      }
      if($var = get_option( $link_show_prettybar )) {
        $this->link_redirect_type = 'prettybar';
        delete_option( $link_show_prettybar );
        delete_option( $link_ultra_cloak );
        delete_option( $link_track_as_pixel );
        delete_option( $link_redirect_type );
      }
      if($var = get_option( $link_ultra_cloak )) {
        $this->link_redirect_type = 'cloak';
        delete_option( $link_show_prettybar );
        delete_option( $link_ultra_cloak );
        delete_option( $link_track_as_pixel );
        delete_option( $link_redirect_type );
      }
      if($var = get_option( $link_redirect_type )) {
        $this->link_redirect_type = $var;
        delete_option( $link_show_prettybar );
        delete_option( $link_ultra_cloak );
        delete_option( $link_track_as_pixel );
        delete_option( $link_redirect_type );
      }
      else
        $this->link_redirect_type = '307';
    }

    if(!isset($this->link_redirect_action))
        $this->link_redirect_action = 'init';

    if(!isset($this->prli_exclude_ips))
    {
      if($var = get_option( $prli_exclude_ips )) {
        $this->prli_exclude_ips = $var;
        delete_option( $prli_exclude_ips );
      }
      else
        $this->prli_exclude_ips = '';
    }

    if(!isset($this->prettybar_image_url))
    {
      if($var = get_option( $prettybar_image_url )) {
        $this->prettybar_image_url = $var;
        delete_option( $prettybar_image_url );
      }
      else
        $this->prettybar_image_url = PRLI_IMAGES_URL . '/pretty-link-48x48.png';
    }

    if(!isset($this->prettybar_background_image_url))
    {
      if($var = get_option( $prettybar_background_image_url )) {
        $this->prettybar_background_image_url = $var;
        delete_option( $prettybar_background_image_url );
      }
      else
        $this->prettybar_background_image_url = PRLI_IMAGES_URL . '/bar_background.png';
    }

    if(!isset($this->prettybar_color))
    {
      if($var = get_option( $prettybar_color )) {
        $this->prettybar_color = $var;
        delete_option( $prettybar_color );
      }
      else
        $this->prettybar_color = '';
    }

    if(!isset($this->prettybar_text_color))
    {
      if($var = get_option( $prettybar_text_color )) {
        $this->prettybar_text_color = $var;
        delete_option( $prettybar_text_color );
      }
      else
        $this->prettybar_text_color = '000000';
    }

    if(!isset($this->prettybar_link_color))
    {
      if($var = get_option( $prettybar_link_color )) {
        $this->prettybar_link_color = $var;
        delete_option( $prettybar_link_color );
      }
      else
        $this->prettybar_link_color = '0000ee';
    }

    if(!isset($this->prettybar_hover_color))
    {
      if($var = get_option( $prettybar_hover_color )) {
        $this->prettybar_hover_color = $var;
        delete_option( $prettybar_hover_color );
      }
      else
        $this->prettybar_hover_color = 'ababab';
    }

    if(!isset($this->prettybar_visited_color))
    {
      if($var = get_option( $prettybar_visited_color )) {
        $this->prettybar_visited_color = $var;
        delete_option( $prettybar_visited_color );
      }
      else
        $this->prettybar_visited_color = '551a8b';
    }

    if(!isset($this->prettybar_title_limit))
    {
      if($var = get_option( $prettybar_title_limit )) {
        $this->prettybar_title_limit = $var;
        delete_option( $prettybar_title_limit );
      }
      else
        $this->prettybar_title_limit = '25';
    }

    if(!isset($this->prettybar_desc_limit))
    {
      if($var = get_option( $prettybar_desc_limit )) {
        $this->prettybar_desc_limit = $var;
        delete_option( $prettybar_desc_limit );
      }
      else
        $this->prettybar_desc_limit = '30';
    }

    if(!isset($this->prettybar_link_limit))
    {
      if($var = get_option( $prettybar_link_limit )) {
        $this->prettybar_link_limit = $var;
        delete_option( $prettybar_link_limit );
      }
      else
        $this->prettybar_link_limit = '30';
    }

    if(!isset($this->bookmarklet_auth))
        $this->bookmarklet_auth = md5(get_option('auth_salt') . time());

    if(!isset($this->whitelist_ips))
      $this->whitelist_ips = '';

    if(!isset($this->filter_robots))
      $this->filter_robots = 0;

    if(!isset($this->extended_tracking))
      $this->extended_tracking = 'normal';
  }

  public function store() {
    $storage_array = (array)$this;
    update_option( 'prli_options', $storage_array );
  }

  public static function get_options() {
    $prli_options = get_option('prli_options');
    
    if($prli_options) {
      if(is_string($prli_options))
        $prli_options = unserialize($prli_options);
      
      if(is_object($prli_options) and is_a($prli_options,'PrliOptions')) {
        $prli_options->set_default_options();
        $prli_options->store(); // store will convert this back into an array
      }
      else if(is_array($prli_options))
        $prli_options = new PrliOptions($prli_options);
      else
        $prli_options = new PrliOptions();
    }
    else
      $prli_options = new PrliOptions();

    return $prli_options;
  }
}
