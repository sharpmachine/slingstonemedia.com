<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

class PrliAppController
{
  function __construct()
  {
    add_action('init', array(&$this,'parse_standalone_request'));
    add_action('admin_notices', array(&$this, 'upgrade_database_headline'));
  }
  
  public function upgrade_database_headline()
  {
    global $prli_update, $prli_db_version, $prlipro_db_version;

    // Only show the message if the user can update core
    if(current_user_can( 'update_core' )) {
      $old_prli_db_version = get_option('prli_db_version');
      $show_db_upgrade_message = ( !$old_prli_db_version or ( intval($old_prli_db_version) < $prli_db_version ) );
      
      if( !$show_db_upgrade_message and
          $prli_update->pro_is_installed_and_authorized())
      {
        $old_prlipro_db_version = get_option('prlipro_db_version');
        $show_db_upgrade_message = ( !$old_prlipro_db_version or ( intval($old_prlipro_db_version) < $prlipro_db_version ) );
      }
      
      if( $show_db_upgrade_message )
      {
         $db_upgrade_url = wp_nonce_url(site_url("index.php?plugin=pretty-link&controller=admin&action=db_upgrade"), "prli-db-upgrade");
         ?>
         <div class="error" style="padding-top: 5px; padding-bottom: 5px;"><?php printf(__('Database Upgrade is required for Pretty Link to work properly<br/>%1$sAutomatically Upgrade your Database%2$s', 'pretty-link'), "<a href=\"{$db_upgrade_url}\">",'</a>'); ?></div>
         <?php
      }
    }
  }
  
  public function parse_standalone_request()
  {
    if( !empty($_REQUEST['plugin']) and $_REQUEST['plugin'] == 'pretty-link' and 
        !empty($_REQUEST['controller']) and !empty($_REQUEST['action']) ) {
      $this->standalone_route($_REQUEST['controller'], $_REQUEST['action']);
      do_action('prli-standalone-route');
      exit;
    }
    else if( !empty($_GET['action']) and $_GET['action']=='prli_bookmarklet' ) {
      PrliBookmarkletController::route();
      exit;
    }
  }
  
  public function standalone_route($controller, $action)
  {
    if($controller=='admin')
    {
      if($action=='db_upgrade')
        $this->db_upgrade();
    }
  }
  
  public function db_upgrade()
  {
    if(!function_exists('wp_redirect'))
      require_once(ABSPATH . WPINC . '/pluggable.php');

    if( wp_verify_nonce( $_REQUEST['_wpnonce'], "prli-db-upgrade" ) and current_user_can( 'update_core' ) ) {
      prli_install();
      wp_redirect(admin_url("admin.php?page=pretty-link&message=" . urlencode(__('Your Database Has Been Successfully Upgraded.', 'pretty-link'))));
    }
    else
      wp_redirect(home_url());
  }
}
