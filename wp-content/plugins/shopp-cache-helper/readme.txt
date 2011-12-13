=== Plugin Name ===
Contributors: tysonlt
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BJQ6ZNN4JVAZL
Tags: shopp,cache,w3-total-cache
Requires at least: 3.0.0
Tested up to: 3.0.1
Stable tag: 1.0

Helps Shopp integrate with caching plugins. Designed for W3 Total Cache, but will work on any cache plugin that checks for custom cookies.

== Description ==

Helps Shopp integrate with caching plugins. Currently designed for W3 Total Cache, but the cookie strategy will work for any cache plugin that monitors cookies. 
Other caching plugins will require manual configuration, but W3 Total Cache is configured automatically.
* Automatically integrates with W3 Total Cache to configure caching settings - no other configuration is required!
* Automatically configures common Shopp paths that should not be cached (W3 Total Cache only)
* Sets a cookie whenever the cart is updated. If the cart becomes empty the cookie is purged. 
* Sets DONOTCACHEPAGE constant on 'init' action to make sure that pages with shopping cart items are never cached.
* Clears the cache when product, category etc. details are changed.

== Installation ==

1. Upload the contents of this zip file to your `/wp-content/plugins/' folder, or use the built-in Plugin upload tool.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. To configure the cache plugin, see the screenshots.

== Frequently Asked Questions ==

= Where is the options page? =

You can configure all the options using the 'Shopp Cache Helper' entry in the 'Settings' menu. Usually the default options
are all you need to get started. In fact not many of the options really need to be configured, except possibly the option
to flush the cache when Shopp product data is changed. If you want to flush the cache manually whenever you change Shopp 
backend data (eg Product, Category, etc), just uncheck this option.

= How do I configure it with W3 Total Cache? =

You no longer have to do anything - Shopp Cache Helper will update W3 Total Cache settings automatically. 
All you have to do is activate this plugin, and Shopp will be completely configured to work with the W3 Total Cache page cache.

= How do I configure it with (Some other caching plugin)? =

Currently this plugin automatically integrates with W3 Total Cache. To configure for other caches, do the following:

* Tell your caching plugin to not serve cached pages when the cookie is detected (default cookie is 'shopp_items_in_cart') 
* Tell your caching plugin to never cache the pages listed in the 'No-cache paths' setting of this plugin.
* Make sure your caching plugin honours the 'DONOTCACHEPAGE' constant. Currently W3 Total Cache and WP Super Cache are known to honour this constant.

== Screenshots ==

1. This shows my W3 Total Cache settings in the Page Cache section. 

== Upgrade Notice ==

NOTE: This plugin now configures W3 Total Cache automatically. If you are upgrading, please check the Page Cache settings in the W3 Total Cache
admin section to ensure that your settings have been migrated correctly.

== Changelog ==

= 1.0 =
* Production release
* Admin screen added to configure caching options
* Automatically integrates with W3 Total Cache to add required settings

= 0.4 =
* Added cookie code back, uses this as well as DONOTCACHEPAGE constant for double checking
* Stop clearing page cache when cart emptied

= 0.3 =
* Replaced cookie with DONOTCACHEPAGE constant

= 0.2 =
* Added code to clear page cache when cart is emptied. (This is overkill, really should make this an option.)
* Clear page cache whenever Shopp data is changed, such as product details.

= 0.1 =
* Initial version