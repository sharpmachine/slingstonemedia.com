<?php
/*
Plugin Name: Affiliate Royale
Plugin URI: http://affiliateroyale.com
Description: A complete Affiliate Program plugin for WordPress. Use it to start an Affiliate Program for your products to dramatically increase traffic, attention and sales.
Version: 1.0.06
Author: Caseproof, LLC
Text Domain: affiliate-royale
Copyright: 2004-2011, Caseproof, LLC

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('WAFP_PLUGIN_NAME',"affiliate-royale");
$wafp_script_url = get_option('home') . '/index.php?plugin=wafp';
define('WAFP_PATH',WP_PLUGIN_DIR.'/'.WAFP_PLUGIN_NAME);
define('WAFP_IMAGES_PATH',WAFP_PATH.'/images');
define('WAFP_CSS_PATH',WAFP_PATH.'/css');
define('WAFP_JS_PATH',WAFP_PATH.'/js');
define('WAFP_I18N_PATH',WAFP_PATH.'/i18n');
define('WAFP_APIS_PATH',WAFP_PATH.'/classes/apis');
define('WAFP_MODELS_PATH',WAFP_PATH.'/classes/models');
define('WAFP_CONTROLLERS_PATH',WAFP_PATH.'/classes/controllers');
define('WAFP_VIEWS_PATH',WAFP_PATH.'/classes/views');
define('WAFP_WIDGETS_PATH',WAFP_PATH.'/classes/widgets');
define('WAFP_HELPERS_PATH',WAFP_PATH.'/classes/helpers');
define('WAFP_TESTS_PATH',WAFP_PATH.'/tests');
define('WAFP_URL',plugins_url($path = '/'.WAFP_PLUGIN_NAME));
define('WAFP_IMAGES_URL',WAFP_URL.'/images');
define('WAFP_CSS_URL',WAFP_URL.'/css');
define('WAFP_JS_URL',WAFP_URL.'/js');
define('WAFP_SCRIPT_URL',$wafp_script_url);

// Autoload all the requisite classes
function __autoload($class_name) {
  // Only load Affiliate Royale classes here
  if(preg_match('/^Wafp.*$/', $class_name))
  {
    if(preg_match('/^.*Controller$/', $class_name))
      $filepath = WAFP_CONTROLLERS_PATH . "/{$class_name}.php";
    else if(preg_match('/^.*Helper$/', $class_name))
      $filepath = WAFP_HELPERS_PATH . "/{$class_name}.php";
    else
      $filepath = WAFP_MODELS_PATH . "/{$class_name}.php";
    
    if(file_exists($filepath))
      require_once($filepath);
  }
}

global $wafp_update;
$wafp_update = new WafpUpdate();

// Gotta load the language before everything else
WafpAppController::load_language();

// For IIS compatibility
if (!function_exists('fnmatch'))
{
  function fnmatch($pattern, $string)
  {
    return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.'))."$#i", $string);
  }
}

// More Global variables
global $wafp_blogurl;
global $wafp_siteurl;
global $wafp_blogname;
global $wafp_blogdescription;

$wafp_blogurl         = ((get_option('home'))?get_option('home'):get_option('siteurl'));
$wafp_siteurl         = get_option('siteurl');
$wafp_blogname        = get_option('blogname');
$wafp_blogdescription = get_option('blogdescription');

define('WAFP_BLOGURL', $wafp_blogurl);
define('WAFP_SITEURL', $wafp_siteurl);
define('WAFP_BLOGNAME', $wafp_blogname);
define('WAFP_BLOGDESCRIPTION', $wafp_blogdescription);

global $wafp_db_version;
$wafp_db_version = 12; // this is the version of the database we're moving to

/***** SETUP OPTIONS OBJECT *****/
global $wafp_options;

$wafp_options = get_option('wafp_options');

// If unserializing didn't work
if(!$wafp_options)
{
  $wafp_options = new WafpOptions();
  update_option('wafp_options',$wafp_options);
}
else
  $wafp_options->set_default_options(); // Sets defaults for unset options

// Instansiate Models
global $wafp_db;
global $wafp_utils;
global $wafp_update;
global $wafp_current_user;

$wafp_db     = new WafpDb();
$wafp_utils  = new WafpUtils();
$wafp_update = new WafpUpdate();

WafpSubscription::register();

if(WafpUtils::is_user_logged_in())
{
  global $current_user;
  WafpUtils::get_currentuserinfo();
  
  $wafp_current_user = new WafpUser($current_user->ID);
}
else
  $wafp_current_user = false;

// Instansiate Controllers
global $wafp_app_controller;
global $wafp_options_controller;
global $wafp_links_controller;
global $wafp_dashboard_controller;
global $wafp_reports_controller;
global $wafp_integration_controller;
global $wafp_transactions_controller;
global $wafp_users_controller;
global $wafp_shortcodes_controller;
global $wafp_aweber_controller;

$wafp_app_controller          = new WafpAppController();
$wafp_options_controller      = new WafpOptionsController();
$wafp_links_controller        = new WafpLinksController();
$wafp_dashboard_controller    = new WafpDashboardController();
$wafp_reports_controller      = new WafpReportsController();
$wafp_integration_controller  = new WafpIntegrationController();
$wafp_transactions_controller = new WafpTransactionsController();
$wafp_users_controller        = new WafpUsersController();
$wafp_shortcodes_controller   = new WafpShortcodesController();
$wafp_aweber_controller       = new WafpAweberController();

global $wafp_paypal_controller;
global $wafp_authorize_controller;
global $wafp_memberpress_controller;
global $wafp_shopp_controller;

$wafp_paypal_controller      = new WafpPayPalController(false,false);
$wafp_authorize_controller   = new WafpAuthorizeController();
$wafp_memberpress_controller = new WafpMemberPressController();
$wafp_shopp_controller       = new WafpShoppController();

// Instansiate Helpers

// Setup screens
$wafp_app_controller->setup_menus();

// Include Widgets

// Register Widgets

// Include APIs

// Template Tags
?>
