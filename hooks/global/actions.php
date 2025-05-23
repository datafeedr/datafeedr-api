<?php

/**
 * Handles the upgrade process to version 1.4.0.
 *
 * This function initializes the upgrade process for version 1.4.0 by
 * creating an instance of the Dfrapi_Version_140_Upgrade class and
 * invoking its process_stage method.
 *
 * @return void
 */
function dfrapi_handle_version_140_upgrade(): void {
	$upgrader = new Dfrapi_Version_140_Upgrade();
	$upgrader->process_stage();
}

add_action( 'dfrapi_handle_version_140_upgrade_action', 'dfrapi_handle_version_140_upgrade' );
