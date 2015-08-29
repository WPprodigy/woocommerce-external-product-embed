<?php
/**
 * Plugin Name: WooCommerce External Product Embed
 * Plugin URI: http://calebburks.com
 * Description: Provides a shortcode to embed products from another store.
 * Version: 2.0
 * Author: Caleb Burks
 * Author URI: http://calebburks.com
 * Copyright: (c) 2015 Caleb Burks
 * License: GPL v3 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 * Text Domain: woocommerce-external-product-embed
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


add_action( 'plugins_loaded', 'wcepe_load_after_plugins_loaded' );

function wcepe_load_after_plugins_loaded() {

	if ( ! class_exists( "Woocommerce_External_Product_Embed" ) ) {

		require_once( 'classes/class-woocommerce-external-product-embed.php' );

	}

}

/* Silence is Golden */