<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Configuration' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Configuration {

		private $page = 'dfrapi-configuration';
		private $key;
		private $account;

		public function __construct() {
			$this->key = 'dfrapi_configuration';
			$this->account = (array) get_option( 'dfrapi_account', array( 'max_length' => 50 ) );
			add_action( 'init', array( $this, 'load_settings' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}

		function admin_menu() {
			add_submenu_page(
				'dfrapi',
				__( 'Configuration &#8212; Datafeedr API', DFRAPI_DOMAIN ),
				__( 'Configuration', DFRAPI_DOMAIN ),
				'manage_options',
				'dfrapi',
				array( $this, 'output' )
			);
		}

		function admin_notice() {
			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && isset( $_GET['page'] ) && 'dfrapi' == $_GET['page'] ) {
				echo '<div class="updated"><p>';
				_e( 'Configuration successfully updated!', DFRAPI_DOMAIN );
				echo '</p></div>';
			}
		}

		function output() {
			echo '<div class="wrap" id="' . $this->key . '">';
			echo '<h2>' . dfrapi_setting_pages( $this->page ) . ' &#8212; Datafeedr API</h2>';
			echo '<form method="post" action="options.php">';
			wp_nonce_field( 'update-options' );
			settings_fields( $this->page );
			do_settings_sections( $this->page);
			submit_button();
			echo '</form>';
			echo '</div>';
		}

		function load_settings() {
			$this->options = (array) get_option( $this->key );
			$this->options = array_merge(
				array(
					'access_id'                => '',
					'secret_key'               => '',
					'transport_method'         => 'curl',
					'disable_api'              => 'no',
					'zanox_connection_key'     => '',
					'zanox_secret_key'         => '',
					'amazon_access_key_id'     => '',
					'amazon_secret_access_key' => '',
					'amazon_tracking_id'       => '',
					'amazon_locale'            => 'us',
					'ph_application_key'       => '',
					'ph_user_api_key'          => '',
					'ph_publisher_id'          => '',
				),
				$this->options
			);
		}

		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			add_settings_section( 'api_settings', __( 'Datafeedr API Settings', DFRAPI_DOMAIN ), array( &$this, 'section_api_settings_desc' ), $this->page );
			add_settings_field( 'access_id', __( 'API Access ID', DFRAPI_DOMAIN ), array( &$this, 'field_access_id' ), $this->page, 'api_settings' );
			add_settings_field( 'secret_key',  __( 'API Secret Key', DFRAPI_DOMAIN ), array( &$this, 'field_secret_key' ), $this->page, 'api_settings' );
			// add_settings_field( 'transport_method',  __( 'Transport Method', DFRAPI_DOMAIN ), array( &$this, 'field_transport_method' ), $this->page, 'api_settings' );

			/*
			add_settings_field(
				'disable_api',
				__( 'Disable API', DFRAPI_DOMAIN ),
				array( &$this, 'field_disable_api' ),
				$this->page,
				'api_settings'
			);
			*/

			add_settings_section( 'zanox_api_settings', __( 'Zanox Settings', DFRAPI_DOMAIN ), array( &$this, 'section_zanox_api_settings_desc' ), $this->page );
			add_settings_field( 'zanox_connection_key', __( 'Connection Key', DFRAPI_DOMAIN ), array( &$this, 'field_zanox_connection_key' ), $this->page, 'zanox_api_settings' );
			add_settings_field( 'zanox_secret_key',  __( 'Secret Key', DFRAPI_DOMAIN ), array( &$this, 'field_zanox_secret_key' ), $this->page, 'zanox_api_settings' );

			if ( defined( 'DFRCS_VERSION' ) ) {
				add_settings_section( 'amazon_api_settings', __( 'Amazon Settings', DFRAPI_DOMAIN ), array( &$this, 'section_amazon_api_settings_desc' ), $this->page );
				add_settings_field( 'amazon_access_key_id', __( 'Amazon Access Key ID', DFRAPI_DOMAIN ), array( &$this, 'field_amazon_access_key_id' ), $this->page, 'amazon_api_settings' );
				add_settings_field( 'amazon_secret_access_key', __( 'Amazon Secret Access Key', DFRAPI_DOMAIN ), array( &$this, 'field_amazon_secret_access_key' ), $this->page, 'amazon_api_settings' );
				add_settings_field( 'amazon_tracking_id', __( 'Amazon Tracking ID', DFRAPI_DOMAIN ), array( &$this, 'field_amazon_tracking_id' ), $this->page, 'amazon_api_settings' );
				add_settings_field( 'amazon_locale', __( 'Amazon Locale', DFRAPI_DOMAIN ), array( &$this, 'field_amazon_locale' ), $this->page, 'amazon_api_settings' );
			}

			add_settings_section( 'ph_api_settings', __( 'Performance Horizon Settings', DFRAPI_DOMAIN ), array( &$this, 'section_ph_api_settings_desc' ), $this->page );
			add_settings_field( 'ph_application_key', __( 'Application Key', DFRAPI_DOMAIN ), array( &$this, 'field_ph_application_key' ), $this->page, 'ph_api_settings' );
			add_settings_field( 'ph_user_api_key', __( 'User API Key', DFRAPI_DOMAIN ), array( &$this, 'field_ph_user_api_key' ), $this->page, 'ph_api_settings' );
			add_settings_field( 'ph_publisher_id', __( 'Publisher ID', DFRAPI_DOMAIN ), array( &$this, 'field_ph_publisher_id' ), $this->page, 'ph_api_settings' );
		}

		function section_api_settings_desc() {
			echo __( 'Add your ', DFRAPI_DOMAIN );
			echo ' <a href="'.DFRAPI_KEYS_URL.'?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiconfigpage" target="_blank" title="' . __( 'Get your Datafeedr API Keys', DFRAPI_DOMAIN ) . '">';
			echo __( 'Datafeedr API Keys', DFRAPI_DOMAIN );
			echo '</a>.';
		}

		function field_access_id() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[access_id]" value="<?php echo esc_attr( $this->options['access_id'] ); ?>" />
			<?php
		}

		function field_secret_key() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[secret_key]" value="<?php echo esc_attr( $this->options['secret_key'] ); ?>" />
			<?php
		}

		function section_ph_api_settings_desc() {
			echo __( 'If you want to use the Performance Horizon affiliate network, add your ', DFRAPI_DOMAIN );
			echo ' <a href="https://datafeedrapi.helpscoutdocs.com/article/195-how-to-find-your-performance-horizon-publisher-id-and-api-keys" target="_blank" title="' . __( 'Learn how to find your Performance Horizon Keys', DFRAPI_DOMAIN ) . '">';
			echo __( 'Performance Horizon Keys', DFRAPI_DOMAIN );
			echo '</a>.';
		}

		function field_ph_application_key() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[ph_application_key]" value="<?php echo esc_attr( $this->options['ph_application_key'] ); ?>" />
			<?php
		}

		function field_ph_user_api_key() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[ph_user_api_key]" value="<?php echo esc_attr( $this->options['ph_user_api_key'] ); ?>" />
			<?php
		}

		function field_ph_publisher_id() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[ph_publisher_id]" value="<?php echo esc_attr( $this->options['ph_publisher_id'] ); ?>" />
			<?php
		}


		function section_zanox_api_settings_desc() {
			echo __( 'If you want to use the Zanox affiliate network, add your ', DFRAPI_DOMAIN );
			echo ' <a href="http://publisher.zanox.com/ws_gettingstarted/ws.gettingstarted.html" target="_blank" title="' . __( 'Get your Zanox Keys', DFRAPI_DOMAIN ) . '">';
			echo __( 'Zanox Keys', DFRAPI_DOMAIN );
			echo '</a>.';
		}

		function field_zanox_connection_key() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[zanox_connection_key]" value="<?php echo esc_attr( $this->options['zanox_connection_key'] ); ?>" />
			<?php
		}

		function field_zanox_secret_key() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[zanox_secret_key]" value="<?php echo esc_attr( $this->options['zanox_secret_key'] ); ?>" />
			<?php
		}

		function field_transport_method() {
			?>
            <select id="transport_method" name="<?php echo $this->key; ?>[transport_method]">
                <option value="curl" <?php selected( $this->options['transport_method'], 'curl', true ); ?>><?php _e( 'CURL', DFRAPI_DOMAIN ); ?></option>
                <option value="file" <?php selected( $this->options['transport_method'], 'file', true ); ?>><?php _e( 'File', DFRAPI_DOMAIN ); ?></option>
                <option value="socket" <?php selected( $this->options['transport_method'], 'socket', true ); ?>><?php _e( 'Socket', DFRAPI_DOMAIN ); ?></option>
            </select>
            <p class="description"><?php _e( 'If you\'re not sure, use CURL.', DFRAPI_DOMAIN ); ?></p>
			<?php
		}

		function field_disable_api() {
			?>
            <p>
                <input type="radio" value="yes" name="<?php echo $this->key; ?>[disable_api]" <?php checked( $this->options['disable_api'], 'yes', true ); ?> /> <?php _e( 'Yes', DFRAPI_DOMAIN ); ?>
            </p>
            <p>
                <input type="radio" value="no" name="<?php echo $this->key; ?>[disable_api]" <?php checked( $this->options['disable_api'], 'no', true ); ?> /> <?php _e( 'No', DFRAPI_DOMAIN ); ?>
            </p>
            <p class="description"><?php _e( 'Prevent your site from attempting to make a request to the Datafeedr API.', DFRAPI_DOMAIN ); ?></p>
			<?php
		}

		function section_amazon_api_settings_desc() {
			echo __( 'Add your ', DFRAPI_DOMAIN );
			echo '<a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/your-account.html" target="_blank">';
			echo __( 'Amazon Product Advertising API keys', DFRAPI_DOMAIN );
			echo '</a>.';
			echo '<br /><span style="color:red">';
			echo __( 'These are ONLY compatible with the Datafeedr Comparison Sets plugin.', DFRAPI_DOMAIN );
			echo '</span>';
		}

		function field_amazon_access_key_id() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[amazon_access_key_id]" value="<?php echo esc_attr( $this->options['amazon_access_key_id'] ); ?>" />
			<?php
		}

		function field_amazon_secret_access_key() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[amazon_secret_access_key]" value="<?php echo esc_attr( $this->options['amazon_secret_access_key'] ); ?>" />
			<?php
		}

		function field_amazon_tracking_id() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[amazon_tracking_id]" value="<?php echo esc_attr( $this->options['amazon_tracking_id'] ); ?>" />
            <p class="description">
				<?php _e( 'This is the same as your Associates Tag usually ending in "-20" or "-21". Can be found ', DFRAPI_DOMAIN ); ?>
                <a href="https://affiliate-program.amazon.com/gp/associates/network/main.html" target="_blank"><?php _e( 'here', DFRAPI_DOMAIN ); ?></a>.
            </p>
			<?php
		}

		function field_amazon_locale() {
			?>
            <select id="amazon_locale" name="<?php echo $this->key; ?>[amazon_locale]">
                <option value="br" <?php selected( $this->options['amazon_locale'], 'br', true ); ?>><?php _e( 'Brazil', DFRAPI_DOMAIN ); ?></option>
                <option value="ca" <?php selected( $this->options['amazon_locale'], 'ca', true ); ?>><?php _e( 'Canada', DFRAPI_DOMAIN ); ?></option>
                <option value="cn" <?php selected( $this->options['amazon_locale'], 'cn', true ); ?>><?php _e( 'China', DFRAPI_DOMAIN ); ?></option>
                <option value="fr" <?php selected( $this->options['amazon_locale'], 'fr', true ); ?>><?php _e( 'France', DFRAPI_DOMAIN ); ?></option>
                <option value="de" <?php selected( $this->options['amazon_locale'], 'de', true ); ?>><?php _e( 'Germany', DFRAPI_DOMAIN ); ?></option>
                <option value="in" <?php selected( $this->options['amazon_locale'], 'in', true ); ?>><?php _e( 'India', DFRAPI_DOMAIN ); ?></option>
                <option value="it" <?php selected( $this->options['amazon_locale'], 'it', true ); ?>><?php _e( 'Italy', DFRAPI_DOMAIN ); ?></option>
                <option value="jp" <?php selected( $this->options['amazon_locale'], 'jp', true ); ?>><?php _e( 'Japan', DFRAPI_DOMAIN ); ?></option>
                <option value="mx" <?php selected( $this->options['amazon_locale'], 'mx', true ); ?>><?php _e( 'Mexico', DFRAPI_DOMAIN ); ?></option>
                <option value="es" <?php selected( $this->options['amazon_locale'], 'es', true ); ?>><?php _e( 'Spain', DFRAPI_DOMAIN ); ?></option>
                <option value="uk" <?php selected( $this->options['amazon_locale'], 'uk', true ); ?>><?php _e( 'United Kingdom', DFRAPI_DOMAIN ); ?></option>
                <option value="us" <?php selected( $this->options['amazon_locale'], 'us', true ); ?>><?php _e( 'United States', DFRAPI_DOMAIN ); ?></option>
            </select>
            <p class="description">
                <a href="http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AssociateIDs.html" target="_blank"><?php _e( 'More information', DFRAPI_DOMAIN ); ?></a> <?php _e( 'regarding Amazon Locales.', DFRAPI_DOMAIN ); ?>
            </p>
			<?php
		}

		function validate( $input ) {

			if ( !isset( $input ) || !is_array( $input ) || empty( $input ) ) { return $input; }

			$new_input = array();

			foreach( $input as $key => $value ) {

				// Validate "access_id"
				if ( $key == 'access_id' ) {
					$new_input['access_id'] = trim( $value );
				}

				// Validate "secret_key"
				if ( $key == 'secret_key' ) {
					$new_input['secret_key'] = trim( $value );
				}

				// Validate "transport_method"
				if ( $key == 'transport_method' ) {
					$new_input['transport_method'] = trim( $value );
				}

				// Validate "disable_api"
				if ( $key == 'disable_api' ) {
					$new_input['disable_api'] = trim( $value );
				}

				// Validate "zanox_connection_key"
				if ( $key == 'zanox_connection_key' ) {
					$new_input['zanox_connection_key'] = trim( $value );
				}

				// Validate "zanox_secret_key"
				if ( $key == 'zanox_secret_key' ) {
					$new_input['zanox_secret_key'] = trim( $value );
				}

				// Validate Amazon Access Key ID
				if ( $key == 'amazon_access_key_id' ) {
					$new_input['amazon_access_key_id'] = trim( $value );
				}

				// Validate Amazon Secret Access Key
				if ( $key == 'amazon_secret_access_key' ) {
					$new_input['amazon_secret_access_key'] = trim( $value );
				}

				// Validate Amazon Tracking ID
				if ( $key == 'amazon_tracking_id' ) {
					$new_input['amazon_tracking_id'] = trim( $value );
				}

				// Validate Amazon Locale
				if ( $key == 'amazon_locale' ) {
					$new_input['amazon_locale'] = trim( $value );
				}

				// Validate PH Application Key
				if ( $key == 'ph_application_key' ) {
					$new_input['ph_application_key'] = trim( $value );
				}

				// Validate PH User API Key
				if ( $key == 'ph_user_api_key' ) {
					$new_input['ph_user_api_key'] = trim( $value );
				}

				// Validate PH Publisher Key
				if ( $key == 'ph_publisher_id' ) {
					$new_input['ph_publisher_id'] = trim( $value );
				}

			} // foreach

			// Override setting so it's always enabled.
			$new_input['disable_api'] = trim( 'no' );

			return $new_input;
		}

	} // class Dfrapi_Configuration

} // class_exists check
