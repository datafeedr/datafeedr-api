<?php

defined( 'ABSPATH' ) || exit;

/**
 * BEGIN
 *
 * Get previous version stored in database.
 */
$previous_version = get_option( 'dfrapi_version', false );


/**
 * Upgrade functions go here...
 */


/**
 * Upgrade from any version < 1.4.0, excluding first install.
 *
 * Here we will set up some option in the DB about this important upgrade which proceeds
 * to update all v5 IDs to v7 IDs.
 *
 * @since 1.4.0
 */
if ( $previous_version && version_compare( $previous_version, '1.4.0', '<' ) ) {

	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/Dfrapi_Version_140_Upgrade.php';

	Dfrapi_Version_140_Upgrade::set_initial_status();
}


/**
 * END
 *
 * Now that any upgrade functions are performed, update version in database.
 */
update_option( 'dfrapi_version', DFRAPI_VERSION );

