<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Woocommerce_External_Product_Embed {

	public function __construct () {
		if ( is_admin() ) {
			require_once 'class-woocommerce-external-product-embed-admin.php';
		}
		
		add_shortcode( 'external_product', array( $this, 'external_product_shortcode' ) );
	} 

	private function store_api_info() {
		if ( ! class_exists( "WC_API_Client" ) ) {
			require_once 'class-wc-api-client.php';
		}

		$options         = get_option( 'wcepe_settings' );
		$consumer_key    = $options['wcepe_text_field_0'];
		$consumer_secret = $options['wcepe_text_field_1'];
		$store_url       = $options['wcepe_text_field_2'];

		$wc_api = new WC_API_Client( $consumer_key, $consumer_secret, $store_url );
		return $wc_api;
	}

	public function get_transient_time() {
		$options = get_option( 'wcepe_settings' );
		$time    = $options['wcepe_text_field_3'];

		if ( $time == '' ) {
			$time = '86400';
		}

		return $time;
	}

	/* Set Template File */

	public function get_template_file() {
		// Check if template has been overriden
		if ( file_exists( get_stylesheet_directory() . '/woocommerce-external-product-embed/shortcodes/external-product-single.php' ) ) {
			return get_stylesheet_directory() . '/woocommerce-external-product-embed/shortcodes/external-product-single.php';
		} else {
			return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/shortcodes/external-product-single.php';
		}
	}

	/* Set Link Transient */

	private function get_external_product_link( $external_product_id ) {
		$wc_api   = $this->store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$link     = $external->product->permalink;

		return $link;
	}

	private function set_external_product_link_transient( $external_product_id ) {
		if ( false === ( $value = get_transient( 'wcepe_external_product_link_' . $external_product_id ) ) ) {
	    	set_transient( 'wcepe_external_product_link_' . $external_product_id, 
	    	$this->get_external_product_link( $external_product_id ), $this->get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_link_' . $external_product_id );
	}

	/* Set Image Transient*/

	private function get_external_product_image( $external_product_id ) {
		$wc_api   = $this->store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$image    = $external->product->images[0]->src;

		return $image;
	}

	private function set_external_product_image_transient( $external_product_id ) {
		if ( false === ( $value = get_transient( 'wcepe_external_product_image_' . $external_product_id ) ) ) {
	    	set_transient( 'wcepe_external_product_image_' . $external_product_id, 
	    	$this->get_external_product_image( $external_product_id ), $this->get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_image_' . $external_product_id );
	}


	/* Set Title Transient */

	private function get_external_product_title( $external_product_id ) {
		$wc_api   = $this->store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$title    = $external->product->title;

		return $title;
	}

	private function set_external_product_title_transient( $external_product_id ) {
		if ( false === ( $value = get_transient( 'wcepe_external_product_title_' . $external_product_id ) ) ) {
	    	set_transient( 'wcepe_external_product_title_' . $external_product_id, 
	    	$this->get_external_product_title( $external_product_id ), $this->get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_title_' . $external_product_id );
	}

	/* Set Price Transient*/

	private function get_external_product_price( $external_product_id ) {
		$wc_api   = $this->store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$price    = $external->product->price_html;

		return $price;
	}

	private function set_external_product_price_transient( $external_product_id ) {
		if ( false === ( $value = get_transient( 'wcepe_external_product_price_' . $external_product_id ) ) ) {
	    	set_transient( 'wcepe_external_product_price_' . $external_product_id, 
	    	$this->get_external_product_price( $external_product_id ), $this->get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_price_' . $external_product_id );
	}

	/* Set Rating Transient */

	private function get_external_product_rating( $external_product_id ) {
		$wc_api   = $this->store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$rating   = $external->product->average_rating;

		return $rating;
	}

	private function set_external_product_rating_transient( $external_product_id ) {
		if ( false === ( $value = get_transient( 'wcepe_external_product_rating_' . $external_product_id ) ) ) {
	    	set_transient( 'wcepe_external_product_rating_' . $external_product_id, 
	    	$this->get_external_product_rating( $external_product_id ), $this->get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_rating_' . $external_product_id );
	}

	private function return_rating_width( $external_product_id ) {
		$number = $this->set_external_product_rating_transient( $external_product_id );

		if ( $number >= '5' ) {
			$width = '99px'; 
		} else if ( $number >= '4' ) {
			$width = '77px'; 
		} else if ( $number >= '3' ) {
			$width = '58px'; 
		} else if ( $number >= '2' ) {
			$width = '38px'; 
		} else if ( $number >= '1' ) {
			$width = '20px'; 
		} else if ( $number <= '.9' ) {
			$width = '0'; 
		}

		return $width;
	}

	/* Test for a Valid ID */

	public function test_for_valid_id( $id ) {
		$check_for_valid_product = true;
		if ( $id != '' ) {
			$id_check = explode( ',', $id );
			$id_count = count( $id_check );

			if ( $id_count == 1 ) {
				foreach ( $id_check as $id_test ) {
					$check = $this->set_external_product_link_transient( $id_test );

					if ( $check == '' ) {
						$check_for_valid_product = false;
					} 
				}
			}
		}

		return $check_for_valid_product;
	}

	/* Create Shortcode */

	public function external_product_shortcode( $atts ) {

		extract( shortcode_atts(
			array(
				'id'     => '',
				'image'  => 'show',
				'title'  => 'show',
				'price'  => 'show',
				'rating' => 'show',
				'button' => 'View Product',
			), $atts )
		);

		// Add styles when shortcode is used on page
		wp_enqueue_style( 'wcepe_styles' );
		wp_enqueue_style( 'dashicons' );

		// Check to see if there is a valid ID
		$check_for_valid_product = $this->test_for_valid_id( $id );

		if ($id != '' && $check_for_valid_product == true ) {
			$ids = explode( ',', $id );

			$content  .= '<div class="wcepe_external_product_wrap"><ul class="wcepe_external_products">';

			foreach ( $ids as $id ) {

				// Set Show/Hide Variables for Template
				$show_image  = $image;
				$show_title  = $title;
				$show_price  = $price;
				$show_rating = $rating;

				// Set Variables for Template
				$product_rating = $this->return_rating_width( $id );
				$product_link   = $this->set_external_product_link_transient( $id );
				$product_title  = $this->set_external_product_title_transient( $id );
				$product_image  = $this->set_external_product_image_transient( $id );
				$product_price  = $this->set_external_product_price_transient( $id ); 

				ob_start();
				include( $this->get_template_file() );
				$content .= ob_get_clean();

			}

			$content .= '</ul></div>';

		} else if ( $check_for_valid_product == false ) {
			$content = "<b>Please enter a valid ID to use this shortcode</b>";
		} else {
			$content = "<b>Please enter an ID to use this shortcode</b>";
		}

		return $content;
	}

} // End Class

new Woocommerce_External_Product_Embed();
