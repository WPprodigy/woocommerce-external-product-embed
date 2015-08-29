<?php
/**
 * WooCommerce External Product Embed Transients
 *
 * Get product information and set the transients.
 *
 * @class 	Woocommerce_External_Product_Embed_Transients
 * @version 1.0
 * @author 	Caleb Burks
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Woocommerce_External_Product_Embed_Transients {

	/** @var int product ID */
	public $product_id;


	/**
	 * Gather $product_id
	 */
	public function __construct( $product_id = NULL ) {

		$this->product_id = $product_id;

		// Admin Settings
		require_once 'class-woocommerce-external-product-embed-admin.php';

	} 


	/**
	 * Connect to the REST API
	 */
	private function store_api_info() {

		if ( ! class_exists( "WC_API_Client" ) ) {
			require_once 'API-Client-Library/woocommerce-api.php';
		}

		$setting         = get_option( 'wcepe_settings' );
		$store_url       = $setting['wcepe_store_url'];
		$consumer_key    = $setting['wcepe_consumer_key'];
		$consumer_secret = $setting['wcepe_consumer_secret'];

		$options = array(
			'debug'           => false,
			'return_as_array' => false,
			'validate_url'    => false,
			'timeout'         => 30,
			'ssl_verify'      => false,
		);

		$wc_api = new WC_API_Client( $store_url, $consumer_key, $consumer_secret, $options );

		return $wc_api;

	}


	/**
	 * Get a single product
	 */
	public function get_product() {

		$wc_api = $this->store_api_info();

		return $wc_api->products->get( $this->product_id );

	}


	/**
	 * Get recent products
	 */
	public function get_recent_products( $number ) {

		$wc_api = $this->store_api_info();
		$recent_product_objects = $wc_api->products->get( $id = null,  array( 'filter[limit]' => $number ) );

		return $recent_product_objects->products;

	}


	/**
	 * Set the transient time
	 */
	private function get_transient_time() {

		$setting = get_option( 'wcepe_settings' );
		$time    = $setting['wcepe_transient_time'];

		// Set the default time to 1 day
		if ( $time == '' ) {
			$time = apply_filters( 'wcepe_filter_default_transient_time', '86400' );
		}

		return $time;

	}


	/**
	 * Set the transients
	 */
	private function make_transient( $name, $data ) {

		// Check if the transient exists
		if ( false === ( $value = get_transient( 'wcepe_external_product_' . $name ) ) ) {

	    	set_transient( 'wcepe_external_product_' . $name, $data, $this->get_transient_time() );

		}

		return get_transient( 'wcepe_external_product_' . $name );

	}


	/**
	 * Checks if the product exists
	 */
	private function does_the_product_exist() {

		$number = apply_filters( 'wcepe_filter_number_of_products', '999' );

		$wc_api          = $this->store_api_info();
		$object_products = $wc_api->products->get( $id = null,  array( 'filter[limit]' => $number ) );
		$products        = $object_products->products;

		$ids = array();

		// Get all ID's in an array
		foreach ( $products as $product ) {
			$ids[] = $product->id;
		}

		if ( in_array( $this->product_id, $ids ) ) {
		    return true;
		} else {
			return false;
		}

		return $exists;

	}


	/**
	 * Returns the product's title
	 */
	private function product_title() {

		$product = $this->get_product();
		$title = apply_filters( 'wcepe_filter_title', $product->product->title );

		return $title;

	}


	/**
	 * Returns the product's image
	 */
	private function product_image() {

		$product = $this->get_product();
		$image = apply_filters( 'wcepe_filter_image', $product->product->images[0]->src );

		return $image;

	}


	/**
	 * Returns the product's price
	 */
	private function product_price() {

		$product = $this->get_product();
		$price = apply_filters( 'wcepe_filter_price', $product->product->price_html );

		return $price;

	}


	/**
	 * Returns the product's link
	 */
	private function product_link() {

		$product = $this->get_product();
		$link = apply_filters( 'wcepe_filter_link', $product->product->permalink );

		return $link;

	}


	/**
	 * Returns the product's rating in a pixel width.
	 */
	private function product_rating() {

		$product = $this->get_product();
		$rating = apply_filters( 'wcepe_filter_rating', $product->product->average_rating );

		$pixel_width = array(
			'5'  => '99px',
			'4'  => '77px',
			'3'  => '58px',
			'2'  => '38px',
			'1'  => '20px',
			'0'  => '0'
		);

		// Allow the above pixels to be changed in a filter.
		$filtered_pixel_width = apply_filters( 'wcepe_filter_rating_widths',  $pixel_width );

		// Choose which width should be shown
		if ( $rating >= '5' ) {
			$width = $filtered_pixel_width[5]; 
		} else if ( $rating >= '4' ) {
			$width = $filtered_pixel_width[4]; 
		} else if ( $rating >= '3' ) {
			$width = $filtered_pixel_width[3]; 
		} else if ( $rating >= '2' ) {
			$width = $filtered_pixel_width[2]; 
		} else if ( $rating >= '1' ) {
			$width = $filtered_pixel_width[1]; 
		} else if ( $rating <= '.9' ) {
			$width = $filtered_pixel_width[0];
		} else {
			$width = $filtered_pixel_width[0];
		}

		return $width;

	}

	/**
	 * Get recent product IDs
	 */
	private function recent_products( $number ) {

		$recent_products = $this->get_recent_products( $number );

		$ids = array();

		foreach ( $recent_products as $product ) {
			$ids[] = $product->id;
		}

		//return $this->make_transient( $number . '_recents', $ids );

		return $ids;

	}


	/**
	 * Pulls all product data into one transient
	 */
	public function product_data_transient() {

		// Check if we need to go through the transient creation process
		if ( false === ( $value = get_transient( 'wcepe_external_product_' . $this->product_id . '_data' ) ) ) {

			// Only create one item in the array if the product does not exist
			if ( ! $this->does_the_product_exist() ) {

				$product = array(
					'exists'  => 'no'
				);

				return $this->make_transient( $this->product_id . '_data', $product );

			} else {

				$product = array(
					'title'  => $this->product_title(),
					'image'  => $this->product_image(),
					'price'  => $this->product_price(),
					'link'   => $this->product_link(),
					'rating' => $this->product_rating(),
					'exists' => 'yes'
				);

				$filtered_product = apply_filters( 'wcepe_filter_product_before_transient',  $product );

	    		return $this->make_transient( $this->product_id . '_data', $filtered_product );

			}

		} else {

			// The transient exists so just return it
			return get_transient( 'wcepe_external_product_' . $this->product_id . '_data' );

		}

	}


	/**
	 * Get recent product IDs
	 */
	public function recent_product_ids( $number ) {

		// Check if we need to go through the transient creation process
		if ( false === ( $value = get_transient( 'wcepe_external_product_' . $number . '_recents' ) ) ) {

			$ids = $this->recent_products( $number );

			return $this->make_transient( $number . '_recents', $ids );
			
		} else {

			// The transient exists so just return it
			return get_transient( 'wcepe_external_product_' . $number . '_recents' );

		}

	}


	/**
	 * Get all of the product data and set it to an array
	 */
	public function get_all_product_data() {

		$transient = $this->product_data_transient();

		// Don't look for other data if the product does not exist
		if ( $transient['exists'] == 'no' ) {
			return false;
		}

		$product = array(
			'title'  => $transient['title'],
			'image'  => $transient['image'],
			'price'  => $transient['price'],
			'link'   => $transient['link'],
			'rating' => $transient['rating']
		);

		$filtered_product = apply_filters( 'wcepe_filter_product',  $product );

		return $filtered_product;

	}


} // End Class

new Woocommerce_External_Product_Embed_Transients();