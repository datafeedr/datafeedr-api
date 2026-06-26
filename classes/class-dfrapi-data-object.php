<?php

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Data_Object' ) ) {

	/**
	 * Read-only object wrapper around the associative arrays returned by the Datafeedr API.
	 *
	 * Provides named getters (in subclasses) plus generic get()/has() access for the
	 * hundreds of optional, merchant-specific fields (custom1, item_group_id, etc.)
	 * which cannot have dedicated methods.
	 *
	 * This class is also THE normalization layer for the Datafeedr API: when the
	 * underlying API renames fields (e.g. "_id" => "id", "source" => "network"),
	 * updating the subclass's field_map() restores the canonical (legacy) array
	 * shape in one place instead of in every plugin that consumes the data.
	 *
	 * The canonical array shape is the legacy shape ("_id", "source", "source_id", ...).
	 * Arrays remain the compatibility format everywhere: existing dfrapi_api_get_*()
	 * functions keep returning arrays and anything persisted to the database must be
	 * the result of to_array(), never a serialized object.
	 *
	 * ArrayAccess and JsonSerializable are implemented as a safety net so an object
	 * that accidentally reaches legacy array-style code ($product['name'], isset(),
	 * wp_json_encode()) still behaves like the array it wraps. Writes via array
	 * syntax are not supported: objects are immutable.
	 *
	 * @since 1.4.2
	 */
	abstract class Dfrapi_Data_Object implements ArrayAccess, JsonSerializable {

		/** @var array $data Canonical (legacy-shaped) data. */
		protected $data = array();

		/**
		 * Dfrapi_Data_Object constructor.
		 *
		 * @param array $data Raw or canonical API data. Normalized on construction.
		 */
		public function __construct( array $data ) {
			$this->data = static::normalize( $data );
		}

		/**
		 * Map of raw-API-field => canonical-field used by normalize().
		 *
		 * @return array
		 */
		abstract protected static function field_map(): array;

		/**
		 * Name of the filter applied at the end of normalize().
		 *
		 * @return string
		 */
		abstract protected static function normalize_filter_name(): string;

		/**
		 * Convert a raw API array (current or future shape) into the canonical
		 * (legacy) array shape.
		 *
		 * For each field_map() entry the value is copied to the canonical key,
		 * never renamed, so arrays keyed by the new API names keep working too.
		 * Idempotent: canonical input passes through unchanged.
		 *
		 * @param array $raw Raw API data.
		 *
		 * @return array Canonical data.
		 */
		public static function normalize( array $raw ): array {

			foreach ( static::field_map() as $source_key => $canonical_key ) {
				if ( array_key_exists( $source_key, $raw ) && ! array_key_exists( $canonical_key, $raw ) ) {
					$raw[ $canonical_key ] = $raw[ $source_key ];
				}
			}

			return apply_filters( static::normalize_filter_name(), $raw );
		}

		/**
		 * Get the value of any field, including optional merchant-specific fields
		 * such as "custom1" or "item_group_id".
		 *
		 * @param string $field Field name.
		 * @param mixed $default Value to return when the field does not exist. Default null.
		 *
		 * @return mixed
		 */
		public function get( string $field, $default = null ) {
			return array_key_exists( $field, $this->data ) ? $this->data[ $field ] : $default;
		}

		/**
		 * Whether a field exists (even when its value is empty or null).
		 *
		 * @param string $field Field name.
		 *
		 * @return bool
		 */
		public function has( string $field ): bool {
			return array_key_exists( $field, $this->data );
		}

		/**
		 * Return the canonical array this object wraps.
		 *
		 * This is the persistence boundary: anything stored in the database
		 * (post meta, transients, custom tables) must be this array, never the object.
		 *
		 * @return array
		 */
		public function to_array(): array {
			return $this->data;
		}

		/**
		 * Get the ID.
		 *
		 * Untyped return: product IDs are strings (e.g. "8216057348792880878")
		 * while merchant and network IDs are integers.
		 *
		 * @return int|string
		 */
		public function get_id() {
			return $this->get( '_id', 0 );
		}

		/**
		 * Get the name.
		 *
		 * @return string
		 */
		public function get_name(): string {
			return (string) $this->get( 'name', '' );
		}

		/**
		 * Whether an offset exists. Mirrors isset() semantics on the wrapped array.
		 *
		 * @param mixed $offset Field name.
		 *
		 * @return bool
		 */
		#[\ReturnTypeWillChange]
		public function offsetExists( $offset ) {
			return isset( $this->data[ $offset ] );
		}

		/**
		 * Get the value at an offset. Returns null (without notice) when the field does not exist.
		 *
		 * @param mixed $offset Field name.
		 *
		 * @return mixed
		 */
		#[\ReturnTypeWillChange]
		public function offsetGet( $offset ) {
			return $this->data[ $offset ] ?? null;
		}

		/**
		 * Setting values via array syntax is not supported. Objects are immutable.
		 *
		 * @param mixed $offset Field name.
		 * @param mixed $value Value.
		 *
		 * @return void
		 */
		#[\ReturnTypeWillChange]
		public function offsetSet( $offset, $value ) {
			_doing_it_wrong(
				get_class( $this ) . '::offsetSet',
				'Datafeedr data objects are read-only. Use to_array() and modify the array instead.',
				'1.4.2'
			);
		}

		/**
		 * Unsetting values via array syntax is not supported. Objects are immutable.
		 *
		 * @param mixed $offset Field name.
		 *
		 * @return void
		 */
		#[\ReturnTypeWillChange]
		public function offsetUnset( $offset ) {
			_doing_it_wrong(
				get_class( $this ) . '::offsetUnset',
				'Datafeedr data objects are read-only. Use to_array() and modify the array instead.',
				'1.4.2'
			);
		}

		/**
		 * JSON-encodes identically to the wrapped array.
		 *
		 * @return array
		 */
		#[\ReturnTypeWillChange]
		public function jsonSerialize() {
			return $this->to_array();
		}
	}
}
