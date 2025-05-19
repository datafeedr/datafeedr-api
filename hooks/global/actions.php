<?php

/**
 * @return void
 */
function dfrapi_handle_version_140_upgrade(): void {
	$upgrader = new Dfrapi_Version_140_Upgrade();
	$upgrader->process_stage();
}

add_action( 'dfrapi_handle_version_140_upgrade_action', 'dfrapi_handle_version_140_upgrade' );