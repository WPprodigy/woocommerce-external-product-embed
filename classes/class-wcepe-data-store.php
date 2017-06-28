<?php
/**
 * WooCommerce External Product Embed - Data Store
 *
 * Retrieve/Save product data.
 *
 * @class 	WCEPE_Data_Store
 * @version 3.0.0
 * @author 	Caleb Burks
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCEPE_Data_Store {

	/**
	 * Query Arguments. Gets passed along the to rest api.
	 */
	private $query_args = array();

	/**
	 * Constructor.
	 */
	public function __construct( $query_args = array() ) {
		$this->query_args = $query_args;

		// API Client Helper
		require_once 'class-wcepe-api-client.php';
	}


	/**
	 * Get transient name.
	 * Hashed key, taken from the query args that retrieved the products.
	 */
	public function get_transient_name() {
		return 'wcepe_loop_' . substr( md5( json_encode( $this->query_args  ) ), 24 );
	}

	/**
	 * Get an array full of products to be displayed in the loop.
	 */
	public function get_loop_products() {
		// Load from cache if available.
		$loop_products = get_transient( $this->get_transient_name() );

		// Cache found, return that.
		if ( false !== $loop_products ) {
			return $loop_products;
		}

		return $this->save_loop_products();
	}

	/**
	 * Run the API requests and save all the products data into a single transient.
	 */
	private function save_loop_products() {
		// Query the data from the REST API.
		$wc_api = new WCEPE_API_Client();
		$products = $wc_api->get_products( $this->query_args );

		if ( ! is_array( $products ) ) {
			return;
		}

		$data = array();
		foreach ( $products as $product ) {
			$data[] = $this->prepare_product_data( $product );
		}

		$transient_set_time = $this->get_transient_time();
		if ( '0' != $transient_set_time ) {
			// Create wcepe_loop_* transient.
			set_transient( $this->get_transient_name(), $data, $transient_set_time );
		}

		return $data;
	}

	/**
	 * Prepare the data from a product to be saved.
	 */
	private function prepare_product_data( $product ) {
		$product_data = array();

		// Sanitize all the things.
		foreach ( $product as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $attribute => $attr_val ) {
					$product_data[ sanitize_key( $key ) ][ sanitize_key( $attribute ) ] = sanitize_text_field( $attr_val );
				}
			} else if( 'rating' === $key ) {
				$product_data[ sanitize_key( $key ) ] = $this->prepare_product_rating( $value );
			} else if( 'price' === $key ) {
				// TODO: Sanitize HTML?
				$product_data[ sanitize_key( $key ) ] = $value;
			} else {
				$product_data[ sanitize_key( $key ) ] = sanitize_text_field( $value );
			}
		}

		return $product_data;
	}

	/**
	 * Convert number into a pixel width for the stars.
	 *TODO: Find a better way.
	 */
	private function prepare_product_rating( $rating ) {
		// Can be filtered for different themes/styles.
		$pixel_widths = apply_filters( 'wcepe_product_rating_widths', array(
			'5'  => '99px',
			'4'  => '77px',
			'3'  => '58px',
			'2'  => '38px',
			'1'  => '20px',
			'0'  => '0'
		) );

		if ( $rating >= '5' ) {
			$width = $pixel_widths[5];
		} else if ( $rating >= '4' ) {
			$width = $pixel_widths[4];
		} else if ( $rating >= '3' ) {
			$width = $pixel_widths[3];
		} else if ( $rating >= '2' ) {
			$width = $pixel_widths[2];
		} else if ( $rating >= '1' ) {
			$width = $pixel_widths[1];
		} else if ( $rating <= '.9' ) {
			$width = $pixel_widths[0];
		} else {
			$width = $pixel_widths[0];
		}

		return $width;
	}

	/**
	 * Get the transient time.
	 */
	private function get_transient_time() {
		$setting = get_option( 'wcepe_settings' );
		$time    = $setting['wcepe_transient_time'];

		// Set the default time to 1 day.
		if ( '' === $time ) {
			$time = '86400';
		}

		return $time;
	}


	/**
	 * Delete all transients created by WCEPE.
	 */
	public static function delete_transients() {
		global $wpdb;

		$transient_search = "SELECT `option_name` AS `name`, `option_value` AS `value`
		FROM  $wpdb->options
		WHERE `option_name` LIKE '%transient_wcepe_loop__%'
		ORDER BY `option_name`";

		$transients = $wpdb->get_results( $transient_search );
		$prefix     = '_transient_';

		if ( ! empty( $transients ) ) {

			$transients_to_clear = array();
			foreach ( $transients as $result ) {
				$transients_to_clear[] = $result->name;
			}

			$number_to_delete = count( $transients_to_clear );

			// Delete the transients
			foreach( $transients_to_clear as $transient ) {
				if ( substr( $transient, 0, strlen( $prefix ) ) == $prefix ) {
					$transient_name = substr( $transient, strlen( $prefix ) );
					delete_transient( $transient_name );
				}
			}

			return $number_to_delete;
		}
	}

}
