<?php

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Product' ) ) {

	/**
	 * A single Datafeedr Product.
	 *
	 * Named getters exist for the mandatory and common product fields. All other
	 * fields (merchant-specific fields like "custom1", "item_group_id", etc.) are
	 * available via get() and has().
	 *
	 * Create instances with the dfrapi_product() factory function.
	 *
	 * @since 1.4.2
	 */
	class Dfrapi_Product extends Dfrapi_Data_Object {

		/**
		 * Maps new (v5) API field names to their canonical (legacy) names.
		 *
		 * The API currently returns both sets of keys, so these copies are no-ops
		 * today. When the API stops sending the legacy keys, this map rebuilds them.
		 *
		 * @return array
		 */
		protected static function field_map(): array {
			return array(
				'id'         => '_id',
				'network'    => 'source',
				'network_id' => 'source_id',
			);
		}

		/**
		 * @return string
		 */
		protected static function normalize_filter_name(): string {
			return 'dfrapi_normalize_product_array';
		}

		/**
		 * Get the ISO-4217 currency code.
		 *
		 * @return string
		 */
		public function get_currency(): string {
			return (string) $this->get( 'currency', 'USD' );
		}

		/**
		 * Get the merchant ID.
		 *
		 * @return int
		 */
		public function get_merchant_id(): int {
			return (int) $this->get( 'merchant_id', 0 );
		}

		/**
		 * Get the merchant name.
		 *
		 * @return string
		 */
		public function get_merchant(): string {
			return (string) $this->get( 'merchant', '' );
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
		 * Get the affiliate network name.
		 *
		 * @return string
		 */
		public function get_source(): string {
			return (string) $this->get( 'source', '' );
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
		 * Get the list (regular) price in the currency's least valued units (e.g. cents).
		 *
		 * @return int
		 */
		public function get_list_price(): int {
			return (int) $this->get( 'price', 0 );
		}

		/**
		 * Get the effective price (the lowest, "finalprice" field) in the currency's least valued units.
		 *
		 * Falls back to the list price when the "finalprice" field is absent.
		 *
		 * @return int
		 */
		public function get_effective_price(): int {
			return $this->has( 'finalprice' ) ? (int) $this->get( 'finalprice' ) : $this->get_list_price();
		}

		/**
		 * Get the sale price in the currency's least valued units.
		 *
		 * @return int|null Null when the product has no sale price.
		 */
		public function get_sale_price(): ?int {
			return $this->has( 'saleprice' ) ? (int) $this->get( 'saleprice' ) : null;
		}

		/**
		 * Get the sale discount percentage.
		 *
		 * @return int
		 */
		public function get_sale_discount(): int {
			return (int) $this->get( 'salediscount', 0 );
		}

		/**
		 * Whether the product is on sale.
		 *
		 * @return bool
		 */
		public function is_on_sale(): bool {
			return (int) $this->get( 'onsale', 0 ) === 1;
		}

		/**
		 * Whether the product is commissionable.
		 *
		 * @return bool
		 */
		public function is_commissionable(): bool {
			return (int) $this->get( 'iscommissionable', 1 ) !== 0;
		}

		/**
		 * Get a Dfrapi_Price object for one of this product's price fields.
		 *
		 * @param string $field Price field name: 'finalprice', 'price' or 'saleprice'. Default 'finalprice'.
		 * @param mixed $context The context we are displaying the price in.
		 *
		 * @return Dfrapi_Price
		 */
		public function get_price_object( string $field = 'finalprice', $context = null ): Dfrapi_Price {
			return dfrapi_price( $this->get( $field, 0 ), $this->get_currency(), $context );
		}

		/**
		 * Get the list (regular) price formatted with the currency's symbol and separators.
		 *
		 * @param mixed $context The context we are displaying the price in.
		 *
		 * @return string
		 */
		public function get_list_price_formatted( $context = null ): string {
			return dfrapi_get_price( $this->get_list_price(), $this->get_currency(), $context );
		}

		/**
		 * Get the effective price formatted with the currency's symbol and separators.
		 *
		 * @param mixed $context The context we are displaying the price in.
		 *
		 * @return string
		 */
		public function get_effective_price_formatted( $context = null ): string {
			return dfrapi_get_price( $this->get_effective_price(), $this->get_currency(), $context );
		}

		/**
		 * Get the sale price formatted with the currency's symbol and separators.
		 *
		 * @param mixed $context The context we are displaying the price in.
		 *
		 * @return string Empty string when the product has no sale price.
		 */
		public function get_sale_price_formatted( $context = null ): string {
			$sale_price = $this->get_sale_price();

			return $sale_price === null ? '' : dfrapi_get_price( $sale_price, $this->get_currency(), $context );
		}

		/**
		 * Get the raw affiliate URL (containing @@@ and ### placeholders).
		 *
		 * Use get_affiliate_url() for a clickable URL with affiliate and tracking IDs inserted.
		 *
		 * @return string
		 */
		public function get_url(): string {
			return (string) $this->get( 'url', '' );
		}

		/**
		 * Get the raw affiliate URL containing a tracking ID placeholder.
		 *
		 * @return string|null
		 */
		public function get_ref_url(): ?string {
			return $this->has( 'ref_url' ) ? (string) $this->get( 'ref_url' ) : null;
		}

		/**
		 * Get the URL to the product page on the merchant's site (no affiliate redirect).
		 *
		 * @return string|null
		 */
		public function get_direct_url(): ?string {
			return $this->has( 'direct_url' ) ? (string) $this->get( 'direct_url' ) : null;
		}

		/**
		 * Get the raw impression URL (containing the @@@ placeholder).
		 *
		 * Use get_impression_url() for a URL with the affiliate ID inserted.
		 *
		 * @return string|null
		 */
		public function get_impression_url_raw(): ?string {
			return $this->has( 'impressionurl' ) ? (string) $this->get( 'impressionurl' ) : null;
		}

		/**
		 * Get the affiliate URL with affiliate and tracking IDs inserted.
		 *
		 * @return string Empty string when the affiliate ID is missing.
		 */
		public function get_affiliate_url(): string {
			return (string) dfrapi_url( $this->to_array() );
		}

		/**
		 * Get the impression URL with the affiliate ID inserted.
		 *
		 * @return string Empty string when the product has no impression URL or the affiliate ID is missing.
		 */
		public function get_impression_url(): string {
			return (string) dfrapi_impression_url( $this->to_array() );
		}

		/**
		 * Get the main (large) product image URL.
		 *
		 * @return string|null
		 */
		public function get_image(): ?string {
			return $this->has( 'image' ) ? (string) $this->get( 'image' ) : null;
		}

		/**
		 * Get the thumbnail image URL.
		 *
		 * @return string|null
		 */
		public function get_thumbnail(): ?string {
			return $this->has( 'thumbnail' ) ? (string) $this->get( 'thumbnail' ) : null;
		}

		/**
		 * Whether the product has an image or a thumbnail.
		 *
		 * @return bool
		 */
		public function has_image(): bool {
			return (string) $this->get( 'image', '' ) !== '' || (string) $this->get( 'thumbnail', '' ) !== '';
		}

		/**
		 * Get the product description.
		 *
		 * @return string
		 */
		public function get_description(): string {
			return (string) $this->get( 'description', '' );
		}

		/**
		 * Get the short product description.
		 *
		 * @return string
		 */
		public function get_short_description(): string {
			return (string) $this->get( 'shortdescription', '' );
		}

		/**
		 * Get the HTML product description.
		 *
		 * @return string|null
		 */
		public function get_html_description(): ?string {
			return $this->has( 'htmldescription' ) ? (string) $this->get( 'htmldescription' ) : null;
		}

		/**
		 * Get the brand.
		 *
		 * @return string|null
		 */
		public function get_brand(): ?string {
			return $this->has( 'brand' ) ? (string) $this->get( 'brand' ) : null;
		}

		/**
		 * Get the category.
		 *
		 * @return string|null
		 */
		public function get_category(): ?string {
			return $this->has( 'category' ) ? (string) $this->get( 'category' ) : null;
		}

		/**
		 * Get the color.
		 *
		 * @return string|null
		 */
		public function get_color(): ?string {
			return $this->has( 'color' ) ? (string) $this->get( 'color' ) : null;
		}

		/**
		 * Get the gender.
		 *
		 * @return string|null
		 */
		public function get_gender(): ?string {
			return $this->has( 'gender' ) ? (string) $this->get( 'gender' ) : null;
		}

		/**
		 * Get the size.
		 *
		 * @return string|null
		 */
		public function get_size(): ?string {
			return $this->has( 'size' ) ? (string) $this->get( 'size' ) : null;
		}

		/**
		 * Get the SKU.
		 *
		 * @return string|null
		 */
		public function get_sku(): ?string {
			return $this->has( 'sku' ) ? (string) $this->get( 'sku' ) : null;
		}

		/**
		 * Get the EAN.
		 *
		 * @return string|null
		 */
		public function get_ean(): ?string {
			return $this->has( 'ean' ) ? (string) $this->get( 'ean' ) : null;
		}

		/**
		 * Get the ISBN.
		 *
		 * @return string|null
		 */
		public function get_isbn(): ?string {
			return $this->has( 'isbn' ) ? (string) $this->get( 'isbn' ) : null;
		}

		/**
		 * Get the UPC.
		 *
		 * @return string|null
		 */
		public function get_upc(): ?string {
			return $this->has( 'upc' ) ? (string) $this->get( 'upc' ) : null;
		}

		/**
		 * Get the Amazon ASIN.
		 *
		 * @return string|null
		 */
		public function get_asin(): ?string {
			return $this->has( 'asin' ) ? (string) $this->get( 'asin' ) : null;
		}

		/**
		 * Get the SUID (merchant's unique product identifier).
		 *
		 * @return string|null
		 */
		public function get_suid(): ?string {
			return $this->has( 'suid' ) ? (string) $this->get( 'suid' ) : null;
		}

		/**
		 * Get the barcode (UPC/EAN/ISBN/GTIN).
		 *
		 * @return string|null
		 */
		public function get_barcode(): ?string {
			return $this->has( 'barcode' ) ? (string) $this->get( 'barcode' ) : null;
		}

		/**
		 * Get the tags.
		 *
		 * @return string|null
		 */
		public function get_tags(): ?string {
			return $this->has( 'tags' ) ? (string) $this->get( 'tags' ) : null;
		}

		/**
		 * Get the date the product was added (YYYY-MM-DD HH:MM:SS).
		 *
		 * @return string|null
		 */
		public function get_time_added(): ?string {
			return $this->has( 'time_added' ) ? (string) $this->get( 'time_added' ) : null;
		}

		/**
		 * Get the date the product was last updated (YYYY-MM-DD HH:MM:SS).
		 *
		 * @return string
		 */
		public function get_time_updated(): string {
			return (string) $this->get( 'time_updated', '' );
		}

		/**
		 * Get the coupon code.
		 *
		 * @return string|null
		 */
		public function get_offer_code(): ?string {
			return $this->has( 'offercode' ) ? (string) $this->get( 'offercode' ) : null;
		}

		/**
		 * Get the date the coupon offer begins.
		 *
		 * @return string|null
		 */
		public function get_offer_begin(): ?string {
			return $this->has( 'offerbegin' ) ? (string) $this->get( 'offerbegin' ) : null;
		}

		/**
		 * Get the date the coupon offer ends.
		 *
		 * @return string|null
		 */
		public function get_offer_end(): ?string {
			return $this->has( 'offerend' ) ? (string) $this->get( 'offerend' ) : null;
		}

		/**
		 * Whether the product has a coupon code.
		 *
		 * @return bool
		 */
		public function has_coupon_code(): bool {
			return trim( (string) $this->get( 'offercode', '' ) ) !== '';
		}

		/**
		 * Get the value of the first of $fields which exists, or all of them concatenated.
		 *
		 * See dfrapi_get_fields_from_product() for full details.
		 *
		 * @param array|string $fields One or more field names.
		 * @param mixed $default Value to return when none of the fields exist. Default null.
		 * @param bool|string $concatenate False or a string to concatenate all found values with. Default false.
		 *
		 * @return mixed
		 */
		public function get_fields( $fields, $default = null, $concatenate = false ) {
			return dfrapi_get_fields_from_product( $this->to_array(), $fields, $default, $concatenate );
		}
	}
}
