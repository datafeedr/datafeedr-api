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
	$message = sprintf(
		__( 'You have selected <a href="%2$s">%1$s merchants</a>. Please go to <a href="%2$s">Datafeedr API > Merchants</a> and reduce the number of merchants you have selected to be less than %3$s.', 'datafeedr-api' ),
		number_format_i18n( $merchant_count ),
		esc_url( dfrapi_merchants_page_url() ),
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
	$message = sprintf(
		__( 'You have not entered your Datafeedr API keys. Please go to <a href="%1$s">Datafeedr API > Configuration</a> and enter your Datafeedr API Access ID and Secret Key.', 'datafeedr-api' ),
		esc_url( dfrapi_configuration_page_url() )
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
	$message = sprintf(
		__( 'You have not selected any affiliate networks yet. Please go to <a href="%1$s">Datafeedr API > Networks</a> and select your desired affiliate networks.', 'datafeedr-api' ),
		esc_url( dfrapi_networks_page_url() )
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
	$message = sprintf(
		__( 'You have not selected any merchants yet. Please go to <a href="%1$s">Datafeedr API > Merchants</a> and select your desired merchants.', 'datafeedr-api' ),
		esc_url( dfrapi_merchants_page_url() )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_no_merchants_selected_admin_notice' );

/**
 * Display Admin Notice if user has used more than 90% of their API requests for this pay-period.
 *
 * @return void
 */
function dfrapi_api_usage_over_90_percent_admin_notice() {

	if ( ! dfrapi_api_usage_over_90_percent() ) {
		return;
	}

	$status  = 'warning';
	$plugin  = 'Datafeedr API';
	$heading = __( '90% of API Requests Used', 'datafeedr-api' );
	$message = sprintf(
		__( 'You have used over 90%% of your Datafeedr API requests for the current period. <a href="%1$s" target="_blank" rel="noopener nofollow">Upgrade your Datafeedr API subscription</a> to immediately access additional API requests.', 'datafeedr-api' ),
		esc_url( dfrapi_user_pages( 'change' ) )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_api_usage_over_90_percent_admin_notice' );

/**
 * Display Admin Notice if user is missing at least 1 affiliate ID on the Networks page.
 *
 * @return void
 */
function dfrapi_user_is_missing_affiliate_ids_admin_notice() {

	if ( ! dfrapi_user_is_missing_affiliate_ids() ) {
		return;
	}

	$missing_count = count( dfrapi_get_network_ids_missing_affiliate_id() );

	$status  = 'warning';
	$plugin  = 'Datafeedr API';
	$heading = __( 'Missing Affiliate IDs', 'datafeedr-api' );
	$ids     = translate_nooped_plural( _n_noop( 'affiliate ID', 'affiliate IDs', 'datafeedr-api' ), number_format_i18n( $missing_count ) );
	$message = sprintf(
		__( 'You are missing %2$d %3$s. Please go to <a href="%1$s">Datafeedr API > Networks</a> and enter your missing %3$s.', 'datafeedr-api' ),
		esc_url( dfrapi_networks_page_url() ),
		absint( $missing_count ),
		esc_html( $ids )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_user_is_missing_affiliate_ids_admin_notice' );

/**
 * Display Admin Notice if user has selected a Partnerize merchant which they are not approved by.
 *
 * @return void
 */
function dfrapi_unapproved_partnerize_merchants_selected() {

	global $wpdb;

	// Get Partnerize Network IDs
	$partnerize_network_ids = dfrapi_get_partnerize_network_ids();

	// Get user's currently selected networks and convert into simple array of IDs.
	$selected_network_ids = dfrapi_get_selected_network_ids();

	// Get all Partnerize Network IDs from the user's selected Network IDs.
	$selected_partnerize_ids = array_intersect( $partnerize_network_ids, $selected_network_ids );

	// If there are no selected Partnerize IDs, return.
	if ( empty( $selected_partnerize_ids ) ) {
		return;
	}

	$results = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '%_transient_camref_%' " );

	// No results so nothing to worry about. Return false.
	if ( empty( $results ) ) {
		return;
	}

	$unapproved_merchant_ids = [];

	foreach ( $results as $row ) {

		if ( 'dfrapi_unapproved_ph_merchant' !== $row->option_value ) {
			continue;
		}

		$option_name = $row->option_name; // Example: _transient_camref_46627

		$unapproved_merchant_ids[] = absint( dfrapi_str_after_last( $option_name, '_' ) );
	}

	$unapproved_merchant_ids = array_filter( array_unique( $unapproved_merchant_ids ) );

	if ( empty( $unapproved_merchant_ids ) ) {
		return;
	}

	$merchants = dfrapi_api_get_merchants_by_id( $unapproved_merchant_ids );

	if ( empty( $merchants ) ) {
		return;
	}

	$merchant_source = array_column( $merchants, 'source' );
	$merchant_name   = array_column( $merchants, 'name' );
	array_multisort(
		$merchant_source, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE,
		$merchant_name, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE,
		$merchants
	);

	$list = '';
	foreach ( $merchants as $merchant ) {
		$list .= sprintf(
			'<br />- %1$s: <strong>%2$s</strong> <small>(%3$d)</small>',
			esc_html( $merchant['source'] ),
			esc_html( $merchant['name'] ),
			absint( $merchant['_id'] )
		);
	}

	$status  = 'error';
	$plugin  = 'Datafeedr API';
	$heading = __( 'Unapproved Partnerize Merchants Selected', 'datafeedr-api' );
	$message = sprintf(
		__( 'You have selected one or more Partnerize merchants who have not approved your publisher account:<br />%1$s<br /><br />Please go to <a href="%2$s">Datafeedr API > Merchants</a> and remove these merchants from your selected list of Merchants. Then go here <a href="%3$s">Datafeedr API > Tools</a> and click the <strong>[Delete Cached API Data]</strong> button.', 'datafeedr-api' ),
		$list,
		esc_url( dfrapi_merchants_page_url() ),
		esc_url( dfrapi_tools_page_url() )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_unapproved_partnerize_merchants_selected' );

/**
 * Display Admin Notice if user has selected a Effiliation merchant which they are not approved by.
 *
 * @return void
 */
function dfrapi_unapproved_effiliation_merchants_selected() {

	global $wpdb;

	// Get range of Effiliation Network IDs
	$effiliation_network_ids = dfrapi_get_effiliation_network_ids();

	// Get user's currently selected networks and convert into simple array of IDs.
	$selected_network_ids = dfrapi_get_selected_network_ids();

	// Get all Effiliation Network IDs from the user's selected Network IDs.
	$selected_effiliation_ids = array_intersect( $effiliation_network_ids, $selected_network_ids );

	// If there are no selected Effiliation IDs, return.
	if ( empty( $selected_effiliation_ids ) ) {
		return;
	}

	$results = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '%_transient_effiliation_%' AND option_name != '_transient_effiliation_affiliate_ids' " );

	// No results so nothing to worry about. Return false.
	if ( empty( $results ) ) {
		return;
	}

	$unapproved_merchant_ids = [];

	foreach ( $results as $row ) {

		if ( 'dfrapi_unapproved_effiliation_merchant' !== $row->option_value ) {
			continue;
		}

		$option_name = $row->option_name; // Example: _transient_effiliation_46627

		$unapproved_merchant_ids[] = absint( dfrapi_str_after_last( $option_name, '_' ) );
	}

	$unapproved_merchant_ids = array_filter( array_unique( $unapproved_merchant_ids ) );

	if ( empty( $unapproved_merchant_ids ) ) {
		return;
	}
	$merchants = dfrapi_api_get_merchants_by_id( $unapproved_merchant_ids );

	if ( empty( $merchants ) ) {
		return;
	}

	$merchant_source = array_column( $merchants, 'source' );
	$merchant_name   = array_column( $merchants, 'name' );
	array_multisort(
		$merchant_source, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE,
		$merchant_name, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE,
		$merchants
	);

	$list = '';
	foreach ( $merchants as $merchant ) {
		$list .= sprintf(
			'<br />- %1$s: <strong>%2$s</strong> <small>(%3$d)</small>',
			esc_html( $merchant['source'] ),
			esc_html( $merchant['name'] ),
			absint( $merchant['_id'] )
		);
	}

	$status  = 'error';
	$plugin  = 'Datafeedr API';
	$heading = __( 'Unapproved Effiliation Merchants Selected', 'datafeedr-api' );
	$message = sprintf(
		__( 'You have selected one or more Effiliation merchants who have not approved your publisher account:<br />%1$s<br /><br />Please go to <a href="%2$s">Datafeedr API > Merchants</a> and remove these merchants from your selected list of Merchants. Then go here <a href="%3$s">Datafeedr API > Tools</a> and click the <strong>[Delete Cached API Data]</strong> button.', 'datafeedr-api' ),
		$list,
		esc_url( dfrapi_merchants_page_url() ),
		esc_url( dfrapi_tools_page_url() )
	);

	dfrapi_admin_notice( $message, $status, $heading, $plugin );
}

add_action( 'admin_notices', 'dfrapi_unapproved_effiliation_merchants_selected' );

