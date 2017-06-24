<?php
/**
 * WooCommerce External Product Embed - Data Store
 *
 * Set up the shortcodes used by the plugin.
 *
 * @class 	WCEPE_Data_Store
 * @version 3.0
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
	 * Transient Name.
   * A hashed version of the query args for a specific loop.
	 */
	private $transient_name;

  /**
	 * Constructor.
	 */
	public function __construct( $query_args, $transient_name ) {
    $this->query_args     = $query_args;
    $this->transient_name = $transient_name;

    // API Client Helper
    require_once 'class-wcepe-api-client.php';
	}

  /**
	 * Get an array full of products to be displayed in the loop.
	 */
	public function get_loop_products() {
    // Load from cache if available.
    $loop_products = get_transient( $this->transient_name );

    // Cache found, return that.
    if ( false !== $loop_products ) {
      return $loop_products;
    }

    return $this->save_loop_products( $this->query_args, $this->transient_name );
  }

  /**
	 * Run the API requests and save all the products data into a single transient.
	 */
	private function save_loop_products() {
    // Query the data from the REST API.
    $class = new WCEPE_API_Client();
    $products = $class->get_products( $this->query_args );

    $data = array();
    foreach ( $products as $product ) {
      $data[] = $this->prepare_product_data( $product );
    }

    // Create wcepe_loop_* transient.
    set_transient( $this->transient_name, $data, $this->get_transient_time() );

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
   * TODO: Find a better way.
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

}
