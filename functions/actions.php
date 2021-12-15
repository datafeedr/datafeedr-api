<?php

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display Admin Notice if user has selected more than the DFRAPI_EXCESSIVE_MERCHANT_COUNT amount (default 1,000).
 *
 * @return void
 */
function dfrapi_excessive_merchants_selected_notice() {

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

add_action( 'admin_notices', 'dfrapi_excessive_merchants_selected_notice' );
