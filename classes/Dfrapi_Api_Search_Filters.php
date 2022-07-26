<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search_Filters {

	/**
	 * @return Dfrapi_Search_Filter_Interface[]
	 * @todo maybe add apply_filters here so this can be extended.
	 */
	public static function all(): array {
		return [
			Dfrapi_Any_Search_Filter::class,
			Dfrapi_Name_Search_Filter::class,
			Dfrapi_Id_Search_Filter::class,
			Dfrapi_Barcode_Search_Filter::class,
		];
	}

	/**
	 * An array of all filter names.
	 *
	 *  Example:
	 *  [
	 *      "any",
	 *      "name",
	 *      "id",
	 *      "barcode",
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
	 * @param string $key Example: any, name, brand, etc...
	 *
	 * @return string|null Example: 'Dfrapi_Name_Search_Filter'
	 */
	public static function get_filter_class_name_by_key( string $key ): ?string {
		$key = strtolower( trim( $key ) );

		return array_values( array_filter( self::all(), static function ( $filter ) use ( $key ) {
				return strtolower( $filter::name() ) === $key;
			} ) )[0] ?? null;
	}

	/**
	 * Returns a fully initialized Dfrapi_Search_Filter_Abstract child class for the $param or null
	 * if no class is found.
	 *
	 * @param mixed $param Expects a string or array.
	 *
	 * @return Dfrapi_Search_Filter_Abstract|null
	 */
	public static function factory( $param ): ?Dfrapi_Search_Filter_Abstract {

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

		return $class_name ? new $class_name( $param ) : null;
	}

	/**
	 * Parse an array of $params creating an array of Filters for all filters (but not options)
	 * in the $params.
	 *
	 * @param array $params
	 *
	 * @return Dfrapi_Search_Filter_Abstract[]
	 */
	public static function parse( array $params ): array {

		$filters = [];
		$params  = array_filter( array_unique( array_map( 'trim', $params ) ) );

		foreach ( $params as $param ) {
			$filters[] = self::factory( $param );
		}

		return array_filter( $filters );
	}
}