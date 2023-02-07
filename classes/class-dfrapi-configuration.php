<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Dfrapi_Configuration' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Configuration {

		private $page = 'dfrapi-configuration';
		private $key;
		private $account;
		public $options;

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
				__( 'Configuration &#8212; Datafeedr API', 'datafeedr-api' ),
				__( 'Configuration', 'datafeedr-api' ),
				'manage_options',
				'dfrapi',
				array( $this, 'output' )
			);
		}

		function admin_notice() {
			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && isset( $_GET['page'] ) && 'dfrapi' == $_GET['page'] ) {
				dfrapi_admin_notice( __( 'Configuration successfully updated!', 'datafeedr-api' ), 'success' );
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
					'effiliation_key'          => '',
					'awin_access_token'        => '',
					'affiliate_gateway_sid'    => '',
					'belboon_aid'              => '',
					'adservice_mid'            => '',
					'hs_beacon'                => 'on',
				),
				$this->options
			);
		}

		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			add_settings_section( 'api_settings', __( 'Datafeedr API Settings', 'datafeedr-api' ), array( &$this, 'section_api_settings_desc' ), $this->page );
			add_settings_field( 'access_id', __( 'API Access ID', 'datafeedr-api' ), array( &$this, 'field_access_id' ), $this->page, 'api_settings' );
			add_settings_field( 'secret_key',  __( 'API Secret Key', 'datafeedr-api' ), array( &$this, 'field_secret_key' ), $this->page, 'api_settings' );
			// add_settings_field( 'transport_method',  __( 'Transport Method', 'datafeedr-api' ), array( &$this, 'field_transport_method' ), $this->page, 'api_settings' );

			/*
			add_settings_field(
				'disable_api',
				__( 'Disable API', 'datafeedr-api' ),
				array( &$this, 'field_disable_api' ),
				$this->page,
				'api_settings'
			);
			*/

			add_settings_section( 'awin_settings', __( 'Awin Settings', 'datafeedr-api' ), array( &$this, 'section_awin_settings_desc' ), $this->page );
			add_settings_field( 'awin_access_token', __( 'Awin API Token', 'datafeedr-api' ), array( &$this, 'field_awin_access_token' ), $this->page, 'awin_settings' );

			add_settings_section( 'affiliate_gateway_settings', __( 'Affiliate Gateway Settings', 'datafeedr-api' ), array( &$this, 'section_affiliate_gateway_settings_desc' ), $this->page );
			add_settings_field( 'affiliate_gateway_sid', __( 'Affiliate Gateway SID', 'datafeedr-api' ), array( &$this, 'field_affiliate_gateway_sid' ), $this->page, 'affiliate_gateway_settings' );

			add_settings_section( 'adservice_settings', __( 'Adservice Settings', 'datafeedr-api' ), array( &$this, 'section_adservice_settings_desc' ), $this->page );
			add_settings_field( 'adservice_mid', __( 'Adservice Media ID', 'datafeedr-api' ), array( &$this, 'field_adservice_mid' ), $this->page, 'adservice_settings' );

			add_settings_section( 'belboon_settings', __( 'Belboon Settings', 'datafeedr-api' ), array( &$this, 'section_belboon_settings_desc' ), $this->page );
			add_settings_field( 'belboon_aid', __( 'Adspace ID', 'datafeedr-api' ), array( &$this, 'field_belboon_aid' ), $this->page, 'belboon_settings' );

			if ( defined( 'DFRCS_VERSION' ) ) {
				add_settings_section( 'amazon_api_settings', __( 'Amazon Settings', 'datafeedr-api' ), array( &$this, 'section_amazon_api_settings_desc' ), $this->page );
				add_settings_field( 'amazon_access_key_id', __( 'Amazon Access Key ID', 'datafeedr-api' ), array( &$this, 'field_amazon_access_key_id' ), $this->page, 'amazon_api_settings' );
				add_settings_field( 'amazon_secret_access_key', __( 'Amazon Secret Access Key', 'datafeedr-api' ), array( &$this, 'field_amazon_secret_access_key' ), $this->page, 'amazon_api_settings' );
				add_settings_field( 'amazon_tracking_id', __( 'Amazon Tracking ID', 'datafeedr-api' ), array( &$this, 'field_amazon_tracking_id' ), $this->page, 'amazon_api_settings' );
				add_settings_field( 'amazon_locale', __( 'Amazon Locale', 'datafeedr-api' ), array( &$this, 'field_amazon_locale' ), $this->page, 'amazon_api_settings' );
			}

			add_settings_section( 'ph_api_settings', __( 'Partnerize Settings', 'datafeedr-api' ), array( &$this, 'section_ph_api_settings_desc' ), $this->page );
			add_settings_field( 'ph_application_key', __( 'Application Key', 'datafeedr-api' ), array( &$this, 'field_ph_application_key' ), $this->page, 'ph_api_settings' );
			add_settings_field( 'ph_user_api_key', __( 'User API Key', 'datafeedr-api' ), array( &$this, 'field_ph_user_api_key' ), $this->page, 'ph_api_settings' );
			add_settings_field( 'ph_publisher_id', __( 'Publisher ID', 'datafeedr-api' ), array( &$this, 'field_ph_publisher_id' ), $this->page, 'ph_api_settings' );

			add_settings_section( 'effiliation_api_settings', __( 'Effiliation Settings', 'datafeedr-api' ), array( &$this, 'section_effiliation_api_settings_desc' ), $this->page );
			add_settings_field( 'effiliation_key', __( 'Effiliation Key', 'datafeedr-api' ), array( &$this, 'field_effiliation_key' ), $this->page, 'effiliation_api_settings' );

			add_settings_section( 'hs_beacon_settings', __( 'Datafeedr Documentation & Support Link', 'datafeedr-api' ), array( &$this, 'section_hs_beacon_settings_desc' ), $this->page );
			add_settings_field( 'hs_beacon_status', __( 'Enabled', 'datafeedr-api' ), array( &$this, 'field_hs_beacon_status' ), $this->page, 'hs_beacon_settings' );
		}

		function section_api_settings_desc() {
			echo __( 'Add your ', 'datafeedr-api' );
			echo ' <a href="'.DFRAPI_KEYS_URL.'?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiconfigpage" target="_blank" title="' . __( 'Get your Datafeedr API Keys', 'datafeedr-api' ) . '">';
			echo __( 'Datafeedr API Keys', 'datafeedr-api' );
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
			echo __( 'If you want to use the Partnerize affiliate network, add your ', 'datafeedr-api' );
			echo ' <a href="https://datafeedrapi.helpscoutdocs.com/article/195-how-to-find-your-partnerize-publisher-id-and-api-keys" target="_blank" title="' . __( 'Learn how to find your Partnerize Keys', 'datafeedr-api' ) . '">';
			echo __( 'Partnerize Keys', 'datafeedr-api' );
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

		function section_effiliation_api_settings_desc() {
			echo __( 'If you want to use the Effiliation affiliate network, add your ', 'datafeedr-api' );
			echo ' <a href="https://datafeedrapi.helpscoutdocs.com/article/211-how-to-find-your-effiliation-api-key" target="_blank" title="' . __( 'Get your Effiliation Key', 'datafeedr-api' ) . '">';
			echo __( 'Effiliation Key', 'datafeedr-api' );
			echo '</a>.';
		}

		function field_effiliation_key() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[effiliation_key]" value="<?php echo esc_attr( $this->options['effiliation_key'] ); ?>" />
			<?php
		}

		function section_awin_settings_desc() {
			echo __( 'If you want to use the Awin affiliate network, enter your Awin API Token to view the Awin programs you have joined.', 'datafeedr-api' );
			echo ' <a href="https://datafeedrapi.helpscoutdocs.com/article/120-how-to-find-your-awin-affiliate-id-and-api-key" target="_blank" title="' . __( 'Learn how to get your Awin API Token', 'datafeedr-api' ) . '">';
			echo __( 'Learn how to find your Awin API Token', 'datafeedr-api' );
			echo '</a>.';
		}

		function field_awin_access_token() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[awin_access_token]" value="<?php echo esc_attr( $this->options['awin_access_token'] ); ?>" />
			<?php
		}

		function section_affiliate_gateway_settings_desc() {
			echo __( 'If you want to use The Affiliate Gateway affiliate network, enter your Affiliate Gateway SID.', 'datafeedr-api' );
			echo ' <a href="https://datafeedrapi.helpscoutdocs.com/article/225-how-to-find-your-affiliate-gateway-affiliate-id" target="_blank" title="' . __( 'Learn how to get your Affiliate Gateway SID', 'datafeedr-api' ) . '">';
			echo __( 'Learn how to get your Affiliate Gateway SID', 'datafeedr-api' );
			echo '</a>.';
		}

		function field_affiliate_gateway_sid() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[affiliate_gateway_sid]" value="<?php echo esc_attr( $this->options['affiliate_gateway_sid'] ); ?>" />
			<?php
		}

		function section_adservice_settings_desc() {
			echo __( 'If you want to use the Adservice affiliate network, enter your Adservice Media ID.', 'datafeedr-api' );
			echo ' <a href="https://datafeedrapi.helpscoutdocs.com/article/251-how-to-find-your-adservice-affiliate-id-and-media-id" target="_blank" title="' . __( 'Learn how to get your Adservice Media ID', 'datafeedr-api' ) . '">';
			echo __( 'Learn how to get your Adservice Media ID', 'datafeedr-api' );
			echo '</a>.';
		}

		function field_adservice_mid() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[adservice_mid]" value="<?php echo esc_attr( $this->options['adservice_mid'] ); ?>" />
			<?php
		}

		function section_belboon_settings_desc() {
			echo __( 'If you want to use the Belboon affiliate network, enter your Belboon Adspace ID.', 'datafeedr-api' );
			echo ' <a href="https://datafeedrapi.helpscoutdocs.com/article/125-how-to-find-your-belboon-affiliate-id" target="_blank" title="' . __( 'Learn how to get your Belboon Adspace ID', 'datafeedr-api' ) . '">';
			echo __( 'Learn how to get your Belboon Adspace ID', 'datafeedr-api' );
			echo '</a>.';
		}

		function field_belboon_aid() {
			?>
            <input type="text" class="regular-text" name="<?php echo $this->key; ?>[belboon_aid]" value="<?php echo esc_attr( $this->options['belboon_aid'] ); ?>" />
			<?php
		}

		function field_transport_method() {
			?>
            <select id="transport_method" name="<?php echo $this->key; ?>[transport_method]">
                <option value="curl" <?php selected( $this->options['transport_method'], 'curl', true ); ?>><?php _e( 'CURL', 'datafeedr-api' ); ?></option>
                <option value="file" <?php selected( $this->options['transport_method'], 'file', true ); ?>><?php _e( 'File', 'datafeedr-api' ); ?></option>
                <option value="socket" <?php selected( $this->options['transport_method'], 'socket', true ); ?>><?php _e( 'Socket', 'datafeedr-api' ); ?></option>
            </select>
            <p class="description"><?php _e( 'If you\'re not sure, use CURL.', 'datafeedr-api' ); ?></p>
			<?php
		}

		function field_disable_api() {
			?>
            <p>
                <input type="radio" value="yes" name="<?php echo $this->key; ?>[disable_api]" <?php checked( $this->options['disable_api'], 'yes', true ); ?> /> <?php _e( 'Yes', 'datafeedr-api' ); ?>
            </p>
            <p>
                <input type="radio" value="no" name="<?php echo $this->key; ?>[disable_api]" <?php checked( $this->options['disable_api'], 'no', true ); ?> /> <?php _e( 'No', 'datafeedr-api' ); ?>
            </p>
            <p class="description"><?php _e( 'Prevent your site from attempting to make a request to the Datafeedr API.', 'datafeedr-api' ); ?></p>
			<?php
		}

		function section_amazon_api_settings_desc() {
			echo __( 'Add your ', 'datafeedr-api' );
			echo '<a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/your-account.html" target="_blank">';
			echo __( 'Amazon Product Advertising API keys', 'datafeedr-api' );
			echo '</a>.';
			echo '<br /><span style="color:red">';
			echo __( 'These are ONLY compatible with the ', 'datafeedr-api' );
			echo '<a href="https://datafeedr.me/dfrcs" target="_blank">';
			echo __( 'Datafeedr Comparison Sets', 'datafeedr-api' );
			echo '</a>';
			echo __( ' plugin.', 'datafeedr-api' );
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
				<?php _e( 'This is the same as your Associates Tag usually ending in "-20" or "-21". Can be found ', 'datafeedr-api' ); ?>
                <a href="https://affiliate-program.amazon.com/gp/associates/network/main.html" target="_blank"><?php _e( 'here', 'datafeedr-api' ); ?></a>.
            </p>
			<?php
		}

		function field_amazon_locale() {
			?>
            <select id="amazon_locale" name="<?php echo $this->key; ?>[amazon_locale]">
                <option value="au" <?php selected( $this->options['amazon_locale'], 'au', true ); ?>><?php _e( 'Australia', 'datafeedr-api' ); ?></option>
                <option value="br" <?php selected( $this->options['amazon_locale'], 'br', true ); ?>><?php _e( 'Brazil', 'datafeedr-api' ); ?></option>
                <option value="ca" <?php selected( $this->options['amazon_locale'], 'ca', true ); ?>><?php _e( 'Canada', 'datafeedr-api' ); ?></option>
                <option value="fr" <?php selected( $this->options['amazon_locale'], 'fr', true ); ?>><?php _e( 'France', 'datafeedr-api' ); ?></option>
                <option value="de" <?php selected( $this->options['amazon_locale'], 'de', true ); ?>><?php _e( 'Germany', 'datafeedr-api' ); ?></option>
                <option value="in" <?php selected( $this->options['amazon_locale'], 'in', true ); ?>><?php _e( 'India', 'datafeedr-api' ); ?></option>
                <option value="it" <?php selected( $this->options['amazon_locale'], 'it', true ); ?>><?php _e( 'Italy', 'datafeedr-api' ); ?></option>
                <option value="jp" <?php selected( $this->options['amazon_locale'], 'jp', true ); ?>><?php _e( 'Japan', 'datafeedr-api' ); ?></option>
                <option value="mx" <?php selected( $this->options['amazon_locale'], 'mx', true ); ?>><?php _e( 'Mexico', 'datafeedr-api' ); ?></option>
                <option value="nl" <?php selected( $this->options['amazon_locale'], 'nl', true ); ?>><?php _e( 'Netherlands', 'datafeedr-api' ); ?></option>
                <option value="sg" <?php selected( $this->options['amazon_locale'], 'sg', true ); ?>><?php _e( 'Singapore', 'datafeedr-api' ); ?></option>
                <option value="sa" <?php selected( $this->options['amazon_locale'], 'sa', true ); ?>><?php _e( 'Saudi Arabia', 'datafeedr-api' ); ?></option>
                <option value="es" <?php selected( $this->options['amazon_locale'], 'es', true ); ?>><?php _e( 'Spain', 'datafeedr-api' ); ?></option>
                <option value="se" <?php selected( $this->options['amazon_locale'], 'se', true ); ?>><?php _e( 'Sweden', 'datafeedr-api' ); ?></option>
                <option value="tr" <?php selected( $this->options['amazon_locale'], 'tr', true ); ?>><?php _e( 'Turkey', 'datafeedr-api' ); ?></option>
                <option value="ae" <?php selected( $this->options['amazon_locale'], 'ae', true ); ?>><?php _e( 'United Arab Emirates', 'datafeedr-api' ); ?></option>
                <option value="uk" <?php selected( $this->options['amazon_locale'], 'uk', true ); ?>><?php _e( 'United Kingdom', 'datafeedr-api' ); ?></option>
                <option value="us" <?php selected( $this->options['amazon_locale'], 'us', true ); ?>><?php _e( 'United States', 'datafeedr-api' ); ?></option>
            </select>
            <p class="description">
                <a href="http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AssociateIDs.html" target="_blank"><?php _e( 'More information', 'datafeedr-api' ); ?></a> <?php _e( 'regarding Amazon Locales.', 'datafeedr-api' ); ?>
            </p>
			<?php
		}

		function section_hs_beacon_settings_desc() {
			echo __( 'Display the link to Datafeedr documentation and support on every Datafeedr-specific page in your WordPress Admin Area. ',
				'datafeedr-api' );
			echo __( 'This provides the full Datafeedr documentation and access to a support contact form right inside your WordPress site.',
				'datafeedr-api' );
		}

		function field_hs_beacon_status() {
			?>
            <p>
                <input type="radio" value="on" name="<?php echo $this->key; ?>[hs_beacon]" <?php checked( $this->options['hs_beacon'], 'on', true ); ?> /> <?php _e( 'Yes', 'datafeedr-api' ); ?>
            </p>
            <p>
                <input type="radio" value="off" name="<?php echo $this->key; ?>[hs_beacon]" <?php checked( $this->options['hs_beacon'], 'off', true ); ?> /> <?php _e( 'No', 'datafeedr-api' ); ?>
            </p>
			<?php
		}

		function validate( $input ) {

			if ( !isset( $input ) || !is_array( $input ) || empty( $input ) ) { return $input; }

			$new_input = array();

			foreach ( $input as $key => $value ) {

				// Validate "access_id"
				if ( $key === 'access_id' ) {
					$new_input['access_id'] = trim( $value );
				}

				// Validate "secret_key"
				if ( $key === 'secret_key' ) {
					$new_input['secret_key'] = trim( $value );
				}

				// Validate "transport_method"
				if ( $key === 'transport_method' ) {
					$new_input['transport_method'] = trim( $value );
				}

				// Validate "disable_api"
				if ( $key === 'disable_api' ) {
					$new_input['disable_api'] = trim( $value );
				}

				// Validate Amazon Access Key ID
				if ( $key === 'amazon_access_key_id' ) {
					$new_input['amazon_access_key_id'] = trim( $value );
				}

				// Validate Amazon Secret Access Key
				if ( $key === 'amazon_secret_access_key' ) {
					$new_input['amazon_secret_access_key'] = trim( $value );
				}

				// Validate Amazon Tracking ID
				if ( $key === 'amazon_tracking_id' ) {
					$new_input['amazon_tracking_id'] = trim( $value );
				}

				// Validate Amazon Locale
				if ( $key === 'amazon_locale' ) {
					$new_input['amazon_locale'] = trim( $value );
				}

				// Validate PH Application Key
				if ( $key === 'ph_application_key' ) {
					$new_input['ph_application_key'] = trim( $value );
				}

				// Validate PH User API Key
				if ( $key === 'ph_user_api_key' ) {
					$new_input['ph_user_api_key'] = trim( $value );
				}

				// Validate PH Publisher Key
				if ( $key === 'ph_publisher_id' ) {
					$new_input['ph_publisher_id'] = trim( $value );
				}

				// Validate Effiliation Key
				if ( $key === 'effiliation_key' ) {
					$new_input['effiliation_key'] = trim( $value );
				}

				// Validate Awin Acceess Token
				if ( $key === 'awin_access_token' ) {
					$new_input['awin_access_token'] = trim( $value );
				}

				// Validate The Affiliate Gateway SID
				if ( $key === 'affiliate_gateway_sid' ) {
					$new_input['affiliate_gateway_sid'] = trim( $value );
				}

				// Validate Adservice MID
				if ( $key === 'adservice_mid' ) {
					$new_input['adservice_mid'] = trim( $value );
				}

				// Validate Belboon AID
				if ( $key === 'belboon_aid' ) {
					$new_input['belboon_aid'] = trim( $value );
				}

				// Enable HelpScout Beacon
				if ( $key === 'hs_beacon' ) {
					$new_input['hs_beacon'] = ( 'on' === $value ) ? 'on' : 'off';
				}
			} // foreach

			// Override setting so it's always enabled.
			$new_input['disable_api'] = trim( 'no' );

			return $new_input;
		}

	} // class Dfrapi_Configuration

} // class_exists check
