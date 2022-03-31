<?php

defined( 'ABSPATH' ) || exit;

class Datafeedr_Product_Fields {


	/**
	 * @var array $data
	 */
	public $data;

	public $fields;

	private $separator = ' ';

	/**
	 * @param array $data
	 * @param string|array $fields
	 */
	public function __construct( array $data, $fields ) {
		$this->data = $data;
		$this->set_fields( $fields );
	}

	/**
	 * 'barcode' // return string
	 * 'barcode.sku' // return string with separator
	 * ['barcode'] // return array of single field
	 * ['barcode', 'sku'] // return array of multiple fields
	 * ['barcode.sku', 'color.size'] // return array of multiple fields with separators
	 * ['barcode.sku' => ['separator' => '>'], 'color.size' => ['separator' => '/']] // return array of multiple fields separated by the specified separators
	 *
	 * @return void
	 */
	private function set_fields( $fields ) {

		$this->fields = [];

		$fields = is_array( $fields ) ? $fields : [ (string) $fields ];

		$fields = self::normalize_mixed_associative_array( $fields );

		foreach ( $fields as $field => $args ) {
			$this->handle_string_field( $field, $args );
		}
	}

	// https://stackoverflow.com/a/39181932/2489248
	public static function normalize_mixed_associative_array( array $array ): array {

		$result = [];
		$keys   = array_keys( $array );
		$values = array_values( $array );

		foreach ( $values as $i => $v ) {
			if ( is_array( $v ) ) {
				$result[ $keys[ $i ] ] = $v;
			} else {
				$result[ $v ] = [];
			}
		}

		return $result;
	}

	private function handle_string_field( string $fields, array $args = [] ) {

		$concat_fields = explode( '.', $fields );
		$concatenate   = count( $concat_fields ) > 1;

		$values = [];

		foreach ( $concat_fields as $concat_field ) {
			$values[ $concat_field ] = $this->get_field( $concat_field );
		}

		$this->fields[ $fields ] = $this->generate_fields_array( $fields, $values, $concatenate, $args );
	}

	private function generate_fields_array( string $fields, array $values, bool $concatenate, array $args ): array {
		return [
			'fields'      => $fields,
			'separator'   => $concatenate ? ( $args['separator'] ?? $this->separator ) : null,
			'concatenate' => $concatenate,
			'values'      => $values,
			'prefix'      => $args['prefix'] ?? '',
		];
	}

	public function values(): array {

		$values = [];

		foreach ( $this->fields as $field ) {

			$prefix = $field['prefix'];

			if ( $field['concatenate'] ) {
				$value = trim( implode( $field['separator'], array_values( $field['values'] ) ) );
				$value = empty( $value ) ? [ null ] : [ $prefix . $value ];
			} else {
				$value = array_filter( array_values( $field['values'] ) );
			}

			$values[] = $value;
		}

		return array_filter( $values );
	}

	public function first() {
		return $this->values()[0] ?? null;
	}

	public function value( string $field ) {

	}

	public function separate_with( string $separator ): Datafeedr_Product_Fields {
		foreach ( $this->fields as $key => $field ) {
			if ( $field['concatenate'] ) {
				$this->fields[ $key ]['separator'] = $separator;
			}

		}

		return $this;
	}

	public function get_field_with_preferred_terms() {

	}

	public function get_field( string $field, $default = null ) {
		return $this->data[ $field ] ?? $default;
	}
}