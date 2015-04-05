<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Woocommerce_External_Product_Embed {

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		require_once 'class-woocommerce-external-product-embed-admin.php';
        wp_register_style( 'wcepe_styles', plugins_url('../assets/styles.css', __FILE__) );
		wp_enqueue_style( 'wcepe_styles' );
		add_shortcode( 'external_product', array( $this, 'wcepe_external_product_shortcode' ) );
	} // End __construct()


	/**
     * Get API Connection info
     * @access  private
     * @since   1.0.0
     */
	private function wcepe_store_api_info() {
		require_once 'class-wc-api-client.php';
	
		// require_once 'classes/class-wc-external-product-embed-admin.php';

		$options = get_option( 'wcepe_settings' );

		$consumer_key    = $options['wcepe_text_field_0'];
		$consumer_secret = $options['wcepe_text_field_1'];
		$store_url       = $options['wcepe_text_field_2'];

		$wc_api = new WC_API_Client( $consumer_key, $consumer_secret, $store_url );

		return $wc_api;
	}

	/**
     * Get transient's set time
     * @access  public
     * @since   1.0.0
     */
	public function wcepe_get_transient_time() {
		$options = get_option( 'wcepe_settings' );
		$time = $options['wcepe_text_field_3'];

		if ($time == '') {
			$time = '86400';
		}

		return $time;
	}


	/* Get and Return Link */

	private function wcepe_get_external_product_link( $external_product_id ) {
		$wc_api = $this->wcepe_store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$link  = $external->product->permalink;

		return $link;
	}

	private function wcepe_set_external_product_link_transient( $external_product_id ) {

		if ( false === ( $value = get_transient( 'wcepe_external_product_link_' . $external_product_id ) ) ) {
	    	set_transient( 'wcepe_external_product_link_' . $external_product_id, 
	    	$this->wcepe_get_external_product_link( $external_product_id ), $this->wcepe_get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_link_' . $external_product_id );
	}


	/* Get and Display Image */

	private function wcepe_get_external_product_image( $external_product_id ) {
		$wc_api = $this->wcepe_store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$image = $external->product->images[0]->src;

		return $image;
	}

	private function wcepe_set_external_product_image_transient( $external_product_id ) {

		if ( false === ( $value = get_transient( 'wcepe_external_product_image_' . $external_product_id ) ) &&  $this->wcepe_get_external_product_image( $external_product_id ) != '' ) {
	    	set_transient( 'wcepe_external_product_image_' . $external_product_id, 
	    	$this->wcepe_get_external_product_image( $external_product_id ), $this->wcepe_get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_image_' . $external_product_id );
	}

	private function wcepe_display_external_product_image( $show_image, $external_product_id ) {

		if ( $show_image == 'show' &&  $this->wcepe_set_external_product_image_transient( $external_product_id ) != '' ) {
			return '<a href=' . $this->wcepe_set_external_product_link_transient( $external_product_id ) . ' target="_blank"><img class="external_product_image" src=' . $this->wcepe_set_external_product_image_transient( $external_product_id ) . ' ></a>';
		} 
	}


	/* Get and Display Title */

	private function wcepe_get_external_product_title( $external_product_id ) {
		$wc_api = $this->wcepe_store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$title = $external->product->title;

		return $title;
	}

	private function wcepe_set_external_product_title_transient( $external_product_id ) {

		if ( false === ( $value = get_transient( 'wcepe_external_product_title_' . $external_product_id ) ) &&  $this->wcepe_get_external_product_title( $external_product_id ) != '' ) {
	    	set_transient( 'wcepe_external_product_title_' . $external_product_id, 
	    	$this->wcepe_get_external_product_title( $external_product_id ), $this->wcepe_get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_title_' . $external_product_id );
	}

	private function wcepe_display_external_product_title( $show_title, $external_product_id ) {

		if ( $show_title == 'show' &&  $this->wcepe_set_external_product_title_transient( $external_product_id ) != '' ) {
			return '<h3 class="external_product_title"><a href=' . $this->wcepe_set_external_product_link_transient( $external_product_id ) . ' target="_blank">' . $this->wcepe_set_external_product_title_transient( $external_product_id ) . '</a></h3>';
		} 
	}


	/* Get and Display Price */

	private function wcepe_get_external_product_price( $external_product_id ) {
		$wc_api = $this->wcepe_store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$price = $external->product->price_html;

		return $price;
	}

	private function wcepe_set_external_product_price_transient( $external_product_id ) {

		if ( false === ( $value = get_transient( 'wcepe_external_product_price_' . $external_product_id ) ) &&  $this->wcepe_get_external_product_price( $external_product_id ) != '' ) {
	    	set_transient( 'wcepe_external_product_price_' . $external_product_id, 
	    	$this->wcepe_get_external_product_price( $external_product_id ), $this->wcepe_get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_price_' . $external_product_id );
	}

	private function wcepe_display_external_product_price( $show_price, $external_product_id ) {

		if ( $show_price == 'show' &&  $this->wcepe_set_external_product_price_transient( $external_product_id ) != '' ) {
			return '<span class="external_product_price">' . $this->wcepe_set_external_product_price_transient( $external_product_id ) . '</span>';
		} 
	}


	/* Get and Display Rating */

	private function wcepe_get_external_product_rating( $external_product_id ) {
		$wc_api = $this->wcepe_store_api_info();
		$external = $wc_api->get_product( $external_product_id );
		$rating = $external->product->average_rating;

		return $rating;
	}

	private function wcepe_set_external_product_rating_transient( $external_product_id ) {

		if ( false === ( $value = get_transient( 'wcepe_external_product_rating_' . $external_product_id ) ) &&  $this->wcepe_get_external_product_rating( $external_product_id ) != '' ) {
	    	set_transient( 'wcepe_external_product_rating_' . $external_product_id, 
	    	$this->wcepe_get_external_product_rating( $external_product_id ), $this->wcepe_get_transient_time() );
		}

		return get_transient( 'wcepe_external_product_rating_' . $external_product_id );
	}

	private function wcepe_display_external_product_rating( $show_rating, $external_product_id ) {

		$number = $this->wcepe_set_external_product_rating_transient( $external_product_id );

		if ( $number >= '5' ) {
			$percent = '30%'; 
		} else if ( $number >= '4' ) {
			$percent = '24%'; 
		} else if ( $number >= '3' ) {
			$percent = '18%'; 
		} else if ( $number >= '2' ) {
			$percent = '12%'; 
		} else if ( $number >= '1' ) {
			$percent = '5%'; 
		} else if ( $number <= '.9' ) {
			$percent = '0%'; 
		}

		if ( $show_rating == 'show' &&  $this->wcepe_set_external_product_rating_transient( $external_product_id ) != '' ) {
			return '<p class="external_product_rating"><span style="width:' . $percent . '" class=' . $this->wcepe_set_external_product_rating_transient( $external_product_id ) . '></span></p>';
		} 
	}


	/* Display Button */

	private function wcepe_display_external_product_button( $button_text, $external_product_id ) {

		if ( $button_text != 'hide' ) {
			return '<span class="external_product_button"><a href="' . $this->wcepe_set_external_product_link_transient( $external_product_id ) . '" class="button" target="_blank">' . $button_text . '</a></span>';
		} 
	}


	/* Create Shortcode */

	public function wcepe_external_product_shortcode( $atts ) {

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

		$content  = '<div class="external_product">';
		$content .= $this->wcepe_display_external_product_image( $image, $id );
		$content .= $this->wcepe_display_external_product_title( $title, $id );
		$content .= $this->wcepe_display_external_product_rating( $rating, $id );
		$content .= $this->wcepe_display_external_product_price( $price, $id );
		$content .= $this->wcepe_display_external_product_button( $button, $id );
		$content .= '</div>';

		if ($id == '') {
			$content = "Please enter an ID";
		}

		return $content;

	}


} // End Class
if (class_exists("Woocommerce_External_Product_Embed")) {
	$Woocommerce_External_Product_Embed = new Woocommerce_External_Product_Embed();
}