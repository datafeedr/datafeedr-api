<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Admin_Help' ) ) :

/**
 * Dfrapi_Admin_Help Class
 */
class Dfrapi_Admin_Help {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( "current_screen", array( $this, 'add_tabs' ), 50 );
	}

	/**
	 * Add help tabs
	 */
	public function add_tabs() {

		$screen = get_current_screen();

		/*
		[id] => toplevel_page_dfrapi
		[id] => datafeedr-api_page_dfrapi_networks
		[id] => datafeedr-api_page_dfrapi_merchants
		[id] => datafeedr-api_page_dfrapi_tools
		[id] => datafeedr-api_page_dfrapi_export
		[id] => datafeedr-api_page_dfrapi_import
		[id] => datafeedr-api_page_dfrapi_account
		*/
		$possible_screens = array(
			'toplevel_page_dfrapi',
			'datafeedr-api_page_dfrapi_networks',
			'datafeedr-api_page_dfrapi_merchants',
			'datafeedr-api_page_dfrapi_tools',
			'datafeedr-api_page_dfrapi_export',
			'datafeedr-api_page_dfrapi_import',
			'datafeedr-api_page_dfrapi_account',
		);

		if ( ! in_array( $screen->id, $possible_screens ) ) { return; }

		// The following tabs appear on ALL screens.
		dfrapi_help_tab( $screen );


	}
}

endif;

return new Dfrapi_Admin_Help();
