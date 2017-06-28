<?php
/**
 * WooCommerce External Product Embed - Shortcodes
 *
 * Set up the shortcodes used by the plugin.
 *
 * @class 	WCEPE_Shortcodes
 * @version 3.0.0
 * @author 	Caleb Burks
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCEPE_Shortcodes {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'wcepe_products'           => __CLASS__ . '::products',
			// 'wcepe_product_categories' => __CLASS__ . '::product_categories',
			// 'wcepe_add_to_cart'        => __CLASS__ . '::product_add_to_cart',
			// 'wcepe_add_to_cart_url'    => __CLASS__ . '::product_add_to_cart_url',
			// 'wcepe_product_attribute'  => __CLASS__ . '::product_attribute',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}

		// Data Store - used for getting products from the rest api.
		require_once 'class-wcepe-data-store.php';

		add_action( 'wcepe_product_content_template', array( __CLASS__, 'product_content_template' ), 10, 2 );
	}

	/**
	 * Get the template file.
	 */
	public static function product_content_template( $product, $atts ) {
		global $wcepe_loop;
		$wcepe_loop['columns'] = absint( $atts['columns'] );
		self::get_loop_class();

		// Check if template has been overriden
		if ( file_exists( get_stylesheet_directory() . '/wcepe/product-content.php' ) ) {
			$template = get_stylesheet_directory() . '/wcepe/product-content.php';
		} else {
			$template = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/product-content.php';
		}

		include( $template );
	}

	/**
	 * Get classname for loops based on $wcepe_loop global.
	 * Copied from WooCommerce core's `wc_get_loop_class` function.
	 */
	public static function get_loop_class() {
		global $wcepe_loop;

		$wcepe_loop['loop']    = ! empty( $wcepe_loop['loop'] ) ? $wcepe_loop['loop'] + 1   : 1;
		$wcepe_loop['columns'] = max( 1, ! empty( $wcepe_loop['columns'] ) ? $wcepe_loop['columns'] : 4 );

		if ( 0 === ( $wcepe_loop['loop'] - 1 ) % $wcepe_loop['columns'] || 1 === $wcepe_loop['columns'] ) {
			$wcepe_loop['class'] = 'first';
		} elseif ( 0 === $wcepe_loop['loop'] % $wcepe_loop['columns'] ) {
			$wcepe_loop['class'] = 'last';
		} else {
			$wcepe_loop['class'] = '';
		}
	}

	/**
	 * Loop over products.
	 */
	private static function product_loop( $query_args, $atts ) {
		$data_store = new WCEPE_Data_Store( $query_args );
		$products = $data_store->get_loop_products();

		// Only add styles if WC isn't enabled.
		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style( 'wcepe-styles' );
		}

		ob_start();

		if ( $products ) {

			do_action( 'wcepe_shortcode_before_products_loop', $atts );

			foreach ( $products as $product ) {
				// product_content_template - priority 10.
				do_action( 'wcepe_product_content_template', $product, $atts );
			}

			do_action( 'wcepe_shortcode_after_products_loop', $atts );

		}

		$opening = apply_filters( 'wcepe_products_loop_wrapper_open', '<div class="woocommerce wcepe_products_wrap columns-' . $atts['columns'] . '"><ul class="wcepe_products products">', $atts );
		$closing = apply_filters( 'wcepe_products_loop_wrapper_close', '</ul></div>', $atts );

		return $opening . ob_get_clean() . $closing;
	}

	/**
	 * Shortcode. Go through the attributes and send the query args to the product_loop().
	 */
	 public static function products( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		$default_atts = apply_filters( 'wcepe_default_products_atts', array(
			'orderby'  => 'title',
			'order'    => 'desc',
			'number'   => 12,
			'per_page' => 0,  // Will override 'number'
			'columns'  => '4',
			'ids'      => '', // Comma seperated IDs
			'skus'     => '', // Comma seperated SKUs
			'category' => '', // Comma seperated category IDs
			'recent'   => false,
			'on_sale'  => false,
			'featured' => false,
			'hide'     => '', // image, title, rating, onsale, price, or button
			'button'   => __( 'View Product', 'woocommerce-external-product-embed' ) // Button text
		) );

		$atts = shortcode_atts( $default_atts, $atts, 'wcepe_products' );

		$min_per_page = 0;
		$query_args   = apply_filters( 'wcepe_default_products_query_args', array(
			'status'       => 'publish',
			'orderby'      => $atts['orderby'],
			'order'        => $atts['order'],
			'per_page'     => absint( $atts['number'] ),
		) );

		if ( ! empty( $atts['ids'] ) ) {
			// IDs can be an array, but for consistency with sku and category ¯\_(ツ)_/¯
			$ids = implode( ",", array_map( 'trim', explode( ',', $atts['ids'] ) ) );
			$query_args['include'] = $ids;
			$min_per_page += count( explode( ',', $ids ) );
		}

		if ( ! empty( $atts['skus'] ) ) {
			// WC Core won't accept an array. We need to clear out all whitespace then convert back to comma list.
			$skus = implode( ",", array_map( 'trim', explode( ',', $atts['skus'] ) ) );
			$query_args['sku'] = $skus;
			$min_per_page += count( explode( ',', $skus ) );
		}

		if ( ! empty( $atts['category'] ) ) {
			// Same as SKUs above. Clear out all whitespace then convert back to comma list.
			$category_ids = implode( ",", array_map( 'trim', explode( ',', $atts['category'] ) ) );
			$query_args['category'] = $category_ids;
		}

		// Not really necessary, but figured people would ask about the recent shortcode.
		if ( $atts['recent'] ) {
			$query_args['orderby'] = 'date';
		}

		if ( $atts['on_sale'] ) {
			$query_args['on_sale'] = true;
		}

		if ( $atts['featured'] ) {
			$query_args['featured'] = true;
		}

		// Allow 'per_page' to override 'number' for those used to WC core shortcodes.
		if ( ! empty( $atts['per_page'] ) ) {
			$query_args['per_page'] = absint( $atts['per_page'] );
		}

		// Ensure enough products are shown if IDs or SKUs were manually entered.
		if ( $query_args['per_page'] < $min_per_page ) {
			$query_args['per_page'] =  absint( $min_per_page );
		}

		// Used to hide certain product parts in the template.
		$atts['parts_to_hide'] = array();
		if ( ! empty( $atts['hide'] ) ) {
			$atts['parts_to_hide'] = array_map( 'trim', explode( ',', strtolower( $atts['hide'] ) ) );
		}

		return self::product_loop( $query_args, $atts );
	}

}
