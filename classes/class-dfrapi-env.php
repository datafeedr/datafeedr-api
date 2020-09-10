<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Env' ) ) {

	/**
	 * Check environment and print errors..
	 */
	class Dfrapi_Env {

		function __construct() { 
		
			// Cascading Errors
			if ( !self::api_keys_exist() ) {
				dfrapi_admin_messages( 'missing_api_keys' );
			} elseif ( !self::network_is_selected() ) {
				dfrapi_admin_messages( 'missing_network_ids' );
			} elseif ( !self::merchant_is_selected() ) {
				dfrapi_admin_messages( 'missing_merchant_ids' );
			}
		
			// Non-cascading Errors
			if ( self::usage_over_90_percent() ) {
				dfrapi_admin_messages( 'usage_over_90_percent' );
			}
			
			if ( self::missing_affiliate_ids() ) {
				dfrapi_admin_messages( 'missing_affiliate_ids' );
			}
						
			if ( $msg = self::unapproved_zanox_merchants_exist() ) {
				dfrapi_admin_messages( 'unapproved_zanox_merchants', $msg );
			}

			if ( $msg = self::unapproved_ph_merchants_exist() ) {
				dfrapi_admin_messages( 'unapproved_ph_merchants', $msg );
			}

			if ( $msg = self::unapproved_effiliation_merchants_exist() ) {
				dfrapi_admin_messages( 'unapproved_effiliation_merchants', $msg );
			}
		}

		static function api_keys_exist() {
			
			$configuration = (array) get_option( 'dfrapi_configuration' );
			$access_id = false;
			$secret_key = false;
			
			if ( isset( $configuration['access_id'] ) && ( $configuration['access_id'] != '' ) ) {
				$access_id = $configuration['access_id'];
			}
	
			if ( isset( $configuration['secret_key'] ) && ( $configuration['secret_key'] != '' ) ) {
				$secret_key = $configuration['secret_key'];
			}
			
			if ( $access_id && $secret_key ) {
				return true;
			}
			
			return false;
		}
	
		static function network_is_selected() {
			$networks = (array) get_option( 'dfrapi_networks' );
			if ( !empty( $networks['ids'] ) ) {
				return true;
			}
			return false;
		}
	
		static function merchant_is_selected() {
			$merchants = (array) get_option( 'dfrapi_merchants' );
			if ( !empty( $merchants['ids'] ) ) {
				return true;
			}
			return false;
		}
		
		static function usage_over_90_percent() {
			$percentage = dfrapi_get_api_usage_percentage();
			if ( $percentage >= 90 ) {
				return true;
			}
			return false;
		}

		static function missing_affiliate_ids() {
			$networks = get_option( 'dfrapi_networks', array() );
			if ( ! empty( $networks ) ) {
				foreach ( $networks['ids'] as $network ) {

					if (
						'801' == $network['nid'] ||
						'811' == $network['nid'] ||
						'812' == $network['nid'] ||
						'813' == $network['nid'] ||
						'814' == $network['nid'] ||
						'815' == $network['nid'] ||
						'816' == $network['nid'] ||
						'817' == $network['nid'] ||
						'818' == $network['nid']
					) {
						continue; // Partnerize does not have affiliate IDs.
					}

					if ( '805' == $network['nid'] || '806' == $network['nid'] || '807' == $network['nid'] ) {
						continue; // Effiliation does not have affiliate IDs.
					}

					if ( empty( $network['aid'] ) ) {
						return true;
					}
				}
			}

			return false;
		}
			
		static function unapproved_zanox_merchants_exist() {

			global $wpdb;

			// Get range of Zanox Network IDs
			$zanox_network_ids = range( 401, 430 );

			// Get user's currently selected networks and convert into simple array of IDs.
			$selected_networks    = get_option( 'dfrapi_networks', array( 'ids' => array() ) );
			$selected_network_ids = array_keys( $selected_networks['ids'] );

			// See if any Zanox Network IDs are found in the user's selected network IDs.
			$selected_zanox_ids = array_intersect( $zanox_network_ids, $selected_network_ids );

			// If there are no selected Zanox IDs, return.
			if ( empty( $selected_zanox_ids ) ) {
				return false;
			}

			$results = $wpdb->get_results(
				"SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '%_transient_zmid_%' ",
				OBJECT
			);

			// No results so nothing to worry about. Return false.
			if ( empty( $results ) ) {
				return false;
			}

			$unapproved_merchants = array();

			foreach ( $results as $row ) {

				if ( 'dfrapi_unapproved_zanox_merchant' != $row->option_value ) {
					continue;
				}

				$name = $row->option_name; // Example: _transient_zmid_46627_1264955
				$name = str_replace( '_transient_zmid_', '', $name );
				$name = explode( '_', $name );
				$name = array_filter( $name );

				$merchant_id = absint( $name[0] );
				$adspace_id  = ( isset( $name[1] ) ) ? absint( $name[1] ) : '_empty_';

				$unapproved_merchants[ $merchant_id ] = $adspace_id;
			}

			if ( empty( $unapproved_merchants ) ) {
				return false;
			}

			$unapproved_merchant_ids = array_keys( $unapproved_merchants );

			$merchants = dfrapi_api_get_merchants_by_id( $unapproved_merchant_ids );

			$msg = '';

			foreach ( $merchants as $merchant ) {
				$mid = absint( $merchant['_id'] );
				$msg .= '<br>- <strong>' . $merchant['name'] . '</strong> has not approved the Adspace ID: <strong>' . $unapproved_merchants[ $mid ] . '</strong>';
			}

			return $msg;
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
