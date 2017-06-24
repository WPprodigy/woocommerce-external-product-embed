<?php
/**
 * WooCommerce External Product Embed
 *
 * Set up shortcodes.
 *
 * @class 	Woocommerce_External_Product_Embed
 * @version 1.0
 * @author 	Caleb Burks
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Woocommerce_External_Product_Embed {

	public function __construct() {
		// Enqueue CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'register_css' ) );

		// Load Text Domain
		add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );

		// Load Admin
		require_once 'class-wcepe-admin.php';
		add_action( 'init', array( 'WCEPE_Admin', 'init' ) );

		// Register Shortcodes
		require_once 'class-wcepe-shortcodes.php';
		add_action( 'init', array( 'WCEPE_Shortcodes', 'init' ) );
	}

	/**
	 * Load Text Domain
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'woocommerce-external-product-embed', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Register the stylesheet
	 */
	public function register_css() {
		wp_register_style( 'wcepe-styles', plugins_url( 'assets/styles.css', dirname( plugin_basename( __FILE__ ) ) ) );
	}

} // End Class

new Woocommerce_External_Product_Embed();
