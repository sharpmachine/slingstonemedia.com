<?php
/*
Plugin Name: Shopp Cache Helper
Description: Lets Shopp integrate with caching plugins. Currently designed for W3 Total Cache, but the cookie strategy will work for any cache plugin that monitors cookies. Other caching plugins will require manual configuration, but W3 Total Cache is configured automatically.
- Sets a cookie whenever the cart is updated. If the cart becomes empty the cookie is purged. 
- Sets DONOTCACHEPAGE constant on 'init' action to make double sure that pages with shopping cart items are never cached
- Clears the cache when product, category etc. details are changed.
- Integrates with W3 Total Cache to automatically updated caching settings
- Configures common Shopp paths that should not be cached
Author: Tyson LT
Version: 1.0
*/

global $defaultOptions;
$defaultOptions['shopp-cache-helper-cookie-name'] = 'shopp_items_in_cart';
$defaultOptions['shopp-cache-helper-cookie-future-seconds'] = 3600;
$defaultOptions['shopp-cache-helper-cookie-past-seconds'] = 41932800;
$defaultOptions['shopp-cache-helper-cookie-path'] = '/';
$defaultOptions['shopp-cache-helper-shopp-nocache-paths'] = "/shopp\r\n/shop/cart\r\n/shop/checkout\r\n/shop/confirm-order\r\n/shop/receipt\r\n/shop/download\r\n/shop/thanks\r\n/shop/account\r\nreceipt\.php";
$defaultOptions['shopp-cache-helper-clear-cache-on-shopp-data-change'] = '1';

/**
 * Set up our actions.
 */
register_action_hooks();

/**
 * Called when a user adds or removes an item from the cart.
 *
 * Sets a cookie that can be used to tell caching plugins to stop caching.
 */
function shopp_cache_helper_cart_updated($cart) {

  $cookie = _get_option('shopp-cache-helper-cookie-name');
  $plus = _get_option('shopp-cache-helper-cookie-future-seconds');
  $minus = _get_option('shopp-cache-helper-cookie-past-seconds');
  $path = _get_option('shopp-cache-helper-cookie-path');
	
  //cart updated, check for contents
  if (count($cart->contents) > 0) {
 
    //set cookie to tell cache manager to go dynamic
    setcookie($cookie, $cookie, time() + $plus, $path);

    //echo '<script>jQuery(".widget_shoppcartwidget").style.display="block";</script>';

  } else {

    //safe to remove cookie, cart is empty
    setcookie($cookie, '', time() - $minus, $path);

    //echo '<script>jQuery(".widget_shoppcartwidget").style.display="none";</script>';

  }

}

/**
 * Called whenever any data is changed in the shopp backend.
 *
 * Clears the cache to refresh products, categories, and settings.
 */
function shopp_cache_helper_data_changed() {

    //flush w3tc page cache
    if ('1' == _get_option('shopp-cache-helper-clear-cache-on-shopp-data-change') && is_w3tc_installed()) {
      w3tc_pgcache_flush();
    }

}

/**
 * Sets DONOTCACHEPAGE if items found in the cart. 
 */
function shopp_cache_helper_set_constant() {
	
	if (shopp('cart', 'hasitems')) {	
		define('DONOTCACHEPAGE', true);
	}
	
}

/**
 * Register the admin menu.
 */
function shopp_cache_helper_create_menu() {

	//settings page
	add_options_page('Shopp Cache Helper', 'Shopp Cache Helper', 'administrator', 'shopp-cache-helper-settings-page', 'shopp_cache_helper_settings_page');
	
	//call register settings function
	add_action( 'admin_init', 'shopp_cache_helper_register_settings' );
}

/**
 * Our settings.
 */
function shopp_cache_helper_register_settings() {

	//register our settings
	register_setting( 'shopp-cache-helper-settings-group', 'shopp-cache-helper-cookie-name');
	register_setting( 'shopp-cache-helper-settings-group', 'shopp-cache-helper-cookie-future-seconds' );
	register_setting( 'shopp-cache-helper-settings-group', 'shopp-cache-helper-cookie-past-seconds' );
	register_setting( 'shopp-cache-helper-settings-group', 'shopp-cache-helper-cookie-path' );
	register_setting( 'shopp-cache-helper-settings-group', 'shopp-cache-helper-shopp-nocache-paths' );
	register_setting( 'shopp-cache-helper-settings-group', 'shopp-cache-helper-clear-cache-on-shopp-data-change' );

}

/**
 * Activate plugin.
 */
function shopp_cache_helper_activate() {
	
	global $defaultOptions;
	
	//set defaults
	foreach ($defaultOptions as $key => $value) {
		if ('' == trim(get_option($key))) {
			add_option($key, $value);
		}
	}	
	
	//add settings to w3tc
	add_w3tc_settings();
}

/**
 * Deactivate plugin.
 */
function shopp_cache_helper_deactivate() {

	//remove w3tc settings
        //NOTE: decided to keep setting in as they are still valid even if this plugin is inactive
	/*** remove_w3tc_settings(); ***/
	
}

/**
 * After updating cookie name or no-cache paths, udpate these settings in the cache provider.
 * 
 * @param $option
 * @param $oldvalue
 * @param $newvalue
 */
function post_update_option_callback($option, $oldvalue, $newvalue) {
	
	$message = '';

	if (is_w3tc_installed()) {
	
		if ('shopp-cache-helper-shopp-nocache-paths' == $option) {
			
			//get settings object
			$config = W3_Config::instance();
			$changed = false;
			
			//update file paths
			$reject = $config->get_array('pgcache.reject.uri');

			//remove old paths
			if (!is_array($oldvalue)) {
				$oldvalue = explode("\r\n", $oldvalue);
			}

			if (is_array($oldvalue)) {
				foreach ($oldvalue as $path) {
					$key = array_search($path, $reject);
					if (false !== $key) {
						unset($reject[$key]);
						$changed = true;					
					}
				}
			}
							
			//add new paths
			if (!is_array($newvalue)) {
				$newvalue= explode("\r\n", $newvalue);
			}

			if (is_array($newvalue)) {
				foreach ($newvalue as $path) {
					if ('' != trim($path)) {
						if (!in_array($path, $reject)) {
							array_push($reject, $path);
							$changed = true;
						}
					}
				}
			}
			
			//save new settings
			if ($changed) {
				$config->set('pgcache.reject.uri', $reject);
				$config->save();
			}

		} else if ('shopp-cache-helper-cookie-name' == $option) {
			
			//get settings object
			$config = W3_Config::instance();
			$changed = false;
			
			//update cookie
			$cookies = $config->get_array('pgcache.reject.cookie');
			
			//unset old cookie name
			if ('' != $oldvalue) {
				$key = array_search($oldvalue, $cookies);
				if (false !== $key) {
					unset($cookies[$key]);
					$changed = true;
				}				
			}
	
			//add new cookie name
			if ('' != $newvalue) {
				if (!in_array($newvalue, $cookies)) {
					array_push($cookies, $newvalue);
					$changed = true;
				}				
			}
			
			//set new value
			if ($changed) {
				$config->set('pgcache.reject.cookie', $cookies);
				$config->save();
			}
			
		}
		
	}
	
}

/**
 * Put settings into w3tc.
 */
function add_w3tc_settings() {

	if (is_w3tc_installed()) {

		//get settings object
		$config = new W3_Config;
		$changed = false;
		
		//add file paths
		$reject = $config->get_array('pgcache.reject.uri');
		$paths = explode("\r\n", _get_option('shopp-cache-helper-shopp-nocache-paths'));
		if (is_array($paths)) {
			
			//check for already there
			foreach ($paths as $path) {
				if (!in_array($path, $reject)) {
					array_push($reject, $path);
					$changed = true;
				}
			}
			
			$config->set('pgcache.reject.uri', $reject);
			
		}
		
		//add cookie
		$cookies = $config->get_array('pgcache.reject.cookie');
		$cookie_name = _get_option('shopp-cache-helper-cookie-name');
		if ('' != $cookie_name) {
			if (!in_array($cookie_name, $cookies)) {
				array_push($cookies, $cookie_name);
				$changed = true;
			}
			$config->set('pgcache.reject.cookie', $cookies);
		}

		//save config
		if ($changed) {
			$config->save();
		}
		
	}
	
}

/**
 * Remove settings from w3tc
 */
function remove_w3tc_settings() {
	
	if (is_w3tc_installed()) {
	
		//get settings object
		$config = W3_Config::instance();
		$changed = false;
		
		//remove file paths
		$reject = $config->get_array('pgcache.reject.uri');
		$paths = explode("\r\n", _get_option('shopp-cache-helper-shopp-nocache-paths'));
		if (is_array($paths)) {
			foreach ($paths as $path) {
				$key = array_search($path, $reject);
				if (false !== $key) {
					unset($reject[$key]);
					$changed = true;
				}
			}
			$config->set('pgcache.reject.uri', $reject);
		}
		
		//remove cookie
		$cookies = $config->get_array('pgcache.reject.cookie');
		$cookie_name = _get_option('shopp-cache-helper-cookie-name');
		if ('' != $cookie_name) {
			$key = array_search($cookie_name, $cookies);
			if (false !== $key) {
				unset($cookies[$key]);
				$changed = true;
			}
			$config->set('pgcache.reject.cookie', $cookies);
		}

		//save config
		if ($changed) {
			$config->save();
		}
		
	}
}

/**
 * Check for W3TC installation.
 */
function is_w3tc_installed() {
	return function_exists('w3tc_pgcache_flush');
}

/**
 * Private get_option wrapper to return defaults.
 * @param string $key
 */
function _get_option($key) {
	global $defaultOptions;
	$value = trim(get_option($key, ''));
	if ('' == $value) {
		$value = $defaultOptions[$key];
	}
	return $value;
}

/**
 * Set up our action hooks.
 */
function register_action_hooks() {

	//Create custom plugin settings menu.
	add_action('admin_menu', 'shopp_cache_helper_create_menu');
	
	//Check for cart items and write cache killer constant.
	add_action('init', 'shopp_cache_helper_set_constant');
	
	//Action to set cookie when cart updated.
	add_action('shopp_cart_updated', 'shopp_cache_helper_cart_updated'); 
	
	//Listen for option updates to clear old options from cache providers
	add_action('updated_option', 'post_update_option_callback', 10, 3);
	
	//Actions to clear cache when product data updated.
	add_action('shopp_product_saved', 'shopp_cache_helper_data_changed');
	add_action('shopp_category_saved', 'shopp_cache_helper_data_changed');
	add_action('shopp_promo_saved', 'shopp_cache_helper_data_changed');
	add_action('add_product_download', 'shopp_cache_helper_data_changed');
	
	//Activation hook.
	register_activation_hook( __FILE__, 'shopp_cache_helper_activate' );
	
	//Dectivation hook.
	register_deactivation_hook( __FILE__, 'shopp_cache_helper_deactivate' );

}
	
/**
 * Print the admin page.
 */
function shopp_cache_helper_settings_page() {
?>
<div class="wrap">
<h2>Shopp Cache Helper</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'shopp-cache-helper-settings-group' ); ?>

    <table class="form-table">

        <tr valign="top">
            <th scope="row">Cookie Name<br/></th>
    	    <td><input type="text" name="shopp-cache-helper-cookie-name" value="<?php echo get_option('shopp-cache-helper-cookie-name'); ?>" /></td>
        </tr>

       <tr valign="top">
            <th scope="row">Cookie Future Seconds<br/><small>Added to now() when setting cookie</small></th>
    	    <td><input type="text" name="shopp-cache-helper-cookie-future-seconds" value="<?php echo get_option('shopp-cache-helper-cookie-future-seconds'); ?>" /></td>
        </tr>
        
       <tr valign="top">
            <th scope="row">Cookie Past Seconds<br/><small>Subtracted from now() when removing cookie</small></th>
    	    <td><input type="text" name="shopp-cache-helper-cookie-past-seconds" value="<?php echo get_option('shopp-cache-helper-cookie-past-seconds'); ?>" /></td>
        </tr>
        
        <tr valign="top">
            <th scope="row">Cookie Path<br/></th>
    	    <td><input type="text" name="shopp-cache-helper-cookie-path" value="<?php echo get_option('shopp-cache-helper-cookie-path'); ?>" /></td>
        </tr>
               
        <tr valign="top">
            <th scope="row">Cache flushing options</th>
    	    <td>
		<label for="shopp-cache-helper-clear-cache-on-shopp-data-change">

		<input type="checkbox" id="shopp-cache-helper-clear-cache-on-shopp-data-change" name="shopp-cache-helper-clear-cache-on-shopp-data-change" value="1" 
		  <?php echo (('1' == get_option('shopp-cache-helper-clear-cache-on-shopp-data-change')) ? ' checked="checked"' : ''); ?>
		/>
		Flush page cache on Shopp data change
		</label>
	    </td>
        </tr>

        <tr valign="top">
            <th scope="row">No-cache paths</th>
	  		<td><textarea rows=12 cols=75 name="shopp-cache-helper-shopp-nocache-paths"><?=htmlspecialchars(get_option('shopp-cache-helper-shopp-nocache-paths'))?></textarea></td>
        </tr>

    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } ?>