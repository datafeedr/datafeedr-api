<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search_Operators {

	/**
	 * @return Dfrapi_Search_Operator_Interface[]
	 * @todo maybe add apply_filters here so this can be extended.
	 */
	public static function all(): array {
		return [
			Dfrapi_Like_Operator::class,
			Dfrapi_Not_Like_Operator::class,
			Dfrapi_In_Operator::class,
			Dfrapi_Not_In_Operator::class,
		];
	}

	/**
	 * An array of all operator names.
	 *
	 *  Example:
	 *  [
	 *      "LIKE",
	 *      "NOT_LIKE",
	 *      "IN",
	 *      "NOT_IN",
	 *      ...
	 *  ]
	 *
	 * @return array
	 */
	public static function names(): array {
		return array_map( static function ( $filter ) {
			return $filter::name();
		}, self::all() );
	}

	public static function factory( Dfrapi_Search_Filter_Abstract $filter ): ?Dfrapi_Search_Operator_Abstract {
		$class_name = self::get_operator_class_name_by_operator(
			self::extract_operator_from_param( $filter::name(), $filter->param )
		);

		return $class_name ? new $class_name() : null;
	}

	/**
	 * @param string $field Example: any, name, brand
	 * @param string $param Example: any like patagonia, name like puff
	 *
	 * @return string
	 */
	public static function extract_operator_from_param( string $field, string $param ): string {

		$field = strtolower( trim( $field ) );

		$param = strtolower( trim( $param ) );
		$param = trim( ltrim( $param, $field ) );

		return strtoupper( trim( dfrapi_str_before( $param, ' ' ) ) );
	}

	/**
	 * Returns the class name from self::all() for the given $operator or null if no class is found.
	 *
	 * @param string $operator
	 *
	 * @return string|null Example: 'Dfrapi_Like_Operator'
	 */
	public static function get_operator_class_name_by_operator( string $operator ): ?string {
		$operator = strtoupper( trim( $operator ) );

		return array_values( array_filter( self::all(), static function ( $filter ) use ( $operator ) {
				return strtoupper( $filter::name() ) === $operator;
			} ) )[0] ?? null;
	}


//	public const IN = 'IN';
//	public const NOT_IN = 'NOT_IN';
//	public const LIKE = 'LIKE';
//	public const NOT_LIKE = 'NOT_LIKE';
//	public const EMPTY = 'EMPTY';
//	public const NOT_EMPTY = 'NOT_EMPTY';
//	public const GTE = 'GTE';
//	public const GT = 'GT';
//	public const LTE = 'LTE';
//	public const LT = 'LT';
//	public const EQ = 'EQ';
//	public const NOT_EQ = 'NOT_EQ';
//	public const ALL = 'ALL';
//	public const LIKE_ALL = 'LIKE_ALL';
//	public const COUPONS = 'COUPONS';
//	public const PRODUCTS = 'PRODUCTS';
//	public const ALL_COUPONS = 'ALL_COUPONS';
//	public const ALL_PRODUCTS = 'ALL_PRODUCTS';
//	public const LIKE_COUPONS = 'LIKE_COUPONS';
//	public const LIKE_PRODUCTS = 'LIKE_PRODUCTS';
//	public const LIKE_ALL_COUPONS = 'LIKE_ALL_COUPONS';
//	public const LIKE_ALL_PRODUCTS = 'LIKE_ALL_PRODUCTS';

//	public function get_operator( string $operator ): ?string {
//		return $this->map()[ $operator ] ?? null;
//	}
//
//	private function map() {
//		return [
//			self::IN                => 'IN',
//			self::NOT_IN            => '!IN',
//			self::LIKE              => 'LIKE',
//			self::NOT_LIKE          => '!LIKE',
//			self::EMPTY             => 'EMPTY',
//			self::NOT_EMPTY         => '!EMPTY',
//			self::GTE               => '>=',
//			self::GT                => '>',
//			self::LTE               => '<=',
//			self::LT                => '<',
//			self::EQ                => '=',
//			self::NOT_EQ            => '!=',
//			self::ALL               => '',
//			self::LIKE_ALL          => '',
//			self::COUPONS           => '',
//			self::PRODUCTS          => '',
//			self::ALL_COUPONS       => '',
//			self::ALL_PRODUCTS      => '',
//			self::LIKE_COUPONS      => '',
//			self::LIKE_PRODUCTS     => '',
//			self::LIKE_ALL_COUPONS  => '',
//			self::LIKE_ALL_PRODUCTS => '',
//		];
//	}


}