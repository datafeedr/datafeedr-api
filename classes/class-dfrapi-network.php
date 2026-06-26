<?php

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Network' ) ) {

	/**
	 * A single Datafeedr affiliate Network.
	 *
	 * Create instances with the dfrapi_network() factory function.
	 *
	 * @since 1.4.2
	 */
	class Dfrapi_Network extends Dfrapi_Data_Object {

		/**
		 * Maps new API field names to their canonical (legacy) names.
		 *
		 * Empty today. Populate when the API renames network fields, e.g.:
		 *
		 *     return array(
		 *         'id' => '_id',
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
			return 'dfrapi_normalize_network_array';
		}

		/**
		 * Get the network ID.
		 *
		 * @return int
		 */
		public function get_id() {
			return (int) $this->get( '_id', 0 );
		}

		/**
		 * Get the group name shared by related networks (e.g. "Amazon", "ShareASale").
		 *
		 * @return string
		 */
		public function get_group(): string {
			return (string) $this->get( 'group', '' );
		}

		/**
		 * Get the group ID shared by related networks.
		 *
		 * @return int
		 */
		public function get_group_id(): int {
			return (int) $this->get( 'group_id', 0 );
		}

		/**
		 * Get the number of merchants in this network.
		 *
		 * @return int
		 */
		public function get_merchant_count(): int {
			return (int) $this->get( 'merchant_count', 0 );
		}

		/**
		 * Get the number of products in this network.
		 *
		 * @return int
		 */
		public function get_product_count(): int {
			return (int) $this->get( 'product_count', 0 );
		}

		/**
		 * Get the network type: "products" or "coupons".
		 *
		 * @return string
		 */
		public function get_type(): string {
			return (string) $this->get( 'type', 'products' );
		}

		/**
		 * Whether this is a product network.
		 *
		 * @return bool
		 */
		public function is_product_network(): bool {
			return $this->get_type() === 'products';
		}

		/**
		 * Whether this is a coupon network.
		 *
		 * @return bool
		 */
		public function is_coupon_network(): bool {
			return $this->get_type() === 'coupons';
		}
	}
}
