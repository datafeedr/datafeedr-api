<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Env' ) ) {

	/**
	 * Check environment and print errors..
	 */
	class Dfrapi_Env {

		public function __construct() {

			// Non-cascading Errors
			if ( $msg = self::unapproved_ph_merchants_exist() ) {
				dfrapi_admin_messages( 'unapproved_ph_merchants', $msg );
			}

			if ( $msg = self::unapproved_effiliation_merchants_exist() ) {
				dfrapi_admin_messages( 'unapproved_effiliation_merchants', $msg );
			}
		}

		public static function api_keys_exist(): bool {
			_deprecated_function( __METHOD__, '1.3.0', 'dfrapi_datafeedr_api_keys_exist()' );

			return dfrapi_datafeedr_api_keys_exist();
		}

		public static function network_is_selected(): bool {
			_deprecated_function( __METHOD__, '1.3.0', 'dfrapi_user_has_selected_networks()' );

			return dfrapi_user_has_selected_networks();
		}

		public static function merchant_is_selected(): bool {
			_deprecated_function( __METHOD__, '1.3.0', 'dfrapi_user_has_selected_merchants()' );

			return dfrapi_user_has_selected_merchants();
		}

		public static function usage_over_90_percent(): bool {
			_deprecated_function( __METHOD__, '1.3.0', 'dfrapi_api_usage_over_90_percent()' );

			return dfrapi_api_usage_over_90_percent();
		}

		public static function missing_affiliate_ids(): bool {
			_deprecated_function( __METHOD__, '1.3.0', 'dfrapi_user_is_missing_affiliate_ids()' );

			return dfrapi_user_is_missing_affiliate_ids();
		}

		static function unapproved_ph_merchants_exist() {

			global $wpdb;

			// Get range of Partnerize Network IDs
			$ph_network_ids = dfrapi_get_partnerize_network_ids();

			// Get user's currently selected networks and convert into simple array of IDs.
			$selected_networks    = get_option( 'dfrapi_networks', array( 'ids' => array() ) );
			$selected_network_ids = array_keys( $selected_networks['ids'] );

			// See if any Partnerize Network IDs are found in the user's selected network IDs.
			$selected_ph_ids = array_intersect( $ph_network_ids, $selected_network_ids );

			// If there are no selected Partnerize IDs, return.
			if ( empty( $selected_ph_ids ) ) {
				return false;
			}

			$results = $wpdb->get_results(
				"SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '%_transient_camref_%' ",
				OBJECT
			);

			// No results so nothing to worry about. Return false.
			if ( empty( $results ) ) {
				return false;
			}

			$unapproved_merchants = array();

			foreach ( $results as $row ) {

				if ( 'dfrapi_unapproved_ph_merchant' != $row->option_value ) {
					continue;
				}

				$name = $row->option_name; // Example: _transient_camref_46627
				$name = str_replace( '_transient_camref_', '', $name );
				$name = explode( '_', $name );
				$name = array_filter( $name );

				$merchant_id = absint( $name[0] );

				$unapproved_merchants[ $merchant_id ] = $merchant_id;
			}

			if ( empty( $unapproved_merchants ) ) {
				return false;
			}

			$unapproved_merchant_ids = array_keys( $unapproved_merchants );

			$merchants = dfrapi_api_get_merchants_by_id( $unapproved_merchant_ids );

			$msg = '';

			foreach ( $merchants as $merchant ) {
				$mid = absint( $merchant['_id'] );
				$msg .= '<br>- <strong>' . $merchant['name'] . '</strong>';
			}

			return $msg;
		}

		static function unapproved_effiliation_merchants_exist() {

			global $wpdb;

			// Get range of Effiliation Network IDs
			$effiliation_network_ids = dfrapi_get_effiliation_network_ids();

			// Get user's currently selected networks and convert into simple array of IDs.
			$selected_networks    = get_option( 'dfrapi_networks', array( 'ids' => array() ) );
			$selected_network_ids = array_keys( $selected_networks['ids'] );

			// See if any Effiliation Network IDs are found in the user's selected network IDs.
			$selected_effiliation_ids = array_intersect( $effiliation_network_ids, $selected_network_ids );

			// If there are no selected Effiliation IDs, return.
			if ( empty( $selected_effiliation_ids ) ) {
				return false;
			}

			$results = $wpdb->get_results(
				"SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '%_transient_effiliation_%' AND option_name != '_transient_effiliation_affiliate_ids' ",
				OBJECT
			);

			// No results so nothing to worry about. Return false.
			if ( empty( $results ) ) {
				return false;
			}

			$unapproved_merchants = array();

			foreach ( $results as $row ) {

				if ( 'dfrapi_unapproved_effiliation_merchant' != $row->option_value ) {
					continue;
				}

				$name = $row->option_name; // Example: _transient_effiliation_46627
				$name = str_replace( '_transient_effiliation_', '', $name );
				$name = explode( '_', $name );
				$name = array_filter( $name );

				$merchant_id = absint( $name[0] );

				$unapproved_merchants[ $merchant_id ] = $merchant_id;
			}

			if ( empty( $unapproved_merchants ) ) {
				return false;
			}

			$unapproved_merchant_ids = array_keys( $unapproved_merchants );

			$merchants = dfrapi_api_get_merchants_by_id( $unapproved_merchant_ids );

			$msg = '';

			foreach ( $merchants as $merchant ) {
				$mid = absint( $merchant['_id'] );
				$msg .= '<br>- <strong>' . $merchant['name'] . '</strong>';
			}

			return $msg;
		}

	} // class Dfrapi_Env

} // class_exists check
