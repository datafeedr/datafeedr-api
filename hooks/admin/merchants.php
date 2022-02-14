<?php

defined( 'ABSPATH' ) || exit;

/**
 * @param array $merchants
 * @param array $network
 *
 * @return array|WP_Error|null
 */
function dfrapi_remove_unapproved_awin_merchants( $merchants, $network ) {

	// Return if not in Awin network.
	if ( 10006 != $network['group_id'] ) {
		return $merchants;
	}

	$affiliate_id = dfrapi_get_affiliate_and_tracking_id( $network['_id'], 'aid' );

	if ( is_wp_error( $affiliate_id ) ) {
		return new WP_Error(
			'missing_awin_affiliate_id',
			'Please enter your Awin affiliate ID for ' . esc_html( $network['name'] ) . ' <a href="' . admin_url( 'admin.php?page=dfrapi_networks' ) . '#group_affiliatewindow" target="_blank">here</a>.'
		);
	}

	static $awin_access_token = null;

	if ( null === $awin_access_token ) {

		$config = get_option( 'dfrapi_configuration', [] );

		$awin_access_token = ( isset( $config['awin_access_token'] ) && ! empty( $config['awin_access_token'] ) ) ?
			trim( $config['awin_access_token'] ) :
			new WP_Error(
				'awin_access_token_missing',
				'Please enter your Awin API Token <a href="' . admin_url( 'admin.php?page=dfrapi' ) . '" target="_blank">here</a>.'
			);
	}

	if ( is_wp_error( $awin_access_token ) ) {
		return $awin_access_token;
	}

	$url                  = null;
	$approved_program_ids = null;

	$url = sprintf(
		'https://api.awin.com/publishers/%1$s/programmes?relationship=joined&accessToken=%2$s',
		$affiliate_id, $awin_access_token
	);

	$response = wp_remote_get( $url );

	if ( is_array( $response ) && ! is_wp_error( $response ) ) {
		if ( isset( $response['response']['code'] ) && '200' == $response['response']['code'] ) {
			if ( isset( $response['body'] ) ) {
				$programs             = json_decode( $response['body'], true );
				$approved_program_ids = wp_list_pluck( $programs, 'id' );
			}
		}
	}

	if ( null === $approved_program_ids ) {
		return new WP_Error(
			'unable_to_retrieve_approved_awin_program_ids',
			'Unable to get your list of joined ' . esc_html( $network['name'] ) . ' programs. Please ensure your Awin Access Token is correct <a href="' . admin_url( 'admin.php?page=dfrapi' ) . '" target="_blank">here</a> and your affiliate ID is correct <a href="' . admin_url( 'admin.php?page=dfrapi_networks' ) . '#group_affiliatewindow" target="_blank">here</a>.' );
	}

	foreach ( $merchants as $key => $merchant ) {

		$approved = false;
		$suids    = isset( $merchant['suids'] ) ? explode( ',', $merchant['suids'] ) : [];

		foreach ( $suids as $suid ) {
			if ( in_array( $suid, $approved_program_ids ) ) {
				$approved = true;
			}
		}

		if ( ! $approved ) {
			unset( $merchants[ $key ] );
		}
	}

	return $merchants;
}

add_filter( 'dfrapi_list_merchants', 'dfrapi_remove_unapproved_awin_merchants', 10, 2 );

/**
 * @param array $merchants
 * @param array $network
 *
 * @return array|WP_Error|null
 * @since 1.0.102
 */
function dfrapi_disable_affiliate_gateway_merchant_selection_when_sid_empty( $merchants, $network ) {

	// Return if not in The Affiliate Gateway network.
	if ( 10033 != $network['group_id'] ) {
		return $merchants;
	}

	$sid = dfrapi_get_affiliate_gateway_sid();

	if ( is_wp_error( $sid ) ) {
		return $sid;
	}

	return $merchants;
}

add_filter( 'dfrapi_list_merchants', 'dfrapi_disable_affiliate_gateway_merchant_selection_when_sid_empty', 10, 2 );

/**
 * @param array $merchants
 * @param array $network
 *
 * @return array|WP_Error|null
 * @since 1.0.124
 */
function dfrapi_disable_belboon_merchant_selection_when_aid_empty( $merchants, $network ) {

	// Return if not in Belboon network.
	if ( 10007 != $network['group_id'] ) {
		return $merchants;
	}

	$aid = dfrapi_get_belboon_adspace_id();

	if ( is_wp_error( $aid ) ) {
		return $aid;
	}

	return $merchants;
}

add_filter( 'dfrapi_list_merchants', 'dfrapi_disable_belboon_merchant_selection_when_aid_empty', 10, 2 );