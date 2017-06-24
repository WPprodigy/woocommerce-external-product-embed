<?php
/**
 * WooCommerce External Product Embed - API Client
 *
 * Connect to the external website and get product information.
 *
 * @class 	WCEPE_API_Client
 * @version 3.0
 * @author 	Caleb Burks
 *
 */

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCEPE_API_Client {

	/**
	 * Set up the API Request.
	 */
	private function connect() {
    $settings = get_option( 'wcepe_settings' );
    $options  = [
      'wp_api' => true,
      'version' => 'wc/v2'
    ];

    return new Client(
      $settings['wcepe_store_url'],
      $settings['wcepe_consumer_key'],
      $settings['wcepe_consumer_secret'],
      $options
    );
	}

  /**
	 * Test connection to external store.
	 */
	public function test_connection() {
    $wc_api = $this->connect();

    // Try to get the latest product to test the connection.
    try {
      $wc_api->get( 'products/', array( 'per_page' => 1 ) );
    } catch ( HttpClientException $e ) {
      return $e->getMessage();
    }
	}

  /**
	 * Get products by ID.
	 */
	public function get_products_by_id( $ids = '99,96,2342' ) {
    return $this->get_products( [
      'include' => $ids,
      'per_page' => count( explode( ",", $ids ) )
    ] );
	}

  /**
	 * Get products by SKU.
	 */
	public function get_products_by_sku( $skus = 'sku1,sku2' ) {
    return $this->get_products( [
      'sku' => $skus,
      'per_page' => count( explode( ",", $skus ) )
    ] );
	}

  /**
	 * Get products by category.
	 */
	public function get_products_by_category( $category_ids = '9,11', $number = 5 ) {
    return $this->get_products( [
      'category' => $category_ids,
      'per_page' => $number
    ] );
	}

  /**
	 * Get featured products.
	 */
	public function get_featured_products( $number = 5 ) {
    return $this->get_products( [
      'featured' => true,
      'per_page' => $number
    ] );
	}

  /**
	 * Get on-sale products.
	 */
	public function get_onsale_products( $number = 5 ) {
    return $this->get_products( [
      'on_sale' => true,
      'per_page' => $number
    ] );
	}

  /**
	 * Get recent products.
	 */
	public function get_recent_products( $number = 5 ) {
    return $this->get_products( [
      'per_page' => $number,
      'orderby' => 'date'
    ] );
	}

  /**
	 * Get products.
	 */
	public function get_products( $args = array() ) {
    $wc_api = $this->connect();
    // print_r($args);

    $default_args = apply_filters( 'wcepe_default_query_args', array(
      'status'  => 'publish',
      'order'   => 'desc',
      'orderby' => 'date'
    ) );

    $results = $wc_api->get(
      'products/',
      array_merge( $default_args, $args)
    );

    $products = array();
    foreach ( $results as $product ) {
      $products[] = $this->get_product_data( $product );
    }

    return $products;
	}

  /**
	 * Get a the data we need from a product.
	 */
	public function get_product_data( $product ) {
    // List of values we need from the API for a single product.
    $needed_data = apply_filters( 'wcepe_product_needed_data', array(
      'title'  => 'name',
			'image'  => array( 'src', 'name', 'alt' ),
			'price'  => 'price_html',
			'link'   => 'permalink',
			'rating' => 'average_rating'
    ) );

    $product_data = array();
    foreach ( $needed_data as $key => $value ) {
      if ( 'image' === $key ) {
        // Images need some special handeling.
        foreach ( $value as $attribute ) {
          $product_data[ 'image' ][ $attribute ] = $product['images'][0][ $attribute ];
        }
      } else {
        $product_data[ $key ] = $product[ $value ];
      }
    }

    return apply_filters( 'wcepe_product_data',  $product_data );
	}


	public function test_display() {
    return $this->get_recent_products();
	}

}
