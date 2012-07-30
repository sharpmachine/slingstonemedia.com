<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

define('PRLI_PLUGIN_NAME',dirname(plugin_basename(__FILE__)));
define('PRLI_PATH',WP_PLUGIN_DIR.'/'.PRLI_PLUGIN_NAME);
define('PRLI_MODELS_PATH',PRLI_PATH.'/classes/models');
define('PRLI_HELPERS_PATH',PRLI_PATH.'/classes/helpers');
define('PRLI_CONTROLLERS_PATH',PRLI_PATH.'/classes/controllers');
define('PRLI_VIEWS_PATH',PRLI_PATH.'/classes/views');
//define(PRLI_URL,WP_PLUGIN_URL.'/'.PRLI_PLUGIN_NAME);
define('PRLI_URL',plugins_url($path = '/'.PRLI_PLUGIN_NAME));
define('PRLI_CSS_URL',PRLI_URL . '/css');
define('PRLI_JS_URL',PRLI_URL . '/js');
define('PRLI_IMAGES_URL',PRLI_URL . '/images');
define('PRLI_BROWSER_URL','https://d14715w921jdje.cloudfront.net/browser');
define('PRLI_OS_URL','https://d14715w921jdje.cloudfront.net/os');

require_once(PRLI_MODELS_PATH.'/PrliOptions.php');

// For IIS compatibility
if (!function_exists('fnmatch'))
{
  function fnmatch($pattern, $string)
  {
    return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.'))."$#i", $string);
  }
}
  
// The number of items per page on a table
global $page_size;
$page_size = 10;

global $prli_blogurl;
global $prli_siteurl;
global $prli_blogname;
global $prli_blogdescription;

$prli_blogurl = ((get_option('home'))?get_option('home'):get_option('siteurl'));
$prli_siteurl = get_option('siteurl');
$prli_blogname = get_option('blogname');
$prli_blogdescription = get_option('blogdescription');

/***** SETUP OPTIONS OBJECT *****/
global $prli_options;
$prli_options = PrliOptions::get_options();

/***** TODO: Uh... these functions should find a better home somewhere *****/
function setup_new_vars($groups)
{
  global $prli_link, $prli_options;

  $values = array();
  $values['url'] =  (isset($_REQUEST['url'])?$_REQUEST['url']:'');
  $values['slug'] = (isset($_REQUEST['slug'])?$_REQUEST['slug']:$prli_link->generateValidSlug());
  $values['name'] = htmlspecialchars((isset($_REQUEST['name'])?stripslashes($_REQUEST['name']):''));
  $values['description'] = htmlspecialchars((isset($_REQUEST['description'])?stripslashes($_REQUEST['description']):''));

  $values['track_me'] = (((isset($_REQUEST['track_me']) and $_REQUEST['track_me'] == 'on') or (!isset($_REQUEST['track_me']) and $prli_options->link_track_me == '1'))?'checked="true"':'');
  $values['nofollow'] = (((isset($_REQUEST['nofollow']) and $_REQUEST['nofollow'] == 'on') or (!isset($_REQUEST['nofollow']) and $prli_options->link_nofollow == '1'))?'checked="true"':'');

  $values['redirect_type'] = array();
  $values['redirect_type']['307'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '307') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == '307'))?'selected="selected"':'');
  $values['redirect_type']['301'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '301') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == '301'))?'selected="selected"':'');
  $values['redirect_type']['prettybar'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'prettybar') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'prettybar'))?'selected="selected"':'');
  $values['redirect_type']['cloak'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'cloak') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'cloak'))?'selected="selected"':'');
  $values['redirect_type']['pixel'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'pixel') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'pixel'))?'selected="selected"':'');
  $values['redirect_type']['metarefresh'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'metarefresh') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'metarefresh'))?'selected="selected"':'');
  $values['redirect_type']['javascript'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'javascript') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'javascript'))?'selected="selected"':'');
  
  $values['groups'] = array();
  
  if(is_array($groups))
  {
    foreach($groups as $group)
    {
      $values['groups'][] = array( 'id' => $group->id,
                                   'value' => ((isset($_REQUEST['group_id']) and $_REQUEST['group_id'] == $group->id)?' selected="true"':''),
                                   'name' => $group->name );
    }
  }
  
  $values['param_forwarding'] = isset($_REQUEST['param_forwarding'])?'checked=checked':'';
  
  if(isset($_REQUEST['delay']))
    $values['delay'] = $_REQUEST['delay'];
  else
    $values['delay'] = 0;

  if(isset($_REQUEST['google_tracking']))
    $values['google_tracking'] = ' checked=checked';
  else {
	global $prli_update;
	if( $prli_update->pro_is_installed_and_authorized() ) {
      global $prlipro_options;

      $values['google_tracking'] = $prlipro_options->google_tracking?' checked=checked':'';
    }
    else
      $values['google_tracking'] = '';
  }

  return $values;
}

function setup_edit_vars($groups,$record)
{
  global $prli_link, $prli_link_meta;

  $values = array();
  $values['url'] =  ((isset($_REQUEST['url']) and $record == null)?$_REQUEST['url']:$record->url);
  $values['slug'] = ((isset($_REQUEST['slug']) and $record == null)?$_REQUEST['slug']:$record->slug);
  $values['name'] = htmlspecialchars(stripslashes(((isset($_REQUEST['name']) and $record == null)?$_REQUEST['name']:$record->name)));
  $values['description'] = htmlspecialchars(stripslashes(((isset($_REQUEST['description']) and $record == null)?$_REQUEST['description']:$record->description)));
  $values['track_me'] = (((isset($_REQUEST['track_me']) or $record->track_me) and ((isset($_REQUEST['track_me']) and $_REQUEST['track_me'] == 'on') or $record->track_me == 1))?'checked="true"':'');
  $values['nofollow'] = (((isset($_REQUEST['nofollow']) or $record->nofollow) and ($_REQUEST['nofollow'] == 'on' or $record->nofollow == 1))?'checked="true"':'');

  $values['groups'] = array();
  foreach($groups as $group)
  {
    $values['groups'][] = array( 'id' => $group->id,
                                 'value' => (((isset($_REQUEST['group_id']) and ($_REQUEST['group_id'] == $group->id)) or ($record->group_id == $group->id))?' selected="true"':''),
                                 'name' => $group->name );
  }

  $values['param_forwarding'] = ((isset($_REQUEST['param_forwarding']) or $record->param_forwarding == 'on' or $record->param_forwarding == 1)?'checked=checked':'');

  $values['redirect_type'] = array();
  $values['redirect_type']['307'] = ((!isset($_REQUEST['redirect_type']) or (isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '307') or (isset($record->redirect_type) and $record->redirect_type == '307'))?' selected="selected"':'');
  $values['redirect_type']['301'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '301') or (isset($record->redirect_type) and $record->redirect_type == '301'))?' selected="selected"':'');
  $values['redirect_type']['prettybar'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'prettybar') or (isset($record->redirect_type) and $record->redirect_type == 'prettybar'))?' selected="selected"':'');
  $values['redirect_type']['cloak'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'cloak') or (isset($record->redirect_type) and $record->redirect_type == 'cloak'))?' selected="selected"':'');
  $values['redirect_type']['pixel'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'pixel') or (isset($record->redirect_type) and $record->redirect_type == 'pixel'))?' selected="selected"':'');
  $values['redirect_type']['metarefresh'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'metarefresh') or (isset($record->redirect_type) and $record->redirect_type == 'metarefresh'))?' selected="selected"':'');
  $values['redirect_type']['javascript'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'javascript') or (isset($record->redirect_type) and $record->redirect_type == 'javascript'))?' selected="selected"':'');


  if(isset($_REQUEST['delay']))
    $values['delay'] = $_REQUEST['delay'];
  else
    $values['delay'] = $prli_link_meta->get_link_meta($record->id, 'delay', true);

  if(isset($_REQUEST['google_tracking']))
    $values['google_tracking'] = ' checked=checked';
  else
    $values['google_tracking'] = (($prli_link_meta->get_link_meta($record->id, 'google_tracking', true) == 1)?' checked=checked':'');

  return $values;
}
