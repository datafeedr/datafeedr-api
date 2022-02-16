<?php

defined( 'ABSPATH' ) || exit;

/**
 * Modify affiliate ID if product is a Zanox product.
 * Replaces $affiliate_id with "zmid".
 */
function dfrapi_get_zanox_zmid( $affiliate_id, $product, $networks ) {
	if ( isset( $product['source'] ) && preg_match( "/\bZanox\b/", $product['source'] ) ) {
		$zanox        = dfrapi_api_get_zanox_zmid( $product['merchant_id'], $affiliate_id );
		$affiliate_id = ( ! isset( $zanox[0]['zmid'] ) ) ? '___MISSING___' : $zanox[0]['zmid'];
	}

	return $affiliate_id;
}

add_filter( 'dfrapi_affiliate_id', 'dfrapi_get_zanox_zmid', 10, 3 );

/**
 * Modify affiliate ID if product is a Partnerize product.
 * Replaces $affiliate_id with "camref".
 *
 * @return string Affiliate ID.
 * @since 1.0.66
 */
function dfrapi_get_ph_camref( $affiliate_id, $product, $networks ) {
	if ( isset( $product['source'] ) && preg_match( "/\bPartnerize\b/", $product['source'] ) ) {
		$ph           = dfrapi_api_get_ph_camref( $product['merchant_id'] );
		$affiliate_id = ( ! isset( $ph[0]['camref'] ) ) ? '___MISSING___' : $ph[0]['camref'];
	}

	return $affiliate_id;
}

add_filter( 'dfrapi_affiliate_id', 'dfrapi_get_ph_camref', 10, 3 );

/**
 * Modify affiliate ID if product is a Effiliation product.
 * Replaces $affiliate_id with "affiliate ID".
 *
 * @return string Affiliate ID.
 * @since 1.0.81
 */
function dfrapi_get_effiliation_affiliate_id( $affiliate_id, $product, $networks ) {
	if ( isset( $product['source'] ) && preg_match( "/\bEffiliation\b/", $product['source'] ) ) {
		$effiliation  = dfrapi_api_get_effiliation_affiliate_id( $product['merchant_id'] );
		$affiliate_id = $effiliation === 'dfrapi_unapproved_effiliation_merchant' ? '___MISSING___' : $effiliation;
	}

	return $affiliate_id;
}

add_filter( 'dfrapi_affiliate_id', 'dfrapi_get_effiliation_affiliate_id', 10, 3 );

/**
 * Insert SID into The Affiliate Gateway affiliate links.
 *
 * @param string $url
 * @param array $product
 * @param string $tracking_id
 *
 * @return string
 */
function dfrapi_insert_affiliate_gateway_sid_into_affiliate_link( $url, $product, $tracking_id ) {

	if ( $product['source'] !== 'The Affiliate Gateway' ) {
		return $url;
	}

	$sid = dfrapi_get_affiliate_gateway_sid();

	if ( is_wp_error( $sid ) ) {
		return $url;
	}

	$url = str_replace( '{SID}', $sid, $url );

	return $url;
}

add_filter( 'dfrapi_after_tracking_id_insertion', 'dfrapi_insert_affiliate_gateway_sid_into_affiliate_link', 20, 3 );

/**
 * Insert Media ID into Adservice affiliate links.
 *
 * @param string $url
 * @param array $product
 * @param string $tracking_id
 *
 * @return string
 */
function dfrapi_insert_adservice_mid_into_affiliate_link( $url, $product, $tracking_id ) {

	if ( ! dfrapi_str_contains( $product['source'], 'Adservice' ) ) {
		return $url;
	}

	$mid = dfrapi_get_adservice_mid();

	return is_wp_error( $mid ) ? $url : str_replace( '{MID}', $mid, $url );
}

add_filter( 'dfrapi_after_tracking_id_insertion', 'dfrapi_insert_adservice_mid_into_affiliate_link', 20, 3 );

/**
 * Insert Adspace ID into Belboon affiliate links.
 *
 * @param string $url
 * @param array $product
 * @param string $affiliate_id
 *
 * @return string
 * @since 1.0.124
 */
function dfrapi_insert_belboon_adspace_id_into_affiliate_link( $url, $product, $affiliate_id ): string {

	if ( strpos( $product['source'], 'Belboon' ) === false ) {
		return $url;
	}

	$aid = dfrapi_get_belboon_adspace_id();

	if ( is_wp_error( $aid ) ) {
		return $url;
	}

	$url = str_replace( '{AID}', $aid, $url );

	return $url;
}

add_filter( 'dfrapi_before_affiliate_id_insertion', 'dfrapi_insert_belboon_adspace_id_into_affiliate_link', 20, 3 );
