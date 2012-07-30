<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

class PrliLinksController
{
  public static function route()
  {
    $action = (isset($_REQUEST['action'])?$_REQUEST['action']:null);

	$params = self::get_params_array();

	// "new()" has its own submenu so we don't need a route for it here
	
    if($action == 'list-form')
      return self::list_form($params);
    else if($action == 'quick-create')
      return self::quick_create_link($params);
    else if($action == 'create')
      return self::create_link($params);
    else if($action == 'edit')
      return self::edit_link($params);
    else if($action == 'bulk-update')
      return self::bulk_update_links($params);
    else if($action == 'update')
      return self::update_link($params);
    else if($action == 'reset')
      return self::reset_link($params);
    else if($action == 'destroy')
      return self::destroy_link($params);
    else if($action == 'bulk-destroy')
      return self::bulk_destroy_links($params);
    else
      return self::list_links($params);
  }

  public static function load_styles() {
	wp_enqueue_style( 'prli-admin-links', PRLI_CSS_URL . '/prli-admin-links.css', array() );
  }

  public static function load_scripts() {
	wp_enqueue_script( 'jquery-clippy', PRLI_JS_URL . '/jquery.clippy.js', array('jquery') );
	wp_enqueue_script( 'prli-admin-links', PRLI_JS_URL . '/prli-admin-links.js', array('jquery','jquery-clippy') );
  }

  public static function load_dynamic_scripts() {
    ?>
    <script type="text/javascript">
      jQuery(document).ready(function() {
        /* Set up the clippies! */
        jQuery('.list-clippy').clippy({clippy_path: '<?php echo PRLI_JS_URL; ?>/clippy.swf', width: '100px', color: '#F9F9F9'});
      });
    </script>
    <?php
  }

  public static function list_links($params) {
	global $wpdb, $prli_group;
	
	if(!empty($params['message']))
	  $prli_message = $params['message'];
	else if(empty($params['group']))
	  $prli_message = prli_get_main_message();
	else
	  $prli_message = __("Links in Group: ", 'pretty-link') . $wpdb->get_var("SELECT name FROM " . $prli_group->table_name . " WHERE id=".$params['group']);
    
	self::display_links_list($params, $prli_message);
  }

  public function list_form($params) {
	if(apply_filters('prli-link-list-process-form', true))
	  self::display_links_list($params, prli_get_main_message());
  }

  public static function new_link($params) {
	global $prli_group;
	$groups = $prli_group->getAll('',' ORDER BY name');
	$values = setup_new_vars($groups);

	require_once PRLI_VIEWS_PATH . '/prli-links/new.php';
  }

  public static function quick_create_link($params) {
	global $prli_link, $prli_group, $prli_options;
	
	$params = self::get_params_array();
    $errors = $prli_link->validate($_POST);
  
    if( count($errors) > 0 )
    {
      $groups = $prli_group->getAll('',' ORDER BY name');
      $values = setup_new_vars($groups);
      require_once PRLI_VIEWS_PATH . '/prli-links/new.php';
    }
    else
    {
      $_POST['param_forwarding'] = 'off';
      $_POST['param_struct'] = '';
      $_POST['name'] = '';
      $_POST['description'] = '';
      if( $prli_options->link_track_me )
        $_POST['track_me'] = 'on';
      if( $prli_options->link_nofollow )
        $_POST['nofollow'] = 'on';
  
      $_POST['redirect_type'] = $prli_options->link_redirect_type;
  
      $record = $prli_link->create( $_POST );
  
      $prli_message = __("Your Pretty Link was Successfully Created", 'pretty-link');
      self::display_links_list($params, $prli_message, '', 1);
    }
  }

  public static function create_link($params) {
	global $prli_link, $prli_group;
    $errors = $prli_link->validate($_POST);
    
    $errors = apply_filters( "prli_validate_link", $errors );
    
    if( count($errors) > 0 )
    {
      $groups = $prli_group->getAll('',' ORDER BY name');
      $values = setup_new_vars($groups);
      require_once PRLI_VIEWS_PATH . '/prli-links/new.php';
    }
    else
    {
      $record = $prli_link->create( $_POST );
    
      do_action( "prli_update_link", $record );
    
      $prli_message = __("Your Pretty Link was Successfully Created", 'pretty-link');
      self::display_links_list($params, $prli_message, '', 1);
    }
  }

  public static function edit_link($params) {
	global $prli_group, $prli_link;
	$groups = $prli_group->getAll('',' ORDER BY name');
    
	$record = $prli_link->getOne( $params['id'] );
	$values = setup_edit_vars($groups,$record);
	$id = $params['id'];
	require_once PRLI_VIEWS_PATH . '/prli-links/edit.php';
  }

  public static function update_link($params) {
	global $prli_link, $prli_group;
    $errors = $prli_link->validate($_POST);
    $id = $_POST['id'];
    
    $errors = apply_filters( "prli_validate_link", $errors );
    
    if( count($errors) > 0 )
    {
      $groups = $prli_group->getAll('',' ORDER BY name');
      $record = $prli_link->getOne( $params['id'] );
      $values = setup_edit_vars($groups,$record);
      require_once PRLI_VIEWS_PATH . '/prli-links/edit.php';
    }
    else
    {
      $record = $prli_link->update( $_POST['id'], $_POST );
    
      do_action( "prli_update_link", $id );
    
      $prli_message = __("Your Pretty Link was Successfully Updated", 'pretty-link');
      self::display_links_list($params, $prli_message, '', 1);
    }
  }
  
  public static function bulk_update_links() {
    global $prli_link;
    if(wp_verify_nonce($_REQUEST['_wpnonce'],'prli_bulk_update') and isset($_REQUEST['ids'])) {

      $ids = $_REQUEST['ids'];
      $params = $_REQUEST['bu'];
      
      $prli_link->bulk_update( $ids, $params );
      do_action('prli-bulk-action-update',$ids,$params);
      
      $message = __('Your links were updated successfully', 'pretty-link');
     
      //self::display_links_list(self::get_params_array(),$message);

      // We're going to redirect here to avoid having a big nasty url that
      // can cause problems when doing several activities in a row.

      // Scrub message, action, _wpnonce, ids & bu vars from the arguments and redirect
      $request_uri = preg_replace( '#\&(message|action|_wpnonce|ids|bu\[[^\]]*?\])=[^\&]*#', '', $_SERVER['REQUEST_URI'] );

      // we assume here that some arguments are set ... if not this value is meaningless anyway
      $request_uri .= '&message=' . urlencode($message);
      $redirect_url = 'http' . (empty($_SERVER['HTTPS'])?'':'s') . '://' . $_SERVER['HTTP_HOST'] . $request_uri;
      
      require PRLI_VIEWS_PATH . '/shared/jsredirect.php';
    }
    else
      wp_die(__('You are unauthorized to view this page.', 'pretty-link'));
  }

  public static function reset_link($params) {
    global $prli_link;
    $prli_link->reset( $params['id'] );
    $prli_message = __("Your Pretty Link was Successfully Reset", 'pretty-link');
    self::display_links_list($params, $prli_message, '', 1);
  }

  public static function destroy_link($params) {
    global $prli_link;
    $prli_link->destroy( $params['id'] );
    $prli_message = __("Your Pretty Link was Successfully Destroyed", 'pretty-link');
    self::display_links_list($params, $prli_message, '', 1);
  }

  public static function bulk_destroy_links($params) {
    global $prli_link;
    if(wp_verify_nonce($_REQUEST['_wpnonce'],'prli_bulk_update') and isset($_REQUEST['ids'])) {
      $ids = explode(',', $_REQUEST['ids']);

      foreach($ids as $id) {
        $prli_link->destroy( $id );
      }
      
      $message = __('Your links were deleted successfully', 'pretty-link');
     
      //self::display_links_list($params,$message);
      // Scrub message, action, _wpnonce, ids & bu vars from the arguments and redirect
      $request_uri = preg_replace( '#\&(message|action|_wpnonce|ids|bu\[[^\]]*?\])=[^\&]*#', '', $_SERVER['REQUEST_URI'] );

      // we assume here that some arguments are set ... if not this value is meaningless anyway
      $request_uri .= '&message=' . urlencode($message);
      $redirect_url = 'http' . (empty($_SERVER['HTTPS'])?'':'s') . '://' . $_SERVER['HTTP_HOST'] . $request_uri;
      
      require PRLI_VIEWS_PATH . '/shared/jsredirect.php';
    }
    else
      wp_die(__('You are unauthorized to view this page.', 'pretty-link'));
  }

  public static function display_links_list($params, $prli_message, $page_params_ov = false, $current_page_ov = false)
  {
    global $wpdb, $prli_utils, $prli_click, $prli_group, $prli_link, $page_size, $prli_options;
  
    $controller_file = basename(__FILE__);
  
    $where_clause = '';
    $page_params  = '';

    $page_size = (isset($_REQUEST['size']) && is_numeric($_REQUEST['size']) && !empty($_REQUEST['size']))?$_REQUEST['size']:10;
  
    if(!empty($params['group']))
    {
      $where_clause = " group_id=" . $params['group'];
      $page_params = "&group=" . $params['group'];
    }
  
    $link_vars = self::get_link_sort_vars($params, $where_clause);
  
    if($current_page_ov)
      $current_page = $current_page_ov;
    else
      $current_page = $params['paged'];
  
    if($page_params_ov)
      $page_params .= $page_params_ov;
    else
      $page_params .= $link_vars['page_params'];
  
    $sort_str = $link_vars['sort_str'];
    $sdir_str = $link_vars['sdir_str'];
    $search_str = $link_vars['search_str'];
  
    $record_count = $prli_link->getRecordCount($link_vars['where_clause']);
    $page_count = $prli_link->getPageCount($page_size,$link_vars['where_clause']);
    $links = $prli_link->getPage($current_page,$page_size,$link_vars['where_clause'],$link_vars['order_by']);
    $page_last_record = $prli_utils->getLastRecordNum($record_count,$current_page,$page_size);
    $page_first_record = $prli_utils->getFirstRecordNum($record_count,$current_page,$page_size);
  
    require_once PRLI_VIEWS_PATH . '/prli-links/list.php';
  }
  
  public static function get_link_sort_vars($params,$where_clause = '')
  {
    $order_by = '';
    $page_params = '';
  
    // These will have to work with both get and post
    $sort_str = $params['sort'];
    $sdir_str = $params['sdir'];
    $search_str = $params['search'];
  
    // Insert search string
    if(!empty($search_str))
    {
      $search_params = explode(" ", $search_str);
  
      foreach($search_params as $search_param)
      {
        if(!empty($where_clause))
          $where_clause .= " AND";
  
        $where_clause .= " (li.name like '%$search_param%' OR li.slug like '%$search_param%' OR li.url like '%$search_param%' OR li.created_at like '%$search_param%')";
      }
  
      $page_params .="&search=$search_str";
    }
  
    // make sure page params stay correct
    if(!empty($sort_str))
      $page_params .="&sort=$sort_str";
  
    if(!empty($sdir_str))
      $page_params .= "&sdir=$sdir_str";
  
    // Add order by clause
    switch($sort_str)
    {
      case "name":
      case "clicks":
      case "group_name":
      case "slug":
        $order_by .= " ORDER BY $sort_str";
        break;
      default:
        $order_by .= " ORDER BY created_at";
    }
  
    // Toggle ascending / descending
    if((empty($sort_str) and empty($sdir_str)) or $sdir_str == 'desc')
    {
      $order_by .= ' DESC';
      $sdir_str = 'desc';
    }
    else
      $sdir_str = 'asc';
  
    return array('order_by' => $order_by,
                 'sort_str' => $sort_str, 
                 'sdir_str' => $sdir_str, 
                 'search_str' => $search_str, 
                 'where_clause' => $where_clause, 
                 'page_params' => $page_params);
  }

  // Set defaults and grab get or post of each possible param
  public static function get_params_array()
  {
    return array(
       'action'     => (isset($_REQUEST['action'])?$_REQUEST['action']:'list'),
       'regenerate' => (isset($_REQUEST['regenerate'])?$_REQUEST['regenerate']:'false'),
       'id'         => (isset($_REQUEST['id'])?$_REQUEST['id']:''),
       'group_name' => (isset($_REQUEST['group_name'])?$_REQUEST['group_name']:''),
       'paged'      => (isset($_REQUEST['paged'])?$_REQUEST['paged']:1),
       'group'      => (isset($_REQUEST['group'])?$_REQUEST['group']:''),
       'search'     => (isset($_REQUEST['search'])?$_REQUEST['search']:''),
       'sort'       => (isset($_REQUEST['sort'])?$_REQUEST['sort']:''),
       'sdir'       => (isset($_REQUEST['sdir'])?$_REQUEST['sdir']:''),
       'message'    => (isset($_REQUEST['message'])?$_REQUEST['message']:'')
    );
  }
}
