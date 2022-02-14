<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Dfrapi_Env' ) ) {

	/**
	 * Check environment and print errors..
	 */
	class Dfrapi_Env {

		public function __construct() {
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

	} // class Dfrapi_Env

} // class_exists check
