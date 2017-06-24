<?php
/**
 * WooCommerce External Product Embed
 *
 * Set up shortcodes.
 *
 * @class 	Woocommerce_External_Product_Embed
 * @version 1.0
 * @author 	Caleb Burks
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Woocommerce_External_Product_Embed {

	public function __construct() {
		// Get product information and set the transients
		require_once 'class-woocommerce-external-product-embed-transients.php';

		// Enqueue CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'register_css' ) );

		// Load Text Domain
		add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );

		// Register Shortcodes
		add_shortcode( 'external_product', array( $this, 'external_product_shortcode' ) );
		add_shortcode( 'recent_external_products', array( $this, 'recent_external_products_shortcode' ) );

		require_once 'class-wcepe-shortcodes.php';
		add_action( 'init', array( 'WCEPE_Shortcodes', 'init' ) );
	}

	/**
	 * Load Text Domain
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'woocommerce-external-product-embed', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}


	/**
	 * Register the stylesheet
	 */
	public function register_css() {
		wp_register_style( 'wcepe-styles', plugins_url( 'assets/styles.css', dirname( plugin_basename( __FILE__ ) ) ) );
	}


	/**
	 * Get the Template File
	 */
	public function get_template_file() {
		// Check if template has been overriden
		if ( file_exists( get_stylesheet_directory() . '/woocommerce-external-product-embed/shortcodes/external-product-single.php' ) ) {
			return get_stylesheet_directory() . '/woocommerce-external-product-embed/shortcodes/external-product-single.php';
		} else {
			return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/shortcodes/external-product-single.php';
		}
	}

	/**
	 * Create the Shortcode
	 */
	public function external_product_shortcode( $atts ) {
		$args = (array)$atts;

		$defaults = array(
			'id'     => '',
			'image'  => 'show',
			'title'  => 'show',
			'price'  => 'show',
			'rating' => 'show',
			'button' => 'View Product'
		);

		$filtered_defaults = apply_filters( 'wcepe_external_product_shortcode', $defaults );
		$args = shortcode_atts( $filtered_defaults, $atts );

		// Make the defaults usuable in the templates
		extract( $args );

		// Add styles when the shortcode is used on page
		wp_enqueue_style( 'wcepe-styles' );
		wp_enqueue_style( 'dashicons' );

		// Returns false if there are no IDs
		if ( $id ) {
			$ids = explode( ',', $id );

			$opening_html = '<div class="wcepe_external_product_wrap"><ul class="wcepe_external_products">';
			$content  = apply_filters( 'wcepe_opening_html',  $opening_html );

			foreach ( $ids as $id ) {
				$product_id = trim($id);
				$transients = new Woocommerce_External_Product_Embed_Transients( $product_id );
				$product    = $transients->get_all_product_data();

				// Returns false if the product does not exist
				if ( $product ) {
					ob_start();
					include( $this->get_template_file() );
					$content .= ob_get_clean();
				}
			} // end foreach

			$ending_html = '</ul></div>';
			$content  .= apply_filters( 'wcepe_ending_html',  $ending_html );

			return $content;
		} // end ID check
	}

	/**
	 * Create the Shortcode
	 */
	public function recent_external_products_shortcode( $atts ) {
		$args = (array)$atts;

		$defaults = array(
			'number' => '4',
			'image'  => 'show',
			'title'  => 'show',
			'price'  => 'show',
			'rating' => 'show',
			'button' => 'View Product'
		);

		$filtered_defaults = apply_filters( 'wcepe_recent_external_products_shortcode', $defaults );
		$args = shortcode_atts( $filtered_defaults, $atts );

		// Make the defaults usuable in the tempaltes
		extract($args);

		// Add styles when the shortcode is used on page
		wp_enqueue_style( 'wcepe-styles' );
		wp_enqueue_style( 'dashicons' );

		$recent_products = new Woocommerce_External_Product_Embed_Transients();
		$ids = $recent_products->recent_product_ids( $number );

		$opening_html = '<div class="wcepe_external_product_wrap"><ul class="wcepe_external_products">';
		$content  = apply_filters( 'wcepe_opening_html',  $opening_html );

		foreach ( $ids as $id ) {
			$product_id = trim($id);
			$transients = new Woocommerce_External_Product_Embed_Transients( $product_id );
			$product    = $transients->get_all_product_data();

			// Returns false if the product does not exist
			if ( $product ) {
				ob_start();
				include( $this->get_template_file() );
				$content .= ob_get_clean();
			}
		} // end foreach

		$ending_html = '</ul></div>';
		$content  .= apply_filters( 'wcepe_ending_html',  $ending_html );

		return $content;
	}

} // End Class

new Woocommerce_External_Product_Embed();
