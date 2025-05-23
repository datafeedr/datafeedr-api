<?php

defined( 'ABSPATH' ) || exit;

/**
 * BEGIN
 *
 * Get previous version stored in database.
 */
$previous_version = get_option( 'dfrapi_version', false );

/*
|--------------------------------------------------------------------------
| Upgrade functions go here...
|--------------------------------------------------------------------------
*/

/**
 * Upgrade from any version less than 1.4.0, excluding if this is the plugin's first install.
 *
 * Here we kick off the upgrade process of v5 IDs to v6/v7 versions. This starts with
 * setting the initial status in the options table.
 *
 * @since 1.4.0
 */
if ( $previous_version && version_compare( $previous_version, '1.4.0', '<' ) ) {

	/**
	 * Initializes the upgrade process for version 1.4.0 of the Datafeedr API plugin.
	 *
	 * @return void
	 */
	function dfrapi_initialize_140_upgrade(): void {

		require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/Dfrapi_Version_140_Upgrade.php';

		Dfrapi_Version_140_Upgrade::set_initial_status();
	}

	add_action( 'wp_loaded', 'dfrapi_initialize_140_upgrade' );
}

/**
 * END
 *
 * Now that any upgrade functions are performed, update version in database.
 */
update_option( 'dfrapi_version', DFRAPI_VERSION );

