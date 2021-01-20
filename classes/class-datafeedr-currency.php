<?php

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Currency' ) ) {

	class Dfrapi_Currency {

		/** @var string Default currency code to use when $currency_code does not exist. */
		const DEFAULT_CURRENCY_CODE = 'USD';

		/** @var string $currency_code 3-character ISO 4217 currency code. */
		private $currency_code;

		/** @var mixed $context The context we are displaying the price in. */
		private $context;

		/**
		 * Dfrapi_Currency constructor.
		 *
		 * @param string $currency_code 3-character ISO 4217 currency code.
		 * @param mixed $context Optional.
		 */
		public function __construct( $currency_code, $context = null ) {
			$this->context       = $context;
			$valid_currency_code = $this->validate_currency_code( $currency_code );
			$this->currency_code = is_wp_error( $valid_currency_code ) ? self::DEFAULT_CURRENCY_CODE : $valid_currency_code;
		}

		/**
		 * Returns the initial $context passed in the constructor.
		 *
		 * @return mixed
		 */
		public function get_context() {
			return $this->context;
		}

		/**
		 * Returns the 3-character ISO 4217 currency code (ie. USD, EUR, GBP, etc...).
		 *
		 * @return string
		 */
		public function get_currency_code() {
			return apply_filters( 'dfrapi_currency_code', $this->currency_code, $this );
		}

		/**
		 * Returns the currency symbol (ie. $, €, £, etc...) for the $this->currency_code.
		 *
		 * @return string
		 */
		public function get_currency_symbol() {
			return apply_filters( 'dfrapi_currency_symbol', $this->get_currency_field( 'symbol' ), $this );
		}

		/**
		 * Returns the currency name (ie. US Dollar, Euro, Great Britain Pound Sterling, etc..) for the $this->currency_code.
		 *
		 * @return string
		 */
		public function get_currency_name() {
			return apply_filters( 'dfrapi_currency_name', $this->get_currency_field( 'name' ), $this );
		}

		/**
		 * Returns the decimal point to use for the $this->currency_code.
		 *
		 * @return string
		 */
		public function get_decimal_point() {
			return apply_filters( 'dfrapi_currency_decimal_point', $this->get_currency_field( 'decimal_point' ), $this );
		}

		/**
		 * Returns the thousands separator to use for the $this->currency_code.
		 *
		 * @return string
		 */
		public function get_thousands_sep() {
			return apply_filters( 'dfrapi_currency_thousands_sep', $this->get_currency_field( 'thousands_sep' ), $this );
		}

		/**
		 * Returns an array of data about the $this->currency_code.
		 *
		 * @return array
		 */
		public function get_currency() {
			return self::currencies()[ $this->currency_code ];
		}

		/**
		 * Return a field from the currencies array for the $this->currency_code.
		 *
		 * This will return an empty string if the $field is not found.
		 *
		 * This function is safe to use to display data as it only returns a string no matter if $field doesn't exist.
		 *
		 * @param string $field Item in currencies array to get.
		 *
		 * @return string
		 */
		public function get_currency_field( $field ) {
			$currency = $this->get_currency();

			return ( isset( $currency[ $field ] ) ) ? $currency[ $field ] : '';
		}

		/**
		 * Returns the format that this currency should be displayed in when the price is positive or zero.
		 *
		 * @return string
		 */
		public function get_format() {
			return apply_filters( 'dfrapi_currency_format', $this->get_currency_field( 'format' ), $this );
		}

		/**
		 * Returns the format that this currency should be displayed in when the price is negative.
		 *
		 * @return string
		 */
		public function get_neg_format() {
			return apply_filters( 'dfrapi_currency_neg_format', $this->get_currency_field( '-format' ), $this );
		}

		/**
		 * Returns true if the currency code is supported. Else returns false.
		 *
		 * @param string $currency_code 3-character ISO 4217 currency code
		 *
		 * @return bool
		 */
		public function currency_code_is_supported( $currency_code ) {
			return in_array( $currency_code, self::get_supported_currency_codes() );
		}

		/**
		 * Returns a valid currency code trimmed and uppercased or WP_Error if code is invalid or unsupported.
		 *
		 * @param string $currency_code 3-character ISO 4217 currency code
		 *
		 * @return string|WP_Error Returns 3-character ISO 4217 code or WP_Error if it doesn't exist or is invalid.
		 */
		private function validate_currency_code( $currency_code ) {

			$code = strtoupper( trim( $currency_code ) );

			if ( strlen( $code ) !== 3 ) {
				return new WP_Error(
					'invalid_currency_code',
					'The currency code "' . esc_html( $currency_code ) . '" is invalid. Currency codes must be exactly 3 characters.'
				);
			}

			if ( ! $this->currency_code_is_supported( $currency_code ) ) {
				return new WP_Error(
					'unsupported_currency_code',
					'The currency code "' . esc_html( $currency_code ) . '" is unsupported.'
				);
			}

			return $code;
		}

		/**
		 * Returns an array of all currently supported Currency Codes.
		 *
		 * @return array
		 */
		public static function get_supported_currency_codes() {
			return array_keys( self::currencies() );
		}

		/**
		 * Returns an array of supported currencies and relevant data about each currency.
		 *
		 * @link https://www.thefinancials.com/Default.aspx?SubSectionID=curformat
		 * @link https://fastspring.com/blog/how-to-format-30-currencies-from-countries-all-over-the-world/
		 *
		 * @return array
		 */
		public static function currencies() {
			return apply_filters( 'dfrapi_currencies', [
				'ARS' => [
					'symbol'        => '$',
					'code'          => 'ARS',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Argentine Peso', 'datafeedr-api' ),
				],
				'AUD' => [
					'symbol'        => '$',
					'code'          => 'AUD',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Australian Dollar', 'datafeedr-api' ),
				],
				'BRL' => [
					'symbol'        => 'R$',
					'code'          => 'BRL',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Brazilian Real', 'datafeedr-api' ),
				],
				'CAD' => [
					'symbol'        => '$',
					'code'          => 'CAD',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Canadian Dollar', 'datafeedr-api' ),
				],
				'CLP' => [
					'symbol'        => '$',
					'code'          => 'CLP',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Chilean Peso', 'datafeedr-api' ),
				],
				'CNY' => [
					'symbol'        => '元',
					'code'          => 'CNY',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Chinese Yuan Renminbi', 'datafeedr-api' ),
				],
				'COP' => [
					'symbol'        => '$',
					'code'          => 'COP',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Colombian Peso', 'datafeedr-api' ),
				],
				'CHF' => [
					'symbol'        => 'fr.',
					'code'          => 'CHF',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => '’',
					'name'          => __( 'Swiss Franc', 'datafeedr-api' ),
				],
				'CZK' => [
					'symbol'        => 'Kč',
					'code'          => 'CZK',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Czech Koruna', 'datafeedr-api' ),
				],
				'DKK' => [
					'symbol'        => 'kr.',
					'code'          => 'DKK',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Danish Krone', 'datafeedr-api' ),
				],
				'EUR' => [
					'symbol'        => '€',
					'code'          => 'EUR',
					'format'        => '{symbol}{price}',
					'-format'       => '-{symbol}{price}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Euro', 'datafeedr-api' ),
				],
				'HKD' => [
					'symbol'        => 'HK$',
					'code'          => 'HKD',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Hong Kong Dollar', 'datafeedr-api' ),
				],
				'GBP' => [
					'symbol'        => '£',
					'code'          => 'GBP',
					'format'        => '{symbol}{price}',
					'-format'       => '-{symbol}{price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Great Britain Pound Sterling', 'datafeedr-api' ),
				],
				'HUF' => [
					'symbol'        => 'Ft',
					'code'          => 'HUF',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Hungarian Forint', 'datafeedr-api' ),
				],
				'INR' => [
					'symbol'        => '₹',
					'code'          => 'INR',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Indian Rupee', 'datafeedr-api' ),
				],
				'ILS' => [
					'symbol'        => '₪',
					'code'          => 'ILS',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'New Israeli Shekel', 'datafeedr-api' ),
				],
				'JPY' => [
					'symbol'        => '¥',
					'code'          => 'JPY',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Japanese Yen', 'datafeedr-api' ),
				],
				'KRW' => [
					'symbol'        => '₩',
					'code'          => 'KRW',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Korean Won', 'datafeedr-api' ),
				],
				'MYR' => [
					'symbol'        => 'RM',
					'code'          => 'MYR',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Malaysian Ringgit', 'datafeedr-api' ),
				],
				'MXN' => [
					'symbol'        => '$',
					'code'          => 'MXN',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Mexican Peso', 'datafeedr-api' ),
				],
				'NOK' => [
					'symbol'        => 'kr',
					'code'          => 'NOK',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Norwegian Krone', 'datafeedr-api' ),
				],
				'NZD' => [
					'symbol'        => '$',
					'code'          => 'NZD',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'New Zealand Dollar', 'datafeedr-api' ),
				],
				'PLN' => [
					'symbol'        => 'zł',
					'code'          => 'PLN',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Polish Zloty', 'datafeedr-api' ),
				],
				'PHP' => [
					'symbol'        => '₱',
					'code'          => 'PHP',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Philippine Peso', 'datafeedr-api' ),
				],
				'RON' => [
					'symbol'        => 'L',
					'code'          => 'RON',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Romanian Leu', 'datafeedr-api' ),
				],
				'RUB' => [
					'symbol'        => 'p.',
					'code'          => 'RUB',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Russian Ruble', 'datafeedr-api' ),
				],
				'SGD' => [
					'symbol'        => '$',
					'code'          => 'SGD',
					'format'        => '{symbol}{price}',
					'-format'       => '-{symbol}{price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Singapore Dollar', 'datafeedr-api' ),
				],
				'ZAR' => [
					'symbol'        => 'R',
					'code'          => 'ZAR',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'South African Rand', 'datafeedr-api' ),
				],
				'SEK' => [
					'symbol'        => 'kr',
					'code'          => 'SEK',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Swedish Krona', 'datafeedr-api' ),
				],
				'TWD' => [
					'symbol'        => '元',
					'code'          => 'TWD',
					'format'        => '{symbol} {price}',
					'-format'       => '-{symbol} {price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'New Taiwan Dollar', 'datafeedr-api' ),
				],
				'THB' => [
					'symbol'        => '฿',
					'code'          => 'THB',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Thai Baht', 'datafeedr-api' ),
				],
				'TRY' => [
					'symbol'        => '₺',
					'code'          => 'TRY',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'Turkish Lira', 'datafeedr-api' ),
				],
				'UAH' => [
					'symbol'        => 'грн',
					'code'          => 'UAH',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => '.',
					'thousands_sep' => ' ',
					'name'          => __( 'Ukrainian Hryvnia', 'datafeedr-api' ),
				],
				'USD' => [
					'symbol'        => '$',
					'code'          => 'USD',
					'format'        => '{symbol}{price}',
					'-format'       => '-{symbol}{price}',
					'decimal_point' => '.',
					'thousands_sep' => ',',
					'name'          => __( 'US Dollar', 'datafeedr-api' ),
				],
				'VND' => [
					'symbol'        => '₫',
					'code'          => 'VND',
					'format'        => '{price} {symbol}',
					'-format'       => '-{price} {symbol}',
					'decimal_point' => ',',
					'thousands_sep' => '.',
					'name'          => __( 'Vietnamese Dong', 'datafeedr-api' ),
				],
			] );
		}
	}
}
