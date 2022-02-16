<?php

defined( 'ABSPATH' ) || exit;

/**
 * Delete cached API data.
 *
 * We can only delete if user has API requests remaining.
 * This is because we'll need to make 1 request to rebuild 'dfrapi_account'.
 */
function dfrapi_delete_cached_api_data() {

	check_ajax_referer( 'dfrapi_ajax_nonce', 'dfrapi_security' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		_e( 'You do not have permission to perform this action.', 'datafeedr-api' );
		die;
	}

	$status = dfrapi_api_get_status();

	if ( ! array_key_exists( 'dfrapi_api_error', $status ) ) {

		delete_option( 'dfrapi_account' );
		$transient_options = get_option( 'dfrapi_transient_whitelist' );

		if ( ! empty( $transient_options ) ) {
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

	_e( 'Cached API data deleted successfully.', 'datafeedr-api' );
	die;
}

add_action( 'wp_ajax_dfrapi_delete_cached_api_data', 'dfrapi_delete_cached_api_data' );

/**
 * Test API Connection.
 */
function dfrapi_test_api_connection() {

	check_ajax_referer( 'dfrapi_ajax_nonce', 'dfrapi_security' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		_e( 'You do not have permission to perform this action.', 'datafeedr-api' );
		die;
	}

	$url      = "https://api.datafeedr.com";
	$response = wp_remote_get( $url );
	$code     = wp_remote_retrieve_response_code( $response );
	$success  = $code === 200;
	$color    = $success ? 'green' : 'red';
	$heading  = $success ? __( 'Success!', 'datafeedr-api' ) : __( 'Failed!', 'datafeedr-api' );
	$message  = $success
		? __( 'Your connection to the Datafeedr API is working.', 'datafeedr-api' )
		: $response->get_error_message();

	printf( '<h2 style="color:%s">%s</h2><hr /><pre>%s</pre>', esc_attr( $color ), esc_html( $heading ), esc_html( $message ) );

	die;
}

add_action( 'wp_ajax_dfrapi_test_api_connection', 'dfrapi_test_api_connection' );
