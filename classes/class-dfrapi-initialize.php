<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'Dfrapi_Initialize' ) ) {


class Dfrapi_Initialize {

	public function __construct() {

        // Core admin functions.
		require_once( DFRAPI_PATH . 'functions/admin-functions.php' );

		// Load required classes.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-env.php' );			// Checks environment for any problems.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-configuration.php' );	// Configuration page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-networks.php' );		// Networks page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-merchants.php' );		// Merchants page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-tools.php' );			// Tools page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-export.php' );		// Export page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-import.php' );		// Import page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-account.php' );		// Account page.
		require_once( DFRAPI_PATH . 'classes/class-dfrapi-help.php' );			// Help tabs.

		// Hooks
		add_action( 'admin_enqueue_scripts', 	array( $this, 'load_css' ) );
		add_action( 'admin_enqueue_scripts', 	array( $this, 'load_js' ) );
		add_action( 'plugins_loaded', 			array( $this, 'initialize_classes' ) );
		add_action( 'admin_notices', 			array( $this, 'admin_notices' ) );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'networks_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'merchants_menu' ), 30 );

		add_action( 'wp_ajax_search_form', 		array( $this, 'ajax_search_form' ) );

		add_filter( 'plugin_action_links_' . DFRAPI_BASENAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

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

	function networks_menu() {
		add_submenu_page(
			'dfrapi',
			__( 'Networks &#8212; Datafeedr API', 'datafeedr-api' ),
			__( 'Networks', 'datafeedr-api' ),
			'manage_options',
			'dfrapi_networks',
			array( $this, 'networks_output' )
		);
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

	function merchants_menu() {
		add_submenu_page(
			'dfrapi',
			__( 'Merchants &#8212; Datafeedr API', 'datafeedr-api' ),
			__( 'Merchants', 'datafeedr-api' ),
			'manage_options',
			'dfrapi_merchants',
			array( $this, 'merchants_output' )
		);
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
		if ( Dfrapi_Env::api_keys_exist() ) {

			if ( is_admin() && isset( $_GET['page'] ) && 'dfrapi_networks' == $_GET['page'] ) {
				new Dfrapi_Networks();
			}

			if ( is_admin() && isset( $_POST['option_page'] ) && ( 'dfrapi-networks' == $_POST['option_page'] ) ) {
				new Dfrapi_Networks();
			}
		}

		// Show "Merchants" page if API keys are present AND a network is selected.
		if ( Dfrapi_Env::api_keys_exist() &&  Dfrapi_Env::network_is_selected() ) {

			if ( is_admin() && isset( $_GET['page'] ) && 'dfrapi_merchants' == $_GET['page'] ) {
				new Dfrapi_Merchants();
			}

			if ( is_admin() && isset( $_POST['option_page'] ) && ( 'dfrapi-merchants' == $_POST['option_page'] ) ) {
				new Dfrapi_Merchants();
			}
		}

		// Show Tools, Export and Import pages if API keys are present.
		if ( Dfrapi_Env::api_keys_exist() ) {
			new Dfrapi_Tools();
			new Dfrapi_Export();
			new Dfrapi_Import();
			new Dfrapi_Account();
		}
	}

	function load_css() {

		// Basic styling for API pages.
		wp_register_style( 'dfrapi_css', DFRAPI_URL . 'css/style.css', false, DFRAPI_VERSION );
		wp_enqueue_style( 'dfrapi_css' );

		// Basic styling for API pages.
		wp_register_style( 'dfrapi_searchform', DFRAPI_URL . 'css/searchform.css', false, DFRAPI_VERSION );
		wp_enqueue_style( 'dfrapi_searchform' );
	}

    function load_js() {

    	wp_register_script( 'dfrapi_general_js', DFRAPI_URL.'js/general.js', array( 'jquery' ), DFRAPI_VERSION, true );
        wp_enqueue_script( 'dfrapi_general_js' );

    	wp_register_script( 'dfrapi_searchfilter_js', DFRAPI_URL.'js/searchfilter.js', array( 'jquery' ), DFRAPI_VERSION, false );
        wp_enqueue_script( 'dfrapi_searchfilter_js' );

    	wp_register_script( 'dfrapi_merchants_js', DFRAPI_URL.'js/merchants.js', array( 'jquery' ), DFRAPI_VERSION, false );
        wp_enqueue_script( 'dfrapi_merchants_js' );

    	wp_register_script( 'dfrapi_searchform_js', DFRAPI_URL.'js/searchform.js', array( 'jquery' ), DFRAPI_VERSION, false );
        wp_enqueue_script( 'dfrapi_searchform_js' );

        wp_register_script( 'dfrapi_jquery_reveal_js', DFRAPI_URL.'js/jquery.reveal.js', array( 'jquery' ), DFRAPI_VERSION, false );
        wp_enqueue_script( 'dfrapi_jquery_reveal_js' );
    }

	function admin_notices() {
		$dfrapi_env = new Dfrapi_Env();
		if ( $notices = get_option( 'dfrapi_admin_notices' ) ) {
			foreach ( $notices as $key => $message ) {
				$button = ( $message['url'] != '' ) ? dfrapi_fix_button( $message['url'], $message['button_text'] ) : '';
				$upgrade_account = ( $key == 'usage_over_90_percent' ) ? ' | <a href="' . dfrapi_user_pages( 'change' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=upgradenag">' . __( 'Upgrade', 'datafeedr-api' ) . '</a>' : '';
				echo '<div class="'.$message['class'].'"><p>'.$message['message'].$button.$upgrade_account.'</p></div>';
			}
			delete_option( 'dfrapi_admin_notices' );
		}
	}

	function plugin_row_meta( $links, $plugin_file ) {
		if ( $plugin_file === DFRAPI_BASENAME ) {
			$links[] = sprintf( '<a href="' . DFRAPI_DOCS_URL . '" target="_blank">%s</a>', __( 'Documentation', 'datafeedr-api' ) );
			$links[] = sprintf( '<a href="' . DFRAPI_HELP_URL . '" target="_blank">%s</a>', __( 'Support', 'datafeedr-api' ) );
		}

		return $links;
	}

	function action_links( $links ) {
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
