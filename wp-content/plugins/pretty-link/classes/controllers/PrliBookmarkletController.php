<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

class PrliBookmarkletController {
  public static function route() {
    global $prli_options;
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    if(isset($_GET['k']) and $_GET['k']==$prli_options->bookmarklet_auth) {
      if($action == 'prli_bookmarklet' and
         isset($_GET['target_url']) and
         PrliUtils::is_url($_GET['target_url'])) { return self::bookmark($_GET['target_url']); }
    }
    else {
      wp_redirect(home_url());
      exit;
    }
  }

  public static function bookmark($target_url) {
    global $prli_options, $prli_blogurl, $prli_link;

    $redirect_type = esc_html((isset($_GET['rt']) and $_GET['rt'] != '-1')?$_GET['rt']:'');
    $track = esc_html((isset($_GET['trk']) and $_GET['trk'] != '-1')?$_GET['trk']:'');
    $group = esc_html((isset($_GET['grp']) and $_GET['grp'] != '-1')?$_GET['grp']:'');

    $result = prli_create_pretty_link( esc_url_raw($target_url, array('http','https')), '', '', '', $group, $track, '', $redirect_type );

    $plink = $prli_link->getOne($result);
    $target_url = $plink->url;
    $target_url_title = $plink->name;
    $pretty_link = $prli_blogurl . PrliUtils::get_permalink_pre_slug_uri() . $plink->slug;

    $twitter_status = substr($target_url_title,0,(114 - strlen($pretty_link))) . ((strlen($target_url_title) > 114)?"...":'') . " | $pretty_link";
    
    require PRLI_VIEWS_PATH . '/prli-tools/bookmarklet.php';
  }
}
