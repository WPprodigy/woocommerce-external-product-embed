<?php
/**
 * Plugin Name: WooCommerce External Product Embed
 * Plugin URI: http://calebburks.com
 * Description: Provides a shortcode to embed products from another store.
 * Version: 0.1
 * Author: Caleb Burks
 * Author URI: http://calebburks.com
 * Copyright: (c) 2015 Caleb Burks
 * License: GPL v3 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( "Woocommerce_External_Product_Embed" ) ) {
	require_once( 'classes/class-woocommerce-external-product-embed.php' );
}

/* Silence is Golden */