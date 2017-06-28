<?php
/**
 * WooCommerce External Product Embed - Deprecated
 *
 * Add backwards compatability support for older versions.
 *
 * @class 	WCEPE_Deprecated
 * @version 3.0.0
 * @author 	Caleb Burks
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCEPE_Deprecated {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		// Register Shortcodes
		add_shortcode( 'external_product', array( __CLASS__, 'external_product' ) );
		add_shortcode( 'recent_external_products', array( __CLASS__, 'recent_external_products' ) );
	}

	/**
	 * Create the [external_product] shortcode.
	 * Essentially just map old attribute fields to our new shortcode.
	 */
	public static function external_product( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		$default_atts = apply_filters( 'wcepe_external_product_shortcode', array(
			'id'     => '',
			'image'  => 'show',
			'title'  => 'show',
			'price'  => 'show',
			'rating' => 'show',
			'button' => 'View Product'
		) );

		$atts = shortcode_atts( $default_atts, $atts, 'external_product' );

		// Turn hidden parts into a comma seperated list.
		$parts_to_hide = self::hidden_parts_list( $atts );

		return do_shortcode( "[wcepe_products ids='" . $atts['id'] . "' hide='" . $parts_to_hide . "' button='" . $atts['button'] . "']" );
	}

	/**
	 * Create the [recent_external_products] shortcode.
	 * Essentially just map old attribute fields to our new shortcode.
	 */
	public static function recent_external_products( $atts ) {
		$default_atts = apply_filters( 'wcepe_recent_external_products_shortcode', array(
			'number' => '4',
			'image'  => 'show',
			'title'  => 'show',
			'price'  => 'show',
			'rating' => 'show',
			'button' => 'View Product'
		) );

		$atts = shortcode_atts( $default_atts, $atts, 'recent_external_products' );

		// Turn hidden parts into a comma seperated list.
		$parts_to_hide = self::hidden_parts_list( $atts );

		return do_shortcode( "[wcepe_products number='" . $atts['number'] . "' hide='" . $parts_to_hide . "' button='" . $atts['button'] . "']" );
	}

	/**
	 * Turn hidden parts into a comma seperated list.
	 */
	public static function hidden_parts_list( $atts ) {
		$parts_to_hide = array( 'image', 'title', 'price', 'rating', 'button' );
		$hide = array();

		foreach ( $parts_to_hide as $part_to_hide ) {
			// The button attribute was also used to give custom button text.
			if ( 'button' == $part_to_hide && 'hide' != strtolower( $atts[ $part_to_hide ] ) ) {
				continue;
			}

			if ( 'show' !== $atts[ $part_to_hide ] ) {
				$hide[] = $part_to_hide;
			}
		}

		return implode(', ', $hide );
	}

}
