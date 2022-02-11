<?php

defined( 'ABSPATH' ) || exit;

/**
 * Display Admin Notice if user has selected more than the DFRAPI_EXCESSIVE_MERCHANT_COUNT amount (default 1,000).
 *
 * @return void
 */
function dfrapi_excessive_merchants_selected_admin_notice() {

	$merchant_count = dfrapi_selected_merchant_count();

	if ( $merchant_count < DFRAPI_EXCESSIVE_MERCHANT_COUNT ) {
		return;
	}

	$status  = 'error';
	$plugin  = 'Datafeedr API';
	$heading = __( 'Too Many Merchants Selected', 'datafeedr-api' );
	$url     = dfrapi_merchants_page_url();
	$message = sprintf(
		__( 'You have selected <a href="%2$s">%1$s merchants</a>. Please go to <a href="%2$s">Datafeedr API > Merchants</a> and reduce the number of merchants you have selected to be less than %3$s.', 'datafeedr-api' ),
		number_format_i18n( $merchant_count ),
		esc_url( $url ),
		number_format_i18n( DFRAPI_EXCESSIVE_MERCHANT_COUNT )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_excessive_merchants_selected_admin_notice' );

/**
 * Display Admin Notice if user has not entered their Datafeedr API keys.
 *
 * @return void
 */
function dfrapi_datafeedr_api_keys_do_not_exist_notice() {

	if ( dfrapi_datafeedr_api_keys_exist() ) {
		return;
	}

	$status  = 'error';
	$plugin  = 'Datafeedr API';
	$heading = __( 'API Keys Missing', 'datafeedr-api' );
	$url     = dfrapi_configuration_page_url();
	$message = sprintf(
		__( 'You have not entered your Datafeedr API keys. Please go to <a href="%1$s">Datafeedr API > Configuration</a> and enter your Datafeedr API Access ID and Secret Key.', 'datafeedr-api' ),
		esc_url( $url )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_datafeedr_api_keys_do_not_exist_notice' );

/**
 * Display Admin Notice if user has not selected any affiliate networks.
 *
 * @return void
 */
function dfrapi_no_networks_selected_admin_notice() {

	// Don't display notice if at least one network has been selected.
	if ( dfrapi_user_has_selected_networks() ) {
		return;
	}

	// Don't display notice if user has not entered API keys yet.
	if ( ! dfrapi_datafeedr_api_keys_exist() ) {
		return;
	}

	$status  = 'error';
	$plugin  = 'Datafeedr API';
	$heading = __( 'No Affiliate Networks Selected', 'datafeedr-api' );
	$url     = dfrapi_networks_page_url();
	$message = sprintf(
		__( 'You have not selected any affiliate networks yet. Please go to <a href="%1$s">Datafeedr API > Networks</a> and select your desired affiliate networks.', 'datafeedr-api' ),
		esc_url( $url )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_no_networks_selected_admin_notice' );

/**
 * Display Admin Notice if user has not selected any merchants.
 *
 * @return void
 */
function dfrapi_no_merchants_selected_admin_notice() {

	// Don't display notice if at least one merchant has been selected.
	if ( dfrapi_user_has_selected_merchants() ) {
		return;
	}

	// Don't display notice if user has not entered API keys yet.
	if ( ! dfrapi_datafeedr_api_keys_exist() ) {
		return;
	}

	// Don't display notice if no networks have been selected yet.
	if ( ! dfrapi_user_has_selected_networks() ) {
		return;
	}

	$status  = 'error';
	$plugin  = 'Datafeedr API';
	$heading = __( 'No Merchants Selected', 'datafeedr-api' );
	$url     = dfrapi_merchants_page_url();
	$message = sprintf(
		__( 'You have not selected any merchants yet. Please go to <a href="%1$s">Datafeedr API > Merchants</a> and select your desired merchants.', 'datafeedr-api' ),
		esc_url( $url )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_no_merchants_selected_admin_notice' );


function dfrapi_api_usage_over_90_percent_admin_notice() {

	if ( ! dfrapi_api_usage_over_90_percent() ) {
		return;
	}

	$status  = 'warning';
	$plugin  = 'Datafeedr API';
	$heading = __( '90% of API Requests Used', 'datafeedr-api' );
	$url     = dfrapi_user_pages( 'change' );
	$message = sprintf(
		__( 'You have used over 90%% of your Datafeedr API requests for the current period. <a href="%1$s" target="_blank" rel="noopener nofollow">Upgrade your Datafeedr API subscription</a> to immediately access additional API requests.', 'datafeedr-api' ),
		esc_url( $url )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_api_usage_over_90_percent_admin_notice' );

function dfrapi_user_is_missing_affiliate_ids_admin_notice() {

	if ( ! dfrapi_user_is_missing_affiliate_ids() ) {
		return;
	}

	$missing_count = count( dfrapi_get_network_ids_missing_affiliate_id() );

	$status  = 'warning';
	$plugin  = 'Datafeedr API';
	$heading = __( 'Missing Affiliate IDs', 'datafeedr-api' );
	$url     = dfrapi_networks_page_url();
	$ids = translate_nooped_plural(_n_noop( 'affiliate ID', 'affiliate IDs', 'datafeedr-api' ), number_format_i18n($missing_count));
	$message = sprintf(
		__( 'You are missing %2$d %3$s. Please go to <a href="%1$s">Datafeedr API > Networks</a> and enter your missing %3$s.', 'datafeedr-api' ),
		esc_url( $url ),
		absint( $missing_count ),
		esc_html($ids)
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_user_is_missing_affiliate_ids_admin_notice' );

function dfrapi_display_admin_notices() {

	// @todo keep working through these Dfrapi_Env notices.
	new Dfrapi_Env(); // triggers a check for all Env related stuff.

	if ( $notices = get_option( 'dfrapi_admin_notices' ) ) {
		foreach ( $notices as $key => $message ) {
			$button          = ( $message['url'] != '' ) ? dfrapi_fix_button( $message['url'], $message['button_text'] ) : '';
			$upgrade_account = ( $key == 'usage_over_90_percent' ) ? ' | <a href="' . dfrapi_user_pages( 'change' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=upgradenag">' . __( 'Upgrade', 'datafeedr-api' ) . '</a>' : '';
			echo '<div class="' . $message['class'] . '"><p>' . $message['message'] . $button . $upgrade_account . '</p></div>';
		}
		delete_option( 'dfrapi_admin_notices' );
	}
}

add_action( 'admin_notices', 'dfrapi_display_admin_notices' );
