<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Woocommerce_External_Product_Embed {

	public function __construct () {
		require_once 'class-woocommerce-external-product-embed-admin.php';

        wp_register_style( 'wcepe_styles', plugins_url('../assets/styles.css', __FILE__) );
		wp_enqueue_style( 'wcepe_styles' );

		wp_enqueue_style( 'dashicons' );

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

	/* Get and Return Link */

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

	/* Get and Display Image */

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

	private function display_external_product_image( $show_image, $external_product_id ) {
		if ( $show_image == 'show' &&  $this->set_external_product_image_transient( $external_product_id ) != '' ) {
			return '<a href=' . $this->set_external_product_link_transient( $external_product_id ) . ' target="_blank"><img class="wcepe_external_product_image" src=' . $this->set_external_product_image_transient( $external_product_id ) . ' ></a>';
		} 
	}

	/* Get and Display Title */

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

	private function display_external_product_title( $show_title, $external_product_id ) {
		if ( $show_title == 'show' &&  $this->set_external_product_title_transient( $external_product_id ) != '' ) {
			return '<h3 class="wcepe_external_product_title"><a href=' . $this->set_external_product_link_transient( $external_product_id ) . ' target="_blank">' . $this->set_external_product_title_transient( $external_product_id ) . '</a></h3>';
		} 
	}

	/* Get and Display Price */

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

	private function display_external_product_price( $show_price, $external_product_id ) {
		if ( $show_price == 'show' &&  $this->set_external_product_price_transient( $external_product_id ) != '' ) {
			return '<span class="wcepe_external_product_price">' . $this->set_external_product_price_transient( $external_product_id ) . '</span>';
		} 
	}

	/* Get and Display Rating */

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

	private function return_rating_percent( $external_product_id ) {
		$number = $this->set_external_product_rating_transient( $external_product_id );

		if ( $number >= '5' ) {
			$percent = '99px'; 
		} else if ( $number >= '4' ) {
			$percent = '77px'; 
		} else if ( $number >= '3' ) {
			$percent = '58px'; 
		} else if ( $number >= '2' ) {
			$percent = '38px'; 
		} else if ( $number >= '1' ) {
			$percent = '20px'; 
		} else if ( $number <= '.9' ) {
			$percent = '0'; 
		}

		return $percent;
	}

	private function display_external_product_rating( $show_rating, $external_product_id ) {
		$percent = $this->return_rating_percent( $external_product_id ); 
		
		if ( $show_rating == 'show' &&  $this->set_external_product_rating_transient( $external_product_id ) != '' ) {
			return '<p class="wcepe_external_product_rating"><span style="width:' . $percent . '"></span></p>';
		}
	}

	/* Display Button */

	private function display_external_product_button( $button_text, $external_product_id ) {
		if ( $button_text != 'hide' && $this->set_external_product_link_transient( $external_product_id ) != '' ) {
			return '<span class="wcepe_external_product_button"><a href="' . $this->set_external_product_link_transient( $external_product_id ) . '" class="button" target="_blank">' . $button_text . '</a></span>';
		} 
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

		// Check to see if there is a valid ID
		$check_for_valid_product = $this->test_for_valid_id( $id );

		// Create Product Data
		if ($id != '' && $check_for_valid_product == true ) {
			$ids = explode( ',', $id );

			$content  = '<div class="wcepe_external_product_wrap"><ul class="wcepe_external_products">';

			foreach ( $ids as $id ) {
				if ( $this->set_external_product_link_transient( $id ) != '') {
					$content .= '<li class="wcepe_external_product">';
				}
				$content .= $this->display_external_product_image( $image, $id );
				$content .= $this->display_external_product_title( $title, $id );
				$content .= $this->display_external_product_rating( $rating, $id );
				$content .= $this->display_external_product_price( $price, $id );
				$content .= $this->display_external_product_button( $button, $id );
				if ( $this->set_external_product_link_transient( $id ) != '') {
					$content .= '</li>';
				}
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

$Woocommerce_External_Product_Embed = new Woocommerce_External_Product_Embed();
