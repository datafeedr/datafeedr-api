<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search_Options {

	/**
	 * @return Dfrapi_Search_Option_Interface[]
	 * @todo maybe add apply_filters here so this can be extended.
	 */
	public static function all(): array {
		return [
			Dfrapi_Sort_Option::class,
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

}