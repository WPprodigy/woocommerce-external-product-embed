<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Woocommerce_External_Product_Embed_Admin {

	public function __construct () {
		add_action( 'admin_menu', array( $this, 'wcepe_add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'wcepe_settings_init' ) );
		add_action( 'admin_notices', array( $this, 'display_success_message' ) );
	}

	public function wcepe_add_admin_menu(  ) { 
		add_options_page( 'WooCommerce External Product Embed', 'WooCommerce External Products', 'manage_options', 'embed_external_woocommerce_products', array( $this, 'wcepe_options_page' ) );
	}

	function wcepe_settings_init(  ) { 
		register_setting( 'pluginPage', 'wcepe_settings' );

		add_settings_section(
			'wcepe_pluginPage_section', 
			__( 'Connect to External Store', 'wordpress' ), 
			array( $this, 'wcepe_settings_section_callback' ),
			'pluginPage'
		);

		add_settings_field( 
			'wcepe_text_field_0',
			__( 'Consumer Key', 'wordpress' ),
			array( $this, 'wcepe_text_field_0_render' ),
			'pluginPage',
			'wcepe_pluginPage_section'
		);

		add_settings_field( 
			'wcepe_text_field_1', 
			__( 'Consumer Secret', 'wordpress' ), 
			array( $this, 'wcepe_text_field_1_render' ),
			'pluginPage', 
			'wcepe_pluginPage_section' 
		);

		add_settings_field( 
			'wcepe_text_field_2', 
			__( 'Store Home URL', 'wordpress' ), 
			array( $this, 'wcepe_text_field_2_render' ),
			'pluginPage', 
			'wcepe_pluginPage_section' 
		);

		add_settings_field( 
			'wcepe_text_field_3', 
			__( 'Transient Set Time <br> (in seconds)', 'wordpress' ), 
			array( $this, 'wcepe_text_field_3_render' ),
			'pluginPage', 
			'wcepe_pluginPage_section' 
		);

		add_settings_field( 
			'wcepe_text_field_4', 
			__( 'Delete Active Transients', 'wordpress' ), 
			array( $this, 'wcepe_text_field_4_render' ),
			'pluginPage', 
			'wcepe_pluginPage_section' 
		);

		add_settings_field( 
			'wcepe_text_field_5', 
			__( 'Delete Expired Transients', 'wordpress' ), 
			array( $this, 'wcepe_text_field_5_render' ),
			'pluginPage', 
			'wcepe_pluginPage_section' 
		);
	}

	function wcepe_text_field_0_render(  ) { 
		$options = get_option( 'wcepe_settings' );
		?>
		<input type='text' class="regular-text" name='wcepe_settings[wcepe_text_field_0]' value='<?php echo $options['wcepe_text_field_0']; ?>'>
		<?php
	}

	function wcepe_text_field_1_render(  ) { 
		$options = get_option( 'wcepe_settings' );
		?>
		<input type='text' class="regular-text" name='wcepe_settings[wcepe_text_field_1]' value='<?php echo $options['wcepe_text_field_1']; ?>'>
		<?php
	}

	function wcepe_text_field_2_render(  ) { 
		$options = get_option( 'wcepe_settings' );
		?>
		<input type='text' class="regular-text" name='wcepe_settings[wcepe_text_field_2]' value='<?php echo $options['wcepe_text_field_2']; ?>'>
		<?php
	}

	function wcepe_text_field_3_render(  ) { 
		$options = get_option( 'wcepe_settings' );
		?>
		<input type='number' name='wcepe_settings[wcepe_text_field_3]' value='<?php echo $options['wcepe_text_field_3']; ?>'>
		<?php
	}

	function wcepe_text_field_4_render(  ) { 
		$options = get_option( 'wcepe_settings' );
		?>
		<a href="<?php echo admin_url( 'options-general.php?page=embed_external_woocommerce_products&amp;action=clear_transients' ); ?>" class="button">Clear Transients</a>
		<?php

		if ( ! empty( $_GET['action'] ) ) {
			if ($_GET['action'] == 'clear_transients') {

				$this->delete_external_product_transients( );

				//echo '<div class="updated"><p>Transients Cleared</p></div>';
			}
		}
	}

	function delete_external_product_transients( ) {
		global $wpdb;
	    $sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
				FROM  $wpdb->options
				WHERE `option_name` LIKE '%transient_wcepe_external_product_%'
				ORDER BY `option_name`";

	    $results = $wpdb->get_results( $sql );

	    $prefix = '_transient_';

	    $transients_to_clear = array();

	    if ( ! empty( $results ) ) {
		    foreach ( $results as $result ) {
		    	$transients_to_clear[] = $result->name;
		    }

		    foreach( $transients_to_clear as $transient ) {
				if (substr($transient, 0, strlen($prefix)) == $prefix) {
		    		$transient = substr($transient, strlen($prefix));

		    		delete_transient( $transient );
				} 
			}
		}
	}

	function wcepe_text_field_5_render(  ) { 
		$options = get_option( 'wcepe_settings' );
		?>
		<a href="<?php echo admin_url( 'options-general.php?page=embed_external_woocommerce_products&amp;action=clear_expired_transients' ); ?>" class="button">Clear Transients</a>
		<?php

		if ( ! empty( $_GET['action'] ) ) {
			if ($_GET['action'] == 'clear_expired_transients') {

				$this->delete_external_expired_product_transients();

				//echo '<div class="updated"><p>Expired Transients Cleared</p></div>';
			}
		}
	}

	function delete_external_expired_product_transients( ) {
		global $wpdb;
        $sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
			    FROM  $wpdb->options
			    WHERE `option_name` LIKE '%transient_timeout_wcepe_external_product_%'
			    ORDER BY `option_name`";

	    $results = $wpdb->get_results( $sql );
	    $prefix = '_transient_timeout_';
	    $transients_to_clear = array();

	    if ( ! empty( $results ) ) {
		    foreach ( $results as $result ) {
		    	if ( $result->value < time() ) {
		    		$transients_to_clear[] = $result->name;
		    	}
		    }

		    foreach( $transients_to_clear as $transient ) {
				if (substr($transient, 0, strlen($prefix)) == $prefix) {
		    		$transient = substr($transient, strlen($prefix));

		    		delete_transient( $transient );
				} 
			}
		}
	}

	function display_success_message() {
		if ( ! empty( $_GET['action'] && $_GET['action'] == 'clear_transients' ) ) {
			echo '<div class="updated"><p>Transients Cleared</p></div>';
		} else if ( ! empty( $_GET['action'] && $_GET['action'] == 'clear_expired_transients' ) ) {
			echo '<div class="updated"><p>Expired Transients Cleared</p></div>';
		}
	}

	function wcepe_settings_section_callback(  ) { 
		//echo $this->display_success_message();
		echo __( 'You can find instructions here:  <a href="http://docs.woothemes.com/document/woocommerce-rest-api/" target="_blank">Generating API keys</a>', 'wordpress' );
	}

	function wcepe_options_page(  ) { 
		?>
		<form action='options.php' method='post'>
			
			<h2>WooCommerce External Product Embed</h2>
			
			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>
			
		</form>
		<?php
	}

} // End Class

new Woocommerce_External_Product_Embed_Admin();
