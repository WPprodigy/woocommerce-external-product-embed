<?php
/**
 * WooCommerce External Product Embed - API Client
 *
 * Connect to the external website and get product information.
 *
 * @class 	WCEPE_API_Client
 * @version 3.0.0
 * @author 	Caleb Burks
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

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
			return 'success';
		} catch ( HttpClientException $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * Get products.
	 */
	public function get_products( $args = array() ) {
		try {
			$wc_api  = $this->connect();
			$results = $wc_api->get( 'products/', $args );

			$products = array();
			foreach ( $results as $product ) {
				$products[] = $this->get_product_data( $product );
			}

			return $products;

		} catch ( HttpClientException $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * Get the data we need from a product.
	 */
	public function get_product_data( $product ) {
		// List of values we need from the API for a single product.
		$needed_data = apply_filters( 'wcepe_product_needed_data', array(
			'title'   => 'name',
			'image'   => array( 'src', 'name', 'alt' ),
			'price'   => 'price_html',
			'link'    => 'permalink',
			'rating'  => 'average_rating',
			'on_sale' => 'on_sale'
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

}
