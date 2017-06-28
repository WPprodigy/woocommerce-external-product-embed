<?php
/**
 * Plugin Name: WooCommerce External Product Embed
 * Plugin URI: http://calebburks.com/plugins
 * Description: Provides a shortcode to embed products from another store.
 * Version: 3.0.0-beta
 * Author: Caleb Burks
 * Author URI: http://calebburks.com
 * Copyright: (c) 2017 Caleb Burks
 * License: GPL v3 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 * Text Domain: woocommerce-external-product-embed
 * Domain Path: /languages/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Woocommerce_External_Product_Embed' ) ) :

class Woocommerce_External_Product_Embed {

	/**
	 * Plugin version, used for styles/scripts.
	 */
	public $version = '3.0.0';

	/**
	 * The single instance of the class.
	 */
	protected static $_instance = null;

	/**
	 * Ensures only one instance of this class is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		// Load Text Domain
		add_action( 'init', array( $this, 'load_text_domain' ) );

		// Add plugin action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

		// Autoloader for the external "WooCommerce API - PHP Client".
		include_once( __DIR__ . '/vendor/autoload.php' );

		// Frontend Only
		if ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) ) {
			// Register Styles
			add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );

			// Shortcodes Class
			include_once( dirname( __FILE__ ) . '/classes/class-wcepe-shortcodes.php' );
			add_action( 'init', array( 'WCEPE_Shortcodes', 'init' ) );

			// Deprecated Class - Backwards Compatability
			include_once( dirname( __FILE__ ) . '/classes/class-wcepe-deprecated.php' );
			add_action( 'init', array( 'WCEPE_Deprecated', 'init' ) );
		}

		// Load Admin
		include_once( dirname( __FILE__ ) . '/classes/class-wcepe-admin.php' );
		add_action( 'init', array( 'WCEPE_Admin', 'init' ) );

		do_action( 'wcepe_loaded' );
	}

	/**
	 * Load Text Domain
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'woocommerce-external-product-embed', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Show action links on the plugin screen.
	 */
	public function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=wc_external_product_embed' ) . '" aria-label="' . esc_attr( __( 'View settings', 'woocommerce-external-product-embed' ) ) . '">' . __( 'Settings', 'woocommerce-external-product-embed' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Register the stylesheet
	 */
	public function register_styles() {
		wp_register_style( 'wcepe-styles', plugins_url( 'assets/styles.css', plugin_basename( __FILE__ ) ) );
	}

}

endif;

/**
 * Main instance of Woocommerce_External_Product_Embed.
 *
 * Returns the main instance of this class to prevent the need to use globals.
 */
function WCEPE() {
	return Woocommerce_External_Product_Embed::instance();
}

// Run this thing.
WCEPE();
