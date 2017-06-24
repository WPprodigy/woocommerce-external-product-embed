<?php
/**
 * WooCommerce External Product Embed - Shortcodes
 *
 * Set up the shortcodes used by the plugin.
 *
 * @class 	WCEPE_Shortcodes
 * @version 3.0
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
			'wcepe_product_category'   => __CLASS__ . '::product_category',
      'wcepe_recent_products'    => __CLASS__ . '::recent_products',
      'wcepe_sale_products'      => __CLASS__ . '::sale_products',
      'wcepe_featured_products'  => __CLASS__ . '::featured_products',
			// 'wcepe_product_categories' => __CLASS__ . '::product_categories',
			// 'wcepe_add_to_cart'        => __CLASS__ . '::product_add_to_cart',
			// 'wcepe_add_to_cart_url'    => __CLASS__ . '::product_add_to_cart_url',
			// 'wcepe_product_attribute'  => __CLASS__ . '::product_attribute',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}

    require_once 'class-wcepe-data-store.php';

    add_action( 'wcepe_product_content_template', __CLASS__ . '::product_content_template', 10 );
	}

  /**
	 * Get the template file.
	 */
	public static function product_content_template( $product ) {
		// Check if template has been overriden
		if ( file_exists( get_stylesheet_directory() . '/woocommerce-external-product-embed/shortcodes/external-product-single.php' ) ) {
			$template = get_stylesheet_directory() . '/woocommerce-external-product-embed/shortcodes/external-product-single.php';
		} else {
			$template = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/shortcodes/external-product-single.php';
		}

    include( $template );
	}

  /**
	 * Loop over products.
	 */
	private static function product_loop( $query_args, $atts, $loop_name ) {
    $transient_name = 'wcepe_loop_' . $loop_name . substr( md5( json_encode( $query_args ) . $loop_name ), 25 );
    $data_store = new WCEPE_Data_Store( $query_args, $transient_name );
    $products = $data_store->get_loop_products();

    // TODO: Abstract this out.
    wp_enqueue_style( 'wcepe-styles' );
		wp_enqueue_style( 'dashicons' );

		ob_start();

		if ( $products ) {

      do_action( "woocommerce_shortcode_before_{$loop_name}_loop", $atts );

      foreach ( $products as $product ) {
        // product_content_template - priority 10.
        do_action( 'wcepe_product_content_template', $product );
      }

      do_action( "woocommerce_shortcode_after_{$loop_name}_loop", $atts );

    }

    return '<div class="woocommerce wcepe_external_product_wrap columns-' . 4 . '"><ul class="products wcepe_external_products">' . ob_get_clean() . '</ul></div>';
	}


  /**
   * Show a single product, or list multiple products by SKU or ID.
   *
   * @param array $atts
   * @return string
   */
  public static function products( $atts ) {
  	$atts = shortcode_atts( array(
  		// 'ids'     => '99,96,2342',
  		'skus'    => '   skuhappy, sku2, skupatient  ',
  	), $atts, 'products' );

    $query_args = array();
    $query_args['add_per_page'] = 10;

  	if ( ! empty( $atts['skus'] ) ) {
      // WC Core won't accept an array. So we need to clear out all whitespace.
      $skus = implode( ",", array_map( 'trim', explode( ',', $atts['skus'] ) ) );

      $query_args['sku'] = $skus;
      $query_args['add_per_page'] += count( explode( ',', $skus ) );
  	}

    if ( ! empty( $atts['ids'] ) ) {
      // IDs can be an array, but for consistency with sku and category ¯\_(ツ)_/¯
      $ids = implode( ",", array_map( 'trim', explode( ',', $atts['ids'] ) ) );

      $query_args['include'] = $ids;
      $query_args['add_per_page'] += count( explode( ',', $ids ) );
  	}

  	return self::product_loop( $query_args, $atts, 'products' );
  }


}
