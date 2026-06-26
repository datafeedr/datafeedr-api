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

	$cache_key            = 'dfrapi_awin_joined_' . $affiliate_id;
	$cooldown_key         = 'dfrapi_awin_429_' . $affiliate_id;
	$approved_program_ids = get_transient( $cache_key );

	// Awin's Publisher API is rate-limited to 20 requests per minute per user.
	// This filter runs on every merchants-page load, so we only call Awin when
	// we have no cached program list AND we're not in a 429 cool-down window.
	// Without this, each reload re-calls Awin and keeps the rate limit tripped.
	if ( false === $approved_program_ids ) {

		if ( false !== get_transient( $cooldown_key ) ) {
			return new WP_Error(
				'awin_rate_limited',
				esc_html( $network['name'] ) . ': Awin is temporarily rate-limiting API requests (max 20 per minute). Please wait a minute or two, then reload this page.'
			);
		}

		$url = sprintf(
			'https://api.awin.com/publishers/%1$s/programmes?relationship=joined&accessToken=%2$s',
			$affiliate_id, $awin_access_token
		);

		$response = wp_remote_get( $url, [ 'timeout' => 15 ] );
		$code     = (int) wp_remote_retrieve_response_code( $response );

		if ( ! is_wp_error( $response ) && 200 === $code ) {
			$programs = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( is_array( $programs ) ) {
				$approved_program_ids = wp_list_pluck( $programs, 'id' );
				set_transient( $cache_key, $approved_program_ids, 12 * HOUR_IN_SECONDS );
				dfrapi_update_transient_whitelist( $cache_key );
			}
		} elseif ( 429 === $code ) {
			// Back off so repeated page loads stop hammering Awin and keeping
			// the limit tripped. Honor Retry-After if Awin sends it.
			$retry_after = absint( wp_remote_retrieve_header( $response, 'retry-after' ) );
			set_transient( $cooldown_key, 1, max( 120, $retry_after ) );
			dfrapi_update_transient_whitelist( $cooldown_key );
			return new WP_Error(
				'awin_rate_limited',
				esc_html( $network['name'] ) . ': Awin is temporarily rate-limiting API requests (max 20 per minute). Please wait a minute or two, then reload this page.'
			);
		}
	}

	if ( false === $approved_program_ids ) {
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