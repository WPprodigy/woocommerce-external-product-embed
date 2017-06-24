<?php
/**
 * WooCommerce External Product Embed Admin
 *
 * Configure Admin Settings
 *
 * @class 	Woocommerce_External_Product_Embed_Admin
 * @version 1.0
 * @author 	Caleb Burks
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Woocommerce_External_Product_Embed_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_notices', array( $this, 'display_messages' ) );
	}

	/**
	 * Add Settings Menu
	 */
	public function add_menu_item() {
		add_options_page( __( 'WooCommerce External Product Embed', 'woocommerce-external-product-embed' ),
			__( 'WooCommerce External Products', 'woocommerce-external-product-embed' ),
			'manage_options', 'embed_external_woocommerce_products', array( $this, 'wcepe_options_page' ) );
	}

	/**
	 * Add the Settings
	 */
	public function settings_init() {
		register_setting( 'wcepe_settings_group', 'wcepe_settings' );

		do_action( 'wcepe_before_api_settings');

		// API Connect Section
		add_settings_section(
			'wcepe_api_connect_section',
			__( 'Connect to the External Store', 'woocommerce-external-product-embed' ),
			array( $this, 'api_instructions' ),'wcepe_settings_group'
		);

		// Store Home URL
		add_settings_field(
			'wcepe_store_url',
			__( 'Store Home URL', 'woocommerce-external-product-embed' ),
			array( $this, 'store_url_text_field' ), 'wcepe_settings_group', 'wcepe_api_connect_section'
		);

		// Consumer Key
		add_settings_field(
			'wcepe_consumer_key',
			__( 'Consumer Key', 'woocommerce-external-product-embed' ),
			array( $this, 'consumer_key_text_field' ), 'wcepe_settings_group', 'wcepe_api_connect_section'
		);

		// Consumer Secret
		add_settings_field(
			'wcepe_consumer_secret',
			__( 'Consumer Secret', 'woocommerce-external-product-embed' ),
			array( $this, 'consumer_secret_text_field' ), 'wcepe_settings_group', 'wcepe_api_connect_section'
		);

		do_action( 'wcepe_after_api_settings');

		// Transient Section
		add_settings_section(
			'wcepe_transient_section',
			__( 'Transient Settings', 'woocommerce-external-product-embed' ),
			array( $this, 'transient_instructions' ),'wcepe_settings_group'
		);

		// Transient Set Time
		add_settings_field(
			'wcepe_transient_time',
			__( 'Transient Set Time <br> (in seconds)', 'woocommerce-external-product-embed' ),
			array( $this, 'transients_set_time' ), 'wcepe_settings_group', 'wcepe_transient_section'
		);

		// Delete Product Transients
		add_settings_field(
			'wcepe_delete_all_transients',
			__( 'Delete Product Transients', 'woocommerce-external-product-embed' ),
			array( $this, 'delete_all_transients' ), 'wcepe_settings_group', 'wcepe_transient_section'
		);

		do_action( 'wcepe_after_transients_settings');
	}

	/**
	 * Add a link to the docs on gettings API credentials
	 */
	public function api_instructions() {
		echo sprintf( __( 'Instructions on how to get this information: <a href="%s" target="_blank">How to generate API keys</a>.',
			'woocommerce-external-product-embed' ), 'http://docs.woothemes.com/document/woocommerce-rest-api/' );
	}

	/**
	 * Store Home URL
	 */
	public function store_url_text_field() {
		$options = get_option( 'wcepe_settings' ); ?>
		<input type='text' class="regular-text wcepe_store_url" name='wcepe_settings[wcepe_store_url]' value='<?php echo $options['wcepe_store_url']; ?>'>
		<?php
	}

	/**
	 * Consumer Key
	 */
	public function consumer_key_text_field() {
		$options = get_option( 'wcepe_settings' ); ?>
		<input type='text' class="regular-text wcepe_consumer_key" name='wcepe_settings[wcepe_consumer_key]' value='<?php echo $options['wcepe_consumer_key']; ?>'>
		<?php
	}

	/**
	 * Consumer Secret
	 */
	public function consumer_secret_text_field() {
		$options = get_option( 'wcepe_settings' ); ?>
		<input type='text' class="regular-text wcepe_consumer_secret" name='wcepe_settings[wcepe_consumer_secret]' value='<?php echo $options['wcepe_consumer_secret']; ?>'>
		<?php
	}

	/**
	 * Explain how transients work
	 */
	public function transient_instructions() {
		echo sprintf( __( '<a href="%s" target="_blank">What are transients?</a> By default, this option is set to 86400 seconds, which is equal to one day.',
			'woocommerce-external-product-embed' ), 'https://codex.wordpress.org/Transients_API' );
	}

	/**
	 * Transient Set Time
	 */
	public function transients_set_time() {
		$options = get_option( 'wcepe_settings' ); ?>
		<input type='text' class="regular-text wcepe_transients_set_time" name='wcepe_settings[wcepe_transient_time]' placeholder="86400" value='<?php echo $options['wcepe_transient_time']; ?>'>
		<?php
	}

	/**
	 * Delete All Transients
	 */
	public function delete_all_transients() {
		$options = get_option( 'wcepe_settings' );
		$delete_url = wp_nonce_url( admin_url( 'options-general.php?page=embed_external_woocommerce_products&action=clear_transients' ), 'wcepe_clear_transients' ); ?>
		<a href="<?php echo $delete_url; ?>" class="button"><?php echo __( 'Clear Transients', 'wcepe' ); ?></a>
		<?php

		if ( ! empty( $_GET['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'wcepe_clear_transients' ) ) {
			if ( $_GET['action'] === 'clear_transients' ) {
				$number = $this->delete_external_product_transients();
			}
		}
	}

	/**
	 * Look for and delete transients
	 */
	private function delete_external_product_transients() {
		global $wpdb;

		$transient_search = "SELECT `option_name` AS `name`, `option_value` AS `value`
		FROM  $wpdb->options
		WHERE `option_name` LIKE '%transient_wcepe_external_product_%'
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

	/**
	 * Display messages when transients are cleared.
	 */
	public function display_messages() {
		if ( ! empty( $_GET['action']) && $_GET['action'] == 'clear_transients' ) {
			$number = $this->delete_external_product_transients();

			if ( $number == 1 ) {
				echo "<div class='updated'><p>" . $number . __( ' product transient was removed.', 'woocommerce-external-product-embed' ) . "</p></div>";
			} elseif ( $number > 1 ) {
				echo "<div class='updated'><p>" . $number . __( ' products transients were removed.', 'woocommerce-external-product-embed' ) . "</p></div>";
			} else {
				echo "<div class='error'><p>" . __( 'There are currently no product transients.', 'woocommerce-external-product-embed' ) . "</p></div>";
			}
		}
	}

	/**
	 * Create the settings page.
	 */
	public function wcepe_options_page() {
		?>
		<div class="wrap">
			<h1><?php echo __( 'WooCommerce External Product Embed', 'woocommerce-external-product-embed' ); ?></h1>
			<form action='options.php' method='post'>
				<?php
				settings_fields( 'wcepe_settings_group' );
				do_settings_sections( 'wcepe_settings_group' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

} // End Class

new Woocommerce_External_Product_Embed_Admin();
