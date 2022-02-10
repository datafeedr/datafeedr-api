<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Dfrapi_Initialize' ) ) {

	class Dfrapi_Initialize {

		public function __construct() {

			// Actions
			add_action( 'plugins_loaded', [ $this, 'initialize_classes' ] );
//			add_action( 'admin_notices', [ $this, 'admin_notices' ] );
			add_action( 'admin_menu', [ $this, 'admin_menu' ] );
			add_action( 'admin_menu', [ $this, 'networks_menu' ], 20 );
			add_action( 'admin_menu', [ $this, 'merchants_menu' ], 30 );
			add_action( 'wp_ajax_search_form', [ $this, 'ajax_search_form' ] );

			// Filters
			add_filter( 'plugin_action_links_' . DFRAPI_BASENAME, [ $this, 'action_links' ] );
			add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );

			// Datafeedr Admin Pages have been loaded. Broadcast "loaded" action.
			do_action( 'dfrapi_loaded' );
		}

		function ajax_search_form() {
			$sform = new Dfrapi_SearchForm();
			echo $sform->ajaxHandler();
			die;
		}

		function admin_menu() {
			add_menu_page(
				__( 'Datafeedr API', 'datafeedr-api' ),
				__( 'Datafeedr API', 'datafeedr-api' ),
				'manage_options',
				'dfrapi',
				'',
				null,
				42
			);
		}

		public function networks_menu() {
			if ( dfrapi_datafeedr_api_keys_exist() ) {
				add_submenu_page(
					'dfrapi',
					__( 'Networks &#8212; Datafeedr API', 'datafeedr-api' ),
					__( 'Networks', 'datafeedr-api' ),
					'manage_options',
					'dfrapi_networks',
					array( $this, 'networks_output' )
				);
			}
		}

		public function networks_output() {
			$key  = 'dfrapi_networks';
			$page = 'dfrapi-networks';
			echo '<div class="wrap" id="' . $key . '">';
			echo '<h2>' . dfrapi_setting_pages( $page ) . ' &#8212; Datafeedr API</h2>';
			echo '<form method="post" action="options.php">';
			submit_button();
			wp_nonce_field( 'update-options' );
			settings_fields( $page );
			do_settings_sections( $page );
			submit_button();
			echo '</form>';
			echo '<div id="dfr_unload_message" style="display:none">' .
			     __( 'You have unsaved changes', 'datafeedr-api' ) .
			     '</div>';
			echo '</div>';
		}

		public function merchants_menu() {
			if ( dfrapi_datafeedr_api_keys_exist() && dfrapi_selected_network_count() > 0 ) {
				add_submenu_page(
					'dfrapi',
					__( 'Merchants &#8212; Datafeedr API', 'datafeedr-api' ),
					__( 'Merchants', 'datafeedr-api' ),
					'manage_options',
					'dfrapi_merchants',
					array( $this, 'merchants_output' )
				);
			}
		}

		function merchants_output() {
			$key  = 'dfrapi_merchants';
			$page = 'dfrapi-merchants';
			echo '<div class="wrap" id="' . $key . '">';
			echo '<h2>' . dfrapi_setting_pages( $page ) . ' &#8212; Datafeedr API</h2>';
			echo '<form method="post" action="options.php">';
			submit_button();
			wp_nonce_field( 'update-options' );
			settings_fields( $page );
			do_settings_sections( $page );
			submit_button();
			echo '</form>';
			echo '<div id="dfr_unload_message" style="display:none">' .
			     __( 'You have unsaved changes', 'datafeedr-api' ) . '</div>';
			echo '</div>';
		}

		function initialize_classes() {

			new Dfrapi_Configuration();

			// Show "Networks" page if API keys are present.
			if ( dfrapi_datafeedr_api_keys_exist() ) {

				if ( isset( $_GET['page'] ) && 'dfrapi_networks' === $_GET['page'] && is_admin() ) {
					new Dfrapi_Networks();
				}

				if ( isset( $_POST['option_page'] ) && ( 'dfrapi-networks' === $_POST['option_page'] ) && is_admin() ) {
					new Dfrapi_Networks();
				}
			}

			// Show "Merchants" page if API keys are present AND a network is selected.
			if ( dfrapi_datafeedr_api_keys_exist() && Dfrapi_Env::network_is_selected() ) {

				if ( is_admin() && isset( $_GET['page'] ) && 'dfrapi_merchants' === $_GET['page'] ) {
					new Dfrapi_Merchants();
				}

				if ( is_admin() && isset( $_POST['option_page'] ) && ( 'dfrapi-merchants' === $_POST['option_page'] ) ) {
					new Dfrapi_Merchants();
				}
			}

			// Show Tools, Export and Import pages if API keys are present.
			if ( dfrapi_datafeedr_api_keys_exist() ) {
				new Dfrapi_Tools();
				new Dfrapi_Export();
				new Dfrapi_Import();
				new Dfrapi_Account();
			}
		}


		public function plugin_row_meta( $links, $plugin_file ) {
			if ( $plugin_file === DFRAPI_BASENAME ) {
				$links[] = sprintf( '<a href="' . DFRAPI_DOCS_URL . '" target="_blank">%s</a>', __( 'Documentation', 'datafeedr-api' ) );
				$links[] = sprintf( '<a href="' . DFRAPI_HELP_URL . '" target="_blank">%s</a>', __( 'Support', 'datafeedr-api' ) );
			}

			return $links;
		}

		public function action_links( $links ) {
			return array_merge(
				$links,
				array(
					'config' => '<a href="' . admin_url( 'admin.php?page=dfrapi' ) . '">' . __( 'Configuration', 'datafeedr-api' ) . '</a>',
				)
			);
		}

	}

	$dfrapi_initialize = new Dfrapi_Initialize();

} // class_exists check
