<?php

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Merchant' ) ) {

	/**
	 * A single Datafeedr Merchant.
	 *
	 * Create instances with the dfrapi_merchant() factory function.
	 *
	 * @since 1.4.2
	 */
	class Dfrapi_Merchant extends Dfrapi_Data_Object {

		/**
		 * Maps new API field names to their canonical (legacy) names.
		 *
		 * Empty today. Populate when the API renames merchant fields, e.g.:
		 *
		 *     return array(
		 *         'id'         => '_id',
		 *         'network'    => 'source',
		 *         'network_id' => 'source_id',
		 *     );
		 *
		 * @return array
		 */
		protected static function field_map(): array {
			return array();
		}

		/**
		 * @return string
		 */
		protected static function normalize_filter_name(): string {
			return 'dfrapi_normalize_merchant_array';
		}

		/**
		 * Get the merchant ID.
		 *
		 * @return int
		 */
		public function get_id() {
			return (int) $this->get( '_id', 0 );
		}

		/**
		 * Get the affiliate network name.
		 *
		 * @return string
		 */
		public function get_source(): string {
			return (string) $this->get( 'source', '' );
		}

		/**
		 * Get the affiliate network ID.
		 *
		 * @return int
		 */
		public function get_source_id(): int {
			return (int) $this->get( 'source_id', 0 );
		}

		/**
		 * Alias of get_source_id() matching the new API field name.
		 *
		 * @return int
		 */
		public function get_network_id(): int {
			return $this->get_source_id();
		}

		/**
		 * Alias of get_source() matching the new API field name.
		 *
		 * @return string
		 */
		public function get_network_name(): string {
			return $this->get_source();
		}

		/**
		 * Get the number of products this merchant has in the Datafeedr database.
		 *
		 * @return int
		 */
		public function get_product_count(): int {
			return (int) $this->get( 'product_count', 0 );
		}

		/**
		 * Whether this merchant has any products in the Datafeedr database.
		 *
		 * @return bool
		 */
		public function has_products(): bool {
			return $this->get_product_count() > 0;
		}

		/**
		 * Get the raw comma-separated affiliate IDs ("suids") field.
		 *
		 * @return string|null
		 */
		public function get_suids_raw(): ?string {
			return $this->has( 'suids' ) ? (string) $this->get( 'suids' ) : null;
		}

		/**
		 * Get the affiliate IDs ("suids") as an array.
		 *
		 * @return array
		 */
		public function get_suids(): array {
			$suids = trim( (string) $this->get( 'suids', '' ) );

			return $suids === '' ? array() : array_map( 'trim', explode( ',', $suids ) );
		}
	}
}
