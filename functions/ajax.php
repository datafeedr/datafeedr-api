<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_admin() ) {
	add_action( 'wp_ajax_dfrapi_delete_cached_api_data', 		'dfrapi_delete_cached_api_data' );
}

/**
 * Delete cached API data.
 */
function dfrapi_delete_cached_api_data() {
	check_ajax_referer( 'dfrapi_ajax_nonce', 'dfrapi_security' );
	// Only delete if user has API requests remaining. This is because we'll need to make 1 request to rebuild 'dfrapi_account'.
	$status = dfrapi_api_get_status();
	if ( !array_key_exists( 'dfrapi_api_error', $status ) ) {
		delete_option( 'dfrapi_account' );
		$transient_options = get_option( 'dfrapi_transient_whitelist' );
		if ( !empty( $transient_options ) ) {
			foreach ( $transient_options as $name ) {
				$use_cache = wp_using_ext_object_cache( false );
				delete_transient( $name );
				wp_using_ext_object_cache( $use_cache );
			}
		}
		// Update account status immediately in case there are not enough API
		// requests remaining in order to do so later.
		$status = dfrapi_api_get_status();
		update_option( 'dfrapi_account', $status );
	}
	_e( 'Cached API data deleted successfully.', DFRAPI_DOMAIN );
	die;
}

