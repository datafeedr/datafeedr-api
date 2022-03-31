<?php

defined( 'ABSPATH' ) || exit;

$product = [
	'name'        => 'Canon T7i Rebel EOS 800D',
	'price'       => 12399,
	'brand'       => 'Canon',
	'merchant'    => 'B&H',
	'desc'        => 'Great camera, good price.',
	'weight'      => '1.5lbs',
	'color'       => 'silver/black',
	'size'        => '10,20,30',
	'merchant_id' => 12345,
	'source_id'   => 4,
];

//$product = new Datafeedr_Product( $product );
//$product->get_field( 'asdf' )->with_preferred_terms( '', '' )->delimit( [ '' ] )->value();

class Datafeedr_Product {

	/**
	 * @var array $data An array of a Datafeedr Product as returned from Datafeedr API.
	 */
	public $data;

	/**
	 * @var array|string $fields
	 */
	public $fields;

	/**
	 * @var int $merchant_id ;
	 */
	public $merchant_id;

	/**
	 * @var int $network_id ;
	 */
	public $network_id;

	/**
	 * Class constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data ) {
		$this->data        = $data;
		$this->merchant_id = absint( $this->data['merchant_id'] ?? 0 );
		$this->network_id  = absint( $this->data['source_id'] ?? 0 );
	}

	public function fields( $fields ): Datafeedr_Product_Fields {
		return new Datafeedr_Product_Fields( $this->data, $fields );
	}




	public function get_brand( string $preferred = null, array $variations = [], $default = null ) {

	}

	/**
	 * Return the value for the $field or $default if the $field does not exist.
	 *
	 * @param string $field The field to return a value for. Example: 'barcode'
	 * @param mixed $default Value to return if no $field is found.
	 *
	 * @return mixed
	 */
	public function get_field( string $field, $default = null ) {
		return $this->data[ $field ] ?? $default;
	}

	/**
	 * Returns an array of values for the given $fields.
	 *
	 * @param array $fields An array of fields to return.
	 * @param string $return The format in which to return the values. Default: just values.
	 * @param bool $include_empty Whether to include "null" values in the returned array. Default: false.
	 *
	 * @return array An array of values either just values, just keys or the full associative array.
	 */
	public function get_fields( array $fields, string $return = 'values', bool $include_empty = false ): array {

		$return = in_array( $return, [ 'values', 'assoc', 'keys' ], true ) ? $return : 'values';

		$values = [];

		foreach ( $fields as $field ) {
			$value = $this->get_field( $field );

			if ( $value !== null || $include_empty ) {
				$values[ $field ] = $value;
			}
		}

		if ( $return === 'assoc' ) {
			return $values;
		}

		if ( $return === 'keys' ) {
			return array_keys( $values );
		}

		return array_values( $values );
	}

	/**
	 * Returns multiple $fields concatenated together with the $separator.
	 *
	 * @param string $fields A "period-separated" list of fields to return. Example: color.size
	 * @param string $separator The separator to use between each value found. Example: / - "blue / large"
	 * @param mixed $default Value to return if no $fields are found.
	 *
	 * @return mixed
	 */
	public function get_concatenated_fields( string $fields, string $separator = ' ', $default = null ) {
		$values = $this->get_fields( array_filter( explode( '.', $fields ) ) );

		return ! empty( $values ) ? implode( $separator, $values ) : $default;
	}

	/**
	 * Returns the first field found from an array of $fields.
	 *
	 * @param array $fields An array of fields. Example: ['barcode', 'sku'].
	 * @param mixed $default Value to return if no $fields are found.
	 *
	 * @return mixed
	 */
	public function get_first_field( array $fields, $default = null ) {
		foreach ( $fields as $field ) {
			$value = $this->get_field( $field );
			if ( $value !== null ) {
				return $value;
			}
		}

		return $default;
	}
}
