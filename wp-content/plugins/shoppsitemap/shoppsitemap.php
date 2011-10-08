<?php
/*
Plugin Name: Shopp Sitemap
Plugin URI: http://shopptools.com
Description: Shopp E-Commerce & Google XML Sitemap Integration
Version: 0.9
Author: Palms Development
Author URI: http://palmsdevelopment.com
*/

if ( ! defined( 'ABSPATH' ) )
	die( "Can't load this file directly" );

class ShoppSitemap
{
	private $shopp;
	
	function __construct() {
		global $Shopp;
		
		$this->shopp =& $Shopp;

		add_action( 'admin_menu', array( &$this, 'action_admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'action_register_settings' ) );
		add_action( 'admin_notices', array( &$this, 'action_admin_notices' ) );

		if ( get_option( 'shoppsitemap_index_cat', 1 ) )
			add_action( 'shopp_category_saved', array( &$this, 'action_category_saved' ) );
			
		if ( get_option( 'shoppsitemap_index_prod', 1 ) )
			add_action( 'shopp_product_saved', array( &$this, 'action_product_saved' ) );
		
		add_action( 'sm_buildmap', array( &$this, 'action_add_urls' ) );
			
		register_activation_hook( __FILE__, array( &$this, 'action_activate' ) );
	}
	
	function action_activate() {
		if ( is_plugin_active( 'shopp/Shopp.php' ) && is_plugin_active( 'google-sitemap-generator/sitemap.php' ) ) {
			$this->rebuild_url_cache();
		}
	}
	
	function action_add_urls() {
		$cat_urls = get_option( 'shoppsitemap_categories' );
		$prod_urls = get_option( 'shoppsitemap_products' );
		
		$generatorObject = GoogleSitemapGenerator::GetInstance();
		
		if( $generatorObject != null ) {
			if ( get_option( 'shoppsitemap_index_cat', 1 ) && ! empty( $cat_urls ) ) {
				$frequency = get_option( 'shoppsitemap_cat_cf' );
				$priority = get_option( 'shoppsitemap_cat_p' );
				foreach ( $cat_urls as $id => $url ) {
					$generatorObject->AddUrl( $url, 0, $frequency, $priority );
				}
			}
			
			if ( get_option( 'shoppsitemap_index_prod', 1 ) && ! empty( $prod_urls ) ) {
				$frequency = get_option( 'shoppsitemap_prod_cf' );
				$priority = get_option( 'shoppsitemap_prod_p' );
				foreach ( $prod_urls as $id => $url ) {
					$generatorObject->AddUrl( $url, 0, $frequency, $priority );
				}
			}
		}
		
		
	}
	
	function action_admin_menu() {
		add_submenu_page( 'plugins.php', 'Shopp Sitemap', 'Shopp Sitemap', SHOPP_USERLEVEL, 'shopp-sitemap-xml', array( $this, 'admin_settings' ) );
	}
	
	function action_admin_notices() {
		$errors = array();
		if ( ! is_plugin_active( 'google-sitemap-generator/sitemap.php' ) )
			$errors[] = 'Shopp Sitemap requires that the <a href="http://www.arnebrachhold.de/redir/sitemap-home/">Google XML Sitemaps</a> plugin be active.  Please install the plugin to continue.';
		
		if ( ! is_plugin_active( 'shopp/Shopp.php' ) )
			$errors[] = 'Shopp Sitemap requires that the <a href="http://www.shopplugin.net/">Shopp</a> plugin be active.  Please install the plugin to continue.';
			
		foreach ( $errors as $error ) {
			echo "<div class='error'><p>{$error}</p></div>";
		}
	}
	
	function action_product_saved( $product ) {
		$prod_urls = get_option( 'shoppsitemap_products' );
		$permalink = $this->shopp->shopuri . $product->slug;
		if ( ! isset( $prod_urls[$product->id] ) || $prod_urls[$product->id] != $permalink ) {
			$prod_urls[$product->id] = $permalink;
			update_option( 'shoppsitemap_products', $prod_urls );
			do_action( "sm_rebuild" );
		}
	}
	
	function action_category_saved( $category ) {
		$cat_urls = get_option( 'shoppsitemap_categories' );
		$permalink = trailingslashit( $this->shopp->link( 'catalog' ) )."category/{$category->slug}";
		if ( ! isset( $cat_urls[$category->id] ) || $cat_urls[$category->id] != $permalink ) {
			$cat_urls[$category->id] = $permalink;
			update_option( 'shoppsitemap_categories', $cat_urls );
			do_action( "sm_rebuild" );
		}
	}
	
	function action_register_settings() {
		register_setting( 'shoppsitemap-options', 'shoppsitemap_index_cat' );
		register_setting( 'shoppsitemap-options', 'shoppsitemap_index_prod' );
		register_setting( 'shoppsitemap-options', 'shoppsitemap_cat_cf' );
		register_setting( 'shoppsitemap-options', 'shoppsitemap_cat_p' );
		register_setting( 'shoppsitemap-options', 'shoppsitemap_prod_cf' );
		register_setting( 'shoppsitemap-options', 'shoppsitemap_prod_p' );
	}
	
	function admin_settings() {
		$updated = false;
		
		if ( $_GET['updated'] == 'true' ) {
			$this->rebuild_url_cache();
			$updated = true;
		}
		
		include( 'shoppsitemap_admin.php' );
	}
	
	function rebuild_url_cache() {
		$cat_urls = array();
		$prod_urls = array();
		
		if ( get_option( 'shoppsitemap_index_cat', 1 ) ) {
			$catalog = new Catalog();
			$catalog->outofstock = true;
			$results = $catalog->load_categories(false,false,true);
			foreach ( $results as $category ) {
				if ( ! empty( $category->slug ) ) {
					$permalink = trailingslashit( $this->shopp->link( 'catalog' ) )."category/{$category->slug}";
					$cat_urls[$category->id] = $permalink;
				}
			}
			update_option( 'shoppsitemap_categories', $cat_urls );
		}
		
		if ( get_option( 'shoppsitemap_index_prod', 1 ) ) {
			// create a dummy category, and load products from all categories
			$category = new Category();
			$catalogtable = DatabaseObject::tablename(Catalog::$table);
			$category->load_products();

			foreach ( $category->products as $product ) {
				if ( ! empty( $product->slug ) ) {
					$permalink = $this->shopp->shopuri . $product->slug;
					$prod_urls[$product->id] = $permalink;
				}
			}
			
			update_option( 'shoppsitemap_products', $prod_urls );
		}
		
		do_action( 'sm_rebuild' );
	}
}

$shopp_sitemap = new ShoppSitemap();