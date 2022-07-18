<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search_Filters {

	/**
	 * @return array
	 */
	public static function all(): array {
		// @todo maybe add apply_filters here so this can be extended.
		return [
			'any'     => new Dfrapi_Any_Search_Filter,
			'name'    => new Dfrapi_Name_Search_Filter,
			'id'      => new Dfrapi_Id_Search_Filter,
			'barcode' => new Dfrapi_Barcode_Search_Filter,
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
		return array_keys( self::all() );
	}

	public static function get_filter_instance( string $key, string $param ): ?Dfrapi_Search_Filter_Interface {
		$filters = self::all();

		return array_key_exists( $key, $filters ) ? new $filters[ $key ]( $param ) : null;
	}

	public static function factory( $param ): ?Dfrapi_Search_Filter_Interface {

		if ( is_array( $param ) ) {
			$param = sprintf(
				'%s %s %s',
				$param['field'],
				$param['operator'] ?? '',
				$param['value'] ?? ''
			);
		}

		if ( is_string( $param ) ) {

			$param = strtolower( trim( $param ) );

			if ( empty( $param ) ) {
				return null;
			}

			$key = dfrapi_str_before( $param, ' ' );

			return self::get_filter_instance( $key, $param );
		}

		return null;
	}

	public static function parse( array $params ): array {

		$filters = [];
		$params  = array_filter( array_unique( array_map( 'trim', $params ) ) );

		foreach ( $params as $param ) {

			$filters[] = self::factory( $param );

//			$filter = is_array( $param )
//				? self::get_filter_instance_from_array( $param )
//				: self::get_filter_instance_from_string( $param );

//			$filters = array_filter( $filters );


//			$param = strtolower( $param ); // Ex. name, brand, merchant_id
//			if ( dfrapi_str_starts_with( $param, $names ) ) {
//				$filter = self::get_filter_instance( $param );
//				if ( $filter ) {
//					$filters[] = $filter->format( $param );
//				}
//			}
		}

		return $filters;
	}


	public function get_field( string $field_name ) {
		return array_values( array_filter( self::all(), static function ( Dfrapi_Search_Filter_Interface $k ) use ( $field_name ) {
				return $k->name() === $field_name;
			} ) )[0] ?? null;
	}
}