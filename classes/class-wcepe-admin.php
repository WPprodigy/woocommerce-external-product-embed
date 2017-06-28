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

		// Prepare admin messages once certain tasks are done.
		add_action( 'wcepe_before_settings_form', array( __CLASS__, 'test_api_connection' ) );
		add_action( 'wcepe_before_settings_form', array( __CLASS__, 'delete_all_transients' ) );

		// Data Store - used for deleting transients
		require_once 'class-wcepe-data-store.php';

		// API Client Helper
		require_once 'class-wcepe-api-client.php';
	}

	/**
	 * Add Settings Menu
	 */
	public static function add_menu_item() {
		add_options_page( __( 'WooCommerce External Product Embed', 'woocommerce-external-product-embed' ),
			__( 'WooCommerce External Products', 'woocommerce-external-product-embed' ),
			'manage_options', 'wc_external_product_embed', array( __CLASS__, 'wcepe_options_page' ) );
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
			array( __CLASS__, 'delete_product_transients_button' ), 'wcepe_settings_group', 'wcepe_transient_section'
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
		<div><?php _e( 'This option defaults to 86400 seconds, equal to one day. Setting to 0 will disable transients from being created (not recommended).', 'woocommerce-external-product-embed' ) ?></div>
		<?php
	}

	/**
	 * Delete Product Transients
	 */
	public static function delete_product_transients_button() {
		$options = get_option( 'wcepe_settings' );
		$delete_url = wp_nonce_url( admin_url( 'options-general.php?page=wc_external_product_embed&action=clear_transients' ), 'wcepe_clear_transients' ); ?>
		<a href="<?php echo $delete_url; ?>" class="button"><?php echo __( 'Clear Transients', 'wcepe' ); ?></a>
		<div><?php _e( 'This will force all products to be updated, even if the "set time" has not run out.', 'woocommerce-external-product-embed' ) ?></div>
		<?php
	}

	/**
	 * Run the transient deletion process.
	 */
	public static function delete_all_transients() {
		if ( ! empty( $_GET['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'wcepe_clear_transients' ) ) {
			if ( $_GET['action'] === 'clear_transients' ) {
				$data_store = new WCEPE_Data_Store();
				$count = $data_store->delete_transients();

				if ( $count == 0 ) {
					$message = __( 'There are currently no product transients.', 'woocommerce-external-product-embed' );
				} else {
					$message = sprintf( _n( '%s product transient was removed.', '%s product transients were removed.', $count, 'woocommerce-external-product-embed' ), $count );
				}

				return self::display_admin_messages( $message );
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

			<?php do_action( 'wcepe_before_settings_form' ) ?>

			<form action='options.php' method='post'>
				<?php
				settings_fields( 'wcepe_settings_group' );
				do_settings_sections( 'wcepe_settings_group' );
				submit_button();
				?>
			</form>

			<?php do_action( 'wcepe_after_settings_form' ) ?>
		</div>
		<?php
	}

	/**
	 * Test the API connection.
	 */
	public static function test_api_connection() {
		if ( ! empty( $_GET['settings-updated']) && $_GET['settings-updated'] == 'true' ) {
			$options  = get_option( 'wcepe_settings' );
			$required = array(
				'Store Home URL' => 'wcepe_store_url',
				'Consumer Key' => 'wcepe_consumer_key',
				'Consumer Secret' => 'wcepe_consumer_secret',
			);

			// Ensure all necessary fields have been saved.
			$messages = array();
			foreach ( $required as $setting_name => $setting_value ) {
				if ( empty( $options[ $setting_value ] ) ) {
					$messages[] = $setting_name . ' is required.';
				}
			}

			// Return if there are missing fields.
			if ( ! empty( $messages ) ) {
				return self::display_admin_messages( $messages, 'error' );
			}

			$wc_api = new WCEPE_API_Client();
			$result = $wc_api->test_connection();

			if ( 'success' === $result ) {
				$result = __( 'Successfully connected to external store!', 'woocommerce-external-product-embed' );
				return self::display_admin_messages( $result );
			}

			if ( 'Syntax error' === $result || 'cURL Error' === substr( $result, 0, 10 ) ) {
				$result = __( 'API Connection Error. Check the Store URL and ensure the REST API is enabled on the external site.', 'woocommerce-external-product-embed' );
			} else if ( '[woocommerce_rest_cannot_view]' === substr( $result, -30 ) ) {
				$result = __( 'API Connection Error. Check the Consumer Key.', 'woocommerce-external-product-embed' );
			} else if ( '[woocommerce_rest_authentication_error]' === substr( $result, -39 ) ) {
				$result = __( 'API Connection Error. Check the Consumer Secret.', 'woocommerce-external-product-embed' );
			}

			return self::display_admin_messages( $result, 'error' );
		}
	}

	/**
	 * Display Admin Messages.
	 */
	public static function display_admin_messages( $messages, $type = 'updated' ) {
		if ( is_array( $messages ) ) {
			foreach ( $messages as $message ) {
				echo "<div class='" . $type . " notice is-dismissible'><p>" . $message . "</p></div>";
			}
		} else {
			echo "<div class='" . $type . " notice is-dismissible'><p>" . $messages . "</p></div>";
		}
	}

} // End Class

new WCEPE_Admin();
