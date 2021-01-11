<?php

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Price' ) ) {

	class Dfrapi_Price {

		/** @var int Default number of decimal places to use when displaying prices. */
		const DEFAULT_DECIMAL_PLACES = 2;

		/** @var bool Whether or not to trim trailing double zeros. Default false. */
		const TRIM_TRAILING_DOUBLE_ZEROS = false;

		/** @var Dfrapi_Currency $currency */
		public $currency;

		/** @var string|integer|numeric $value The original value pass to constructor to be used as the price. */
		private $value;

		/** @var mixed $context The context we are displaying the price in. */
		private $context;

		/** @var integer $int The $value converted into an integer format. */
		private $int;

		/**
		 * Dfrapi_Price constructor.
		 *
		 * @param mixed $value
		 * @param Dfrapi_Currency $currency
		 * @param mixed $context
		 */
		public function __construct( $value, Dfrapi_Currency $currency, $context = null ) {
			$this->value    = $value;
			$this->int      = dfrapi_intify( $value );
			$this->currency = $currency;
			$this->context  = $context;
		}

		/**
		 * Returns a price formatted with decimal, thousands separator, currency symbols and any other
		 * data that appears in the "format" field of the currency.
		 *
		 * @return string
		 */
		public function get_price() {
			return apply_filters( 'dfrapi_price', strtr( $this->get_signed_format(), $this->get_translation_pairs() ), $this );
		}

		/**
		 * Returns the initial $value passed in the constructor.
		 *
		 * @return float|int|string
		 */
		public function get_value() {
			return $this->value;
		}

		/**
		 * Returns the $value after its been "intified".
		 *
		 * @return int
		 */
		public function get_int() {
			return $this->int;
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
		 * Get default number of decimal places to use when formatting a price number.
		 *
		 * @return int
		 */
		public function get_decimal_places() {
			return absint( apply_filters( 'dfrapi_price_decimal_places', self::DEFAULT_DECIMAL_PLACES, $this ) );
		}

		/**
		 * Returns the format to display the price (either positive or negative).
		 *
		 * @return string
		 */
		public function get_signed_format() {
			return $this->int < 0 ? $this->currency->get_neg_format() : $this->currency->get_format();
		}

		/**
		 * Returns the $int value formatted with the proper decimal point and thousands separator.
		 *
		 * Also handles trimming of trailing zeros if required.
		 *
		 * @return integer|float
		 */
		public function get_formatted_number() {

			$formatted_number = $this->number_format();

			if ( boolval( apply_filters( 'dfrapi_price_trim_trailing_double_zeros', self::TRIM_TRAILING_DOUBLE_ZEROS, $this ) ) ) {
				$formatted_number = self::trim_trailing_double_zeros_from_formatted_number( $formatted_number );
			}

			return apply_filters( 'dfrapi_price_formatted_number', $formatted_number, $this );
		}

		/**
		 * Returns the whole/integral number (everything before decimal) of the formatted number.
		 *
		 * @return string
		 */
		public function get_whole_part() {
			return dfrapi_str_before_last( $this->get_formatted_number(), $this->currency->get_decimal_point() );
		}

		/**
		 * Returns the fractional number (everything after decimal) of the formatted number.
		 *
		 * Returns an empty string if the formatted number does not contain a decimal point.
		 *
		 * @return string
		 */
		public function get_fraction_part() {

			$formatted_number = $this->get_formatted_number();
			$decimal_point    = $this->currency->get_decimal_point();

			if ( ! dfrapi_str_contains( $formatted_number, $decimal_point ) ) {
				return '';
			}

			return dfrapi_str_after_last( $formatted_number, $decimal_point );
		}

		/**
		 * Returns a formatted version of the $this->int value.
		 *
		 * @return string
		 */
		public function number_format() {
			return number_format(
				( absint( $this->int ) / 100 ),
				$this->get_decimal_places(),
				$this->currency->get_decimal_point(),
				$this->currency->get_thousands_sep()
			);
		}

		/**
		 * Removes trailing ".00" and ",00" from a formatted number.
		 *
		 * @param string A formatted version of number.
		 *
		 * @return string A formatted version of number.
		 */
		public static function trim_trailing_double_zeros_from_formatted_number( $number ) {

			$needles = apply_filters( 'dfrapi_price_double_zero_needles', [ '.00', ',00' ], $number );

			foreach ( $needles as $needle ) {
				if ( dfrapi_ends_with( $number, $needle ) ) {
					return str_replace( $needle, '', $number );
				}
			}

			return $number;
		}

		/**
		 * Returns an array of translation pairs to use in the formatting of the price.
		 *
		 * {symbol}   Currency Symbol (ie. $, €, £, etc...)
		 * {price}    Price (formatted with decimal & thousands separator)
		 * {code}     Currency Code (ie. USD, EUR, GBP, etc...)
		 * {name}     Currency Name (ie. US Dollar, Euro, Great Britain Pound Sterling, etc...)
		 * {decimal}  The character to use as the decimal point (separating the whole number and fraction number.
		 * {whole}    The price's formatted whole number including thousands separator (everything before the decimal point).
		 * {fraction} The price's fraction (everything after the decimal point).
		 *
		 * @return array
		 */
		private function get_translation_pairs() {
			return apply_filters( 'dfrapi_price_translation_pairs',
				[
					'{code}'     => $this->currency->get_currency_code(),
					'{symbol}'   => $this->currency->get_currency_symbol(),
					'{price}'    => $this->get_formatted_number(),
					'{name}'     => $this->currency->get_currency_name(),
					'{decimal}'  => $this->currency->get_decimal_point(),
					'{whole}'    => $this->get_whole_part(),
					'{fraction}' => $this->get_fraction_part(),
				],
				$this
			);
		}
	}
}
