<?php

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Datafeedr API Plugins settings and configuration to the WordPress
 * Site Health Info section (WordPress Admin Area > Tools > Site Health).
 *
 * @return array
 */
add_filter( 'debug_information', function ( $info ) {

	$options      = get_option( 'dfrapi_configuration', [] );
	$networks     = (array) get_option( 'dfrapi_networks', [] );
	$network_ids  = isset( $networks['ids'] ) ? array_keys( (array) $networks['ids'] ) : [];
	$merchants    = (array) get_option( 'dfrapi_merchants', [] );
	$merchant_ids = isset( $merchants['ids'] ) ? (array) $merchants['ids'] : [];

	$info['datafeedr-api-plugin'] = [
		'label'       => __( 'Datafeedr API Plugin', 'datafeedr-api' ),
		'description' => '',
		'fields'      => [
			'access_id'                => [
				'label' => __( 'Datafeedr Access ID', 'datafeedr-api' ),
				'value' => isset( $options['access_id'] ) && ! empty( $options['access_id'] ) ? '••••' . substr( $options['access_id'], - 4 ) : '—',
				'debug' => isset( $options['access_id'] ) && ! empty( $options['access_id'] ) ? '••••' . substr( $options['access_id'], - 4 ) : '—',
			],
			'secret_key'               => [
				'label' => __( 'Datafeedr Secret Key', 'datafeedr-api' ),
				'value' => isset( $options['secret_key'] ) && ! empty( $options['secret_key'] ) ? '••••' . substr( $options['secret_key'], - 4 ) : '—',
				'debug' => isset( $options['secret_key'] ) && ! empty( $options['secret_key'] ) ? '••••' . substr( $options['secret_key'], - 4 ) : '—',
			],
			'awin_access_token'        => [
				'label' => __( 'Awin API Token', 'datafeedr-api' ),
				'value' => isset( $options['awin_access_token'] ) && ! empty( $options['awin_access_token'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['awin_access_token'] ) && ! empty( $options['awin_access_token'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'affiliate_gateway_sid'    => [
				'label' => __( 'The Affiliate Gateway SID', 'datafeedr-api' ),
				'value' => isset( $options['affiliate_gateway_sid'] ) && ! empty( $options['affiliate_gateway_sid'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['affiliate_gateway_sid'] ) && ! empty( $options['affiliate_gateway_sid'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'belboon_aid'              => [
				'label' => __( 'Belboon Adspace ID', 'datafeedr-api' ),
				'value' => isset( $options['belboon_aid'] ) && ! empty( $options['belboon_aid'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['belboon_aid'] ) && ! empty( $options['belboon_aid'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'amazon_access_key_id'     => [
				'label' => __( 'Amazon Access Key ID', 'datafeedr-api' ),
				'value' => isset( $options['amazon_access_key_id'] ) && ! empty( $options['amazon_access_key_id'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['amazon_access_key_id'] ) && ! empty( $options['amazon_access_key_id'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'amazon_secret_access_key' => [
				'label' => __( 'Amazon Secret Access Key', 'datafeedr-api' ),
				'value' => isset( $options['amazon_secret_access_key'] ) && ! empty( $options['amazon_secret_access_key'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['amazon_secret_access_key'] ) && ! empty( $options['amazon_secret_access_key'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'amazon_tracking_id'       => [
				'label' => __( 'Amazon Tracking ID', 'datafeedr-api' ),
				'value' => isset( $options['amazon_tracking_id'] ) && ! empty( $options['amazon_tracking_id'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['amazon_tracking_id'] ) && ! empty( $options['amazon_tracking_id'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'amazon_locale'            => [
				'label' => __( 'Amazon Locale', 'datafeedr-api' ),
				'value' => strtoupper( $options['amazon_locale'] ?? '—' ),
				'debug' => $options['amazon_locale'] ?? '—',
			],
			'ph_application_key'       => [
				'label' => __( 'Partnerize Application Key', 'datafeedr-api' ),
				'value' => isset( $options['ph_application_key'] ) && ! empty( $options['ph_application_key'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['ph_application_key'] ) && ! empty( $options['ph_application_key'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'ph_user_api_key'          => [
				'label' => __( 'User API Key', 'datafeedr-api' ),
				'value' => isset( $options['ph_user_api_key'] ) && ! empty( $options['ph_user_api_key'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['ph_user_api_key'] ) && ! empty( $options['ph_user_api_key'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'ph_publisher_id'          => [
				'label' => __( 'Publisher ID', 'datafeedr-api' ),
				'value' => isset( $options['ph_publisher_id'] ) && ! empty( $options['ph_publisher_id'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['ph_publisher_id'] ) && ! empty( $options['ph_publisher_id'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'effiliation_key'          => [
				'label' => __( 'Effiliation Key', 'datafeedr-api' ),
				'value' => isset( $options['effiliation_key'] ) && ! empty( $options['effiliation_key'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
				'debug' => isset( $options['effiliation_key'] ) && ! empty( $options['effiliation_key'] ) ? __( 'Yes', 'datafeedr-api' ) : '—',
			],
			'hs_beacon'                => [
				'label' => __( 'Support Link Enabled', 'datafeedr-api' ),
				'value' => ucfirst( $options['hs_beacon'] ?? 'on' ),
				'debug' => $options['hs_beacon'] ?? 'on',
			],
			'selected_networks'        => [
				'label' => __( 'Selected Networks', 'datafeedr-api' ),
				'value' => absint( count( $network_ids ) ),
				'debug' => absint( count( $network_ids ) ),
			],
			'selected_network_ids'     => [
				'label' => __( 'Selected Network IDs', 'datafeedr-api' ),
				'value' => implode( ', ', $network_ids ),
				'debug' => implode( ', ', $network_ids ),
			],
			'selected_merchants'       => [
				'label' => __( 'Selected Merchants', 'datafeedr-api' ),
				'value' => absint( count( $merchant_ids ) ),
				'debug' => absint( count( $merchant_ids ) ),
			],
			'selected_merchant_ids'    => [
				'label' => __( 'Selected Merchants IDs', 'datafeedr-api' ),
				'value' => implode( ', ', $merchant_ids ),
				'debug' => implode( ', ', $merchant_ids ),
			],
		]
	];

	return $info;
} );
