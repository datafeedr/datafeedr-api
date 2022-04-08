<?php

defined( 'ABSPATH' ) || exit;

/**
 * [dfrapi filters='name like nemo tent ; merchant ALL; currency in USD, CAD ; onsale = 1 ; finalprice gte 15000 ; finalprice lte 33500' exclude_duplicates='merchant_id' sort='finalprice' cache='10']
 */
class Dfrapi_Shortcode {

	/**
	 * @var array $attributes
	 */
	private $attributes;

	/**
	 * @var string $transient_name
	 */
	private $transient_name;

	/**
	 * @var mixed $transient
	 */
	private $transient;

	/**
	 * @var array $filters
	 */
	private $filters;

	/**
	 * @var int|null $limit
	 */
	private $limit;

	/**
	 * @var int|null $offset
	 */
	private $offset;

	/**
	 * @var array|null $sort
	 */
	private $sort;

	/**
	 * @var string|null $exclude_duplicates
	 */
	private $exclude_duplicates;

	/**
	 * @var int $cache_lifetime
	 */
	private $cache_lifetime;

	public function __construct( array $attributes ) {
		$this->attributes = shortcode_atts( [
			'filters'            => '',
			'limit'              => null,
			'offset'             => null,
			'sort'               => null,
			'exclude_duplicates' => null,
			'cache'              => ( DAY_IN_SECONDS * 3 ),
			'template'           => null,
		], $attributes );

		$this->set_transient_name();
		$this->set_transient();
		$this->set_filters();
		$this->set_limit();
		$this->set_offset();
		$this->set_sort();
		$this->set_exclude_duplicates();
		$this->set_cache_lifetime();
	}

	public function result(): string {

		if ( $this->transient_exists() ) {
			$response = $this->transient;
		} else {

			if ( empty( $this->filters ) ) {
				return __( 'No filters found', 'datafeedr-api' );
			}

			$response = dfrapi_api_request( 'search', $this->get_request() )->get_response();

			// @todo check for errors here!

			do_action( 'dfrapi_shortcode_response', $response, $this );

			set_transient( $this->transient_name, maybe_serialize( $response ), $this->cache_lifetime );
		}

		return $this->output_buffer( $response );
	}

	public function get_request(): array {

		$request = [];

		if ( $this->limit !== null ) {
			$request['limit'] = $this->limit;
		}

		if ( $this->offset !== null ) {
			$request['offset'] = $this->offset;
		}

		if ( $this->sort !== null ) {
			$request['sort'] = $this->sort;
		}

		if ( $this->exclude_duplicates !== null ) {
			$request['exclude_duplicates'] = $this->exclude_duplicates;
		}

		$request['query'] = $this->build_query();

		// @todo add filter to before returning $request

		return $request;
	}

	public function get_attributes(): array {
		return $this->attributes;
	}

	public function get_transient_name(): string {
		return $this->transient_name;
	}

	public function get_transient() {
		return $this->transient;
	}

	public function get_filters(): array {
		return $this->filters;
	}

	public function get_limit(): ?int {
		return $this->limit;
	}

	public function get_offset(): ?int {
		return $this->offset;
	}

	public function get_sort(): ?array {
		return $this->sort;
	}

	public function get_exclude_duplicates(): ?string {
		return $this->exclude_duplicates;
	}

	public function get_cache_lifetime(): int {
		return $this->cache_lifetime;
	}

	private function build_query(): array {

		$query = [];

		foreach ( $this->filters as $filter ) {
			$value = dfrapi_format_query_filter_string( $filter );
			if ( ! empty( $value ) ) {
				$query[ $this->uniqid() ] = $value;
			}
		}

		$query = $this->handle_merchant_filters( $query );

		$has_network_filter = false;
		// Add support for "network_id"
		// Add support for coupon_only or product_only searches
		$network_filters = [ 'source_id in', 'source_id =', 'source_id all' ];

		foreach ( $query as $k => $v ) {
			if ( dfrapi_str_starts_with( strtolower( $v ), $network_filters ) ) {
				$has_network_filter = true;
				if ( dfrapi_str_contains( strtolower( $v ), [ 'all' ] ) ) {
					unset( $query[ $k ] );
				}
			}
		}

		if ( ! $has_network_filter ) {
//			$query[ $this->uniqid() ] = 'source_id IN ' . implode( ',', dfrapi_get_coupon_network_ids() );
		}


		error_log( '$query' . ': ' . print_r( $query, true ) );

		// @todo add filter to before returning $query.

		return array_values( $query );
	}

	/**
	 * This method generates the merchant-related queries for a shortcode's filters.
	 *
	 * There are currently 6 options for filtering on merchant data.
	 *
	 * The default search is: merchant_id IN "user's selected merchant IDs"
	 *
	 * Examples:
	 *
	 * [dfrapi filters='name LIKE Petzl Tikka']  **DEFAULT**
	 * -----------------------------------------------------------------------------------------------------------------
	 * This usage will search all products from the merchants selected here Datafeedr API > Merchants. This is the
	 * best usage in most cases.
	 *
	 *
	 * [dfrapi filters='name LIKE Petzl Tikka ; merchant_id IN 21755, 97391']
	 * -----------------------------------------------------------------------------------------------------------------
	 * This usage will search all products from the merchant 21755 (Peter Glenn Ski & Sports) and 97391 (Sun & Ski)
	 * regardless of whether they have been selected here Datafeedr API > Merchants
	 *
	 *
	 * [dfrapi filters='name LIKE Petzl Tikka ; merchant_id !IN 21755, 97391']
	 * -----------------------------------------------------------------------------------------------------------------
	 * This usage will search all products from the user's selected merchants (Datafeedr API > Merchants) excluding
	 * merchants 21755 (Peter Glenn Ski & Sports) and 97391 (Sun & Ski).
	 *
	 *
	 * [dfrapi filters='name LIKE puff jacket ; merchant LIKE patagonia']
	 * -----------------------------------------------------------------------------------------------------------------
	 * This usage will search all products from the user's selected merchants (Datafeedr API > Merchants) but limited
	 * to merchants which match the name ("patagonia"). Therefore, if a user has selected merchants with "patagonia" in
	 * their name, products from those merchants will be returned. This method is most useful when trying to generate
	 * a list of coupons for a specific merchant.
	 *
	 *
	 * [dfrapi filters='name LIKE puff jacket ; merchant LIKE_ALL patagonia']
	 * -----------------------------------------------------------------------------------------------------------------
	 * This usage will search all products from ALL merchants (selected or not) but limited to merchants which match the
	 * name ("patagonia").
	 *
	 *
	 * [dfrapi filters='name LIKE Petzl Tikka ; merchant ALL']
	 * -----------------------------------------------------------------------------------------------------------------
	 * This usage will search all products from ALL merchants (selected or not). This method is most useful if the user
	 * is using something like Sovrn (VigLink) or Skimlinks.
	 *
	 * @param array $query
	 *
	 * @return array An updated $query array with the necessary merchant filters in place.
	 */
	private function handle_merchant_filters( array $query ): array {

		// Default $merchant_filter array structure.
		$merchant_filter = [ 'field' => '', 'operator' => '', 'value' => '' ];

		// Get the first merchant filter from the $query (only one is allowed).
		foreach ( $query as $k => $v ) {
			$filter = strtolower( trim( $v ) ); // Example: merchant ID 1234, 456
			if ( dfrapi_str_starts_with( $filter, [ 'merchant' ] ) ) {
				error_log( '$filter' . ': ' . print_r( $filter, true ) );
				// because a query can only have 1 merchant filter.
				if ( $merchant_filter['field'] === '' ) {
					$merchant_filter = dfrapi_convert_query_filter_into_array( $filter );
				}
				unset( $query[ $k ] ); // Unset so the filter doesn't appear twice in the $query array.
			}
		}

		// There are all the merchant IDs the user has selected here Datafeedr API > Merchants.
		$selected_merchant_ids = array_values( dfrapi_get_selected_merchant_ids() );

		// [dfrapi filters='name LIKE Petzl Tikka ; merchant_id IN 21755, 97391'] (See method's doc block)
		if ( $merchant_filter['field'] === 'merchant_id' && $merchant_filter['operator'] === 'IN' && ! empty( $merchant_filter['value'] ) ) {
			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', dfrapi_parse_string_of_ids( $merchant_filter['value'] ) );

			return $query;
		}

		// [dfrapi filters='name LIKE Petzl Tikka ; merchant_id !IN 21755, 97391'] (See method's doc block)
		if ( $merchant_filter['field'] === 'merchant_id' && $merchant_filter['operator'] === '!IN' && ! empty( $merchant_filter['value'] ) ) {
			$excluded_merchant_ids = dfrapi_parse_string_of_ids( $merchant_filter['value'] );
			$included_merchant_ids = array_values( array_diff( $selected_merchant_ids, $excluded_merchant_ids ) );

			if ( ! empty( $included_merchant_ids ) ) {
				$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', $included_merchant_ids );
			}

			return $query;
		}

		// [dfrapi filters='name LIKE puff jacket ; merchant LIKE patagonia'] (See method's doc block)
		if ( $merchant_filter['field'] === 'merchant' && $merchant_filter['operator'] === 'LIKE' && ! empty( $merchant_filter['value'] ) ) {

			if ( ! empty( $selected_merchant_ids ) ) {
				$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', $selected_merchant_ids );
			}

			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant', 'LIKE', $merchant_filter['value'] );

			return $query;
		}

		// [dfrapi filters='name LIKE Petzl Tikka ; merchant ALL'] (See method's doc block)
		if ( $merchant_filter['field'] === 'merchant' && $merchant_filter['operator'] === 'ALL' ) {
			return $query;
		}

		// [dfrapi filters='name LIKE puff jacket ; merchant LIKE_ALL patagonia'] (See method's doc block)
		if ( $merchant_filter['field'] === 'merchant' && $merchant_filter['operator'] === 'LIKE_ALL' && ! empty( $merchant_filter['value'] ) ) {
			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant', 'LIKE', $merchant_filter['value'] );

			return $query;
		}

		// [dfrapi filters='name LIKE Petzl Tikka'] (See method's doc block)
		if ( ! empty( $selected_merchant_ids ) ) {
			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', $selected_merchant_ids );
		}

		return $query;
	}

	private function output_buffer( $response ) {
		$response = maybe_unserialize( $response );
		ob_start();
		// @todo allow template override
		include dirname( DFRAPI_PLUGIN_FILE ) . '/templates/dfrapi-shortcode.php';

		return ob_get_clean();
	}

	private function set_transient_name(): void {
		$hash = md5( maybe_serialize( $this->attributes ) );

		$this->transient_name = 'dfrapi_shortcode_' . $hash;
	}

	private function set_transient(): void {
		$this->transient = get_transient( $this->transient_name );
	}

	private function transient_exists(): bool {
		return $this->transient !== false;
	}

	private function set_filters(): void {
		$filters       = explode( ';', $this->attributes['filters'] );
		$filters       = array_map( 'trim', $filters );
		$this->filters = array_values( array_filter( array_unique( $filters ) ) );
	}

	private function set_limit(): void {
		if ( $this->attributes['limit'] !== null ) {
			$limit       = absint( $this->attributes['limit'] );
			$this->limit = $limit >= 1 && $limit <= 100 ? $limit : 20;
		}
	}

	private function set_offset(): void {
		if ( $this->attributes['offset'] !== null ) {
			$offset       = absint( $this->attributes['offset'] );
			$this->offset = $offset > 9999 ? 0 : $offset;
		}
	}

	private function set_sort(): void {
		if ( $this->attributes['sort'] !== null ) {
			$this->sort = array_map( 'trim', explode( ';', $this->attributes['sort'] ) );
		}
	}

	private function set_exclude_duplicates(): void {
		if ( $this->attributes['exclude_duplicates'] !== null ) {
			$this->exclude_duplicates = trim( $this->attributes['exclude_duplicates'] );
		}
	}

	private function set_cache_lifetime(): void {
		$cache                = absint( $this->attributes['cache'] );
		$this->cache_lifetime = $cache > 0 ? $cache : ( DAY_IN_SECONDS * 3 );
	}

	private function uniqid(): string {
		return bin2hex( random_bytes( 2 ) );
	}
}
