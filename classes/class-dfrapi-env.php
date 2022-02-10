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
			if ( self::usage_over_90_percent() ) {
				dfrapi_admin_messages( 'usage_over_90_percent' );
			}

			if ( self::missing_affiliate_ids() ) {
				dfrapi_admin_messages( 'missing_affiliate_ids' );
			}

			if ( $msg = self::unapproved_ph_merchants_exist() ) {
				dfrapi_admin_messages( 'unapproved_ph_merchants', $msg );
			}

			if ( $msg = self::unapproved_effiliation_merchants_exist() ) {
				dfrapi_admin_messages( 'unapproved_effiliation_merchants', $msg );
			}
		}

		public static function api_keys_exist(): bool {
			return dfrapi_datafeedr_api_keys_exist();
		}

		public static function network_is_selected(): bool {
			return dfrapi_selected_network_count() > 0;
		}

		public static function merchant_is_selected(): bool {
			return dfrapi_selected_merchant_count() > 0;
		}

		public static function usage_over_90_percent(): bool {
			$percentage = dfrapi_get_api_usage_percentage();

			return $percentage >= 90;
		}

		static function missing_affiliate_ids() {
			$networks = get_option( 'dfrapi_networks', array() );
			if ( ! empty( $networks ) ) {
				foreach ( $networks['ids'] as $network ) {

					$network_id = absint( $network['nid'] );

					$parternize_network_ids  = [ 801, 811, 812, 813, 814, 815, 816, 817, 818, 819, 820 ];
					$effiliation_network_ids = [ 805, 806, 807 ];

					// Partnerize does not have affiliate IDs.
					if ( in_array( $network_id, $parternize_network_ids ) ) {
						continue;
					}

					// Effiliation does not have affiliate IDs.
					if ( in_array( $network_id, $effiliation_network_ids ) ) {
						continue;
					}

					if ( empty( $network['aid'] ) ) {
						return true;
					}
				}
			}

			return false;
		}

		static function unapproved_ph_merchants_exist() {

			global $wpdb;

			// Get range of Partnerize Network IDs
			$ph_network_ids = range( 801, 802 );

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
			$effiliation_network_ids = range( 805, 807 );

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
