<?php
/**
 * WooCommerce External Product Embed - Admin
 *
 * Setup admin settings.
 *
 * @class 	WCEPE_Admin
 * @version 3.0.0
 * @author 	Caleb Burks
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCEPE_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_item' ) );
		add_action( 'admin_init', array( __CLASS__, 'settings_init' ) );
		add_action( 'admin_notices', array( __CLASS__, 'display_messages' ) );

		require_once 'class-wcepe-data-store.php';
	}

	/**
	 * Add Settings Menu
	 */
	public static function add_menu_item() {
		add_options_page( __( 'WooCommerce External Product Embed', 'woocommerce-external-product-embed' ),
			__( 'WooCommerce External Products', 'woocommerce-external-product-embed' ),
			'manage_options', 'embed_external_woocommerce_products', array( __CLASS__, 'wcepe_options_page' ) );
	}

	/**
	 * Add the Settings
	 */
	public static function settings_init() {
		register_setting( 'wcepe_settings_group', 'wcepe_settings' );

		do_action( 'wcepe_before_api_settings');

		// API Connect Section
		add_settings_section(
			'wcepe_api_connect_section',
			__( 'Connect to the External Store', 'woocommerce-external-product-embed' ),
			array( __CLASS__, 'api_instructions' ),'wcepe_settings_group'
		);

		// Store Home URL
		add_settings_field(
			'wcepe_store_url',
			__( 'Store Home URL', 'woocommerce-external-product-embed' ),
			array( __CLASS__, 'store_url_text_field' ), 'wcepe_settings_group', 'wcepe_api_connect_section'
		);

		// Consumer Key
		add_settings_field(
			'wcepe_consumer_key',
			__( 'Consumer Key', 'woocommerce-external-product-embed' ),
			array( __CLASS__, 'consumer_key_text_field' ), 'wcepe_settings_group', 'wcepe_api_connect_section'
		);

		// Consumer Secret
		add_settings_field(
			'wcepe_consumer_secret',
			__( 'Consumer Secret', 'woocommerce-external-product-embed' ),
			array( __CLASS__, 'consumer_secret_text_field' ), 'wcepe_settings_group', 'wcepe_api_connect_section'
		);

		do_action( 'wcepe_after_api_settings');

		// Transient Section
		add_settings_section(
			'wcepe_transient_section',
			__( 'Transient Settings', 'woocommerce-external-product-embed' ),
			array( __CLASS__, 'transient_instructions' ),'wcepe_settings_group'
		);

		// Transient Set Time
		add_settings_field(
			'wcepe_transient_time',
			__( 'Transient Set Time <br> (in seconds)', 'woocommerce-external-product-embed' ),
			array( __CLASS__, 'transients_set_time' ), 'wcepe_settings_group', 'wcepe_transient_section'
		);

		// Delete Product Transients
		add_settings_field(
			'wcepe_delete_all_transients',
			__( 'Delete Product Transients', 'woocommerce-external-product-embed' ),
			array( __CLASS__, 'delete_all_transients' ), 'wcepe_settings_group', 'wcepe_transient_section'
		);

		do_action( 'wcepe_after_transients_settings');
	}

	/**
	 * Add a link to the docs on gettings API credentials
	 */
	public static function api_instructions() {
		echo sprintf( __( 'You will need to enable the REST API on the external website, and <a href="%s" target="_blank">generate API keys</a>.',
			'woocommerce-external-product-embed' ), 'https://docs.woocommerce.com/document/woocommerce-rest-api/#section-3' );
	}

	/**
	 * Store Home URL
	 */
	public static function store_url_text_field() {
		$options = get_option( 'wcepe_settings' ); ?>
		<input type='text' class="regular-text wcepe_store_url" name='wcepe_settings[wcepe_store_url]' value='<?php echo $options['wcepe_store_url']; ?>'>
		<?php
	}

	/**
	 * Consumer Key
	 */
	public static function consumer_key_text_field() {
		$options = get_option( 'wcepe_settings' ); ?>
		<input type='text' class="regular-text wcepe_consumer_key" name='wcepe_settings[wcepe_consumer_key]' value='<?php echo $options['wcepe_consumer_key']; ?>'>
		<?php
	}

	/**
	 * Consumer Secret
	 */
	public static function consumer_secret_text_field() {
		$options = get_option( 'wcepe_settings' ); ?>
		<input type='password' class="regular-text wcepe_consumer_secret" name='wcepe_settings[wcepe_consumer_secret]' value='<?php echo $options['wcepe_consumer_secret']; ?>'>
		<?php
	}

	/**
	 * Explain how transients work
	 */
	public static function transient_instructions() {
		echo sprintf( __( '<div style="max-width: 700px"><a href="%s" target="_blank">Transients</a> help temporarily store cached information.
		 This prevents you from needing to request new data from the external site everytime the page is loaded.</span>',
			'woocommerce-external-product-embed' ), 'https://codex.wordpress.org/Transients_API' );
	}

	/**
	 * Transient Set Time
	 */
	public static function transients_set_time() {
		$options = get_option( 'wcepe_settings' ); ?>
		<input type='text' class="regular-text wcepe_transients_set_time" name='wcepe_settings[wcepe_transient_time]' placeholder="86400" value='<?php echo $options['wcepe_transient_time']; ?>'>
		<div><?php _e( 'This option defaults to 86400 seconds, equal to one day.', 'woocommerce-external-product-embed' ) ?></div>
		<?php
	}

	/**
	 * Delete All Transients
	 */
	public static function delete_all_transients() {
		$options = get_option( 'wcepe_settings' );
		$delete_url = wp_nonce_url( admin_url( 'options-general.php?page=embed_external_woocommerce_products&action=clear_transients' ), 'wcepe_clear_transients' ); ?>
		<a href="<?php echo $delete_url; ?>" class="button"><?php echo __( 'Clear Transients', 'wcepe' ); ?></a>
		<div><?php _e( 'This will force all products to be updated, even if the "set time" has not run out.', 'woocommerce-external-product-embed' ) ?></div>
		<?php

		if ( ! empty( $_GET['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'wcepe_clear_transients' ) ) {
			if ( $_GET['action'] === 'clear_transients' ) {
				$data_store = new WCEPE_Data_Store();
				$data_store->delete_transients();
			}
		}
	}

	/**
	 * Display messages when transients are cleared.
	 */
	public static function display_messages() {
		if ( ! empty( $_GET['action']) && $_GET['action'] == 'clear_transients' ) {
			$data_store = new WCEPE_Data_Store();
			$number = $data_store->delete_transients();

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
	public static function wcepe_options_page() {
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

new WCEPE_Admin();
