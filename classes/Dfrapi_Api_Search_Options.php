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
	 * An array of all option names.
	 *
	 *  Example:
	 *  [
	 *      "sort",
	 *      "offset",
	 *      "limit",
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

	/**
	 * Returns the class name from self::all() for the given $key or null if no class is found.
	 *
	 * @param string $key Example: sort, limit, offset, etc...
	 *
	 * @return string|null Example: 'Dfrapi_Sort_Option'
	 */
	public static function get_filter_class_name_by_key( string $key ): ?string {
		$key = strtolower( trim( $key ) );

		return array_values( array_filter( self::all(), static function ( $filter ) use ( $key ) {
				return strtolower( $filter::name() ) === $key;
			} ) )[0] ?? null;
	}

	/**
	 * Returns a fully initialized Dfrapi_Search_Option_Abstract child class for the $param or null
	 * if no class is found.
	 *
	 * @param mixed $param Expects a string or array.
	 * @param Dfrapi_Api_Search $search An instance of Dfrapi_Api_Search.
	 *
	 * @return Dfrapi_Search_Option_Abstract|null
	 */
	public static function factory( $param, Dfrapi_Api_Search $search ): ?Dfrapi_Search_Option_Abstract {

		// Convert $param array to a string.
		if ( is_array( $param ) ) {
			$param = sprintf( '%s %s %s',
				trim( $param['field'] ),
				trim( $param['operator'] ?? '' ),
				trim( $param['value'] ?? '' )
			);
		}

		if ( ! is_string( $param ) ) {
			return null;
		}

		$param = strtolower( trim( $param ) );

		if ( empty( $param ) ) {
			return null;
		}

		$key = dfrapi_str_before( $param, ' ' );

		$class_name = self::get_filter_class_name_by_key( $key );

		return $class_name ? new $class_name( $param, $search ) : null;
	}

}