<?php

defined( 'ABSPATH' ) || exit;

/**
 * BEGIN
 * 
 * Get previous version stored in database.
 */
$previous_version = get_option( 'dfrapi_version', FALSE );


/**
 * Upgrade functions go here...
 */



/**
 * END
 * 
 * Now that any upgrade functions are performed, update version in database.
 */
update_option( 'dfrapi_version', DFRAPI_VERSION );
