<?php

defined( 'ABSPATH' ) || exit;

/**
 * [dfrapi filters='name like nemo tent ; merchant ALL; currency in USD, CAD ; onsale EQ 1 ; finalprice gte 15000 ; finalprice lte 33500' exclude_duplicates='merchant_id' sort='finalprice' cache='10']
 */
class Dfrapi_Shortcode {

	public const FIELDS = [
		'any'          => [
			'field'     => 'any',
			'limit'     => 5,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'id'           => [
			'field'     => 'id',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'IN', 'NOT_IN' ],
			'alias'     => null,
		],
		'barcode'      => [
			'field'     => 'barcode',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'IN', 'NOT_IN' ],
			'alias'     => null,
		],
		'name'         => [
			'field'     => 'name',
			'limit'     => 5,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'description'  => [
			'field'     => 'description',
			'limit'     => 3,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'tags'         => [
			'field'     => 'tags',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'category'     => [
			'field'     => 'category',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'currency'     => [
			'field'     => 'currency',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'IN', 'NOT_IN' ],
			'alias'     => null,
		],
		'brand'        => [
			'field'     => 'brand',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'merchant'     => [
			'field'     => 'merchant',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE', 'IN', 'NOT_IN' ],
			'alias'     => null,
		],
		'source'       => [
			'field'     => 'source',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE', 'IN', 'NOT_IN' ],
			'alias'     => null,
		],
		'price'        => [
			'field'     => 'price',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			'alias'     => null,
		],
		'saleprice'    => [
			'field'     => 'saleprice',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			'alias'     => null,
		],
		'finalprice'   => [
			'field'     => 'finalprice',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			'alias'     => null,
		],
		'salediscount' => [
			'field'     => 'salediscount',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			'alias'     => null,
		],
		'time_updated' => [
			'field'     => 'time_updated',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			'alias'     => null,
		],
		'color'        => [
			'field'     => 'color',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'material'     => [
			'field'     => 'material',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'size'         => [
			'field'     => 'size',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'gender'       => [
			'field'     => 'gender',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'condition'    => [
			'field'     => 'condition',
			'limit'     => 2,
			'custom'    => false,
			'operators' => [ 'LIKE', 'NOT_LIKE' ],
			'alias'     => null,
		],
		'onsale'       => [
			'field'     => 'onsale',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'EQ' ],
			'alias'     => null,
		],
		'instock'      => [
			'field'     => 'instock',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'EQ' ],
			'alias'     => null,
		],
		'direct_url'   => [
			'field'     => 'direct_url',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'EMPTY', 'NOT_EMPTY' ],
			'alias'     => null,
		],
		'image'        => [
			'field'     => 'image',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'EQ' ],
			'alias'     => null,
		],
		'has_barcode'  => [
			'field'     => 'has_barcode',
			'limit'     => 1,
			'custom'    => false,
			'operators' => [ 'EQ' ],
			'alias'     => null,
		],
		'record_type'  => [
			'field'     => 'record_type',
			'limit'     => 1,
			'custom'    => true,
			'operators' => [ 'EQ' ],
			'alias'     => null,
		],
	];

	public const FIELD_ALIASES = [
		'network' => 'source',
		'm'       => 'merchant',
	];

	public const OPERATORS = [
		'IN'        => [ 'operator' => 'IN', 'custom' => false, ],
		'NOT_IN'    => [ 'operator' => '!IN', 'custom' => false, ],
		'LIKE'      => [ 'operator' => 'LIKE', 'custom' => false, ],
		'NOT_LIKE'  => [ 'operator' => '!LIKE', 'custom' => false, ],
		'EMPTY'     => [ 'operator' => 'EMPTY', 'custom' => false, ],
		'NOT_EMPTY' => [ 'operator' => '!EMPTY', 'custom' => false, ],
		'GTE'       => [ 'operator' => '>=', 'custom' => false, ],
		'GT'        => [ 'operator' => '>', 'custom' => false, ],
		'LTE'       => [ 'operator' => '<=', 'custom' => false, ],
		'LT'        => [ 'operator' => '<', 'custom' => false, ],
		'EQ'        => [ 'operator' => '=', 'custom' => false, ],
		'NOT_EQ'    => [ 'operator' => '!=', 'custom' => false, ],
		'ALL'       => [ 'operator' => '', 'custom' => true, ],
		'LIKE_ALL'  => [ 'operator' => '', 'custom' => true, ],
	];

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

	/**
	 * @var array $fields
	 */
	private $fields;

	/**
	 * @var array $field_aliases
	 */
	private $field_aliases;

	/**
	 * @var array $operators
	 */
	private $operators;

	/**
	 * @var array $filters
	 */
	private $filters;

	public function __construct( array $attributes ) {

		$this->attributes = shortcode_atts( [
			'filters'            => '',
			'limit'              => null,
			'offset'             => null,
			'sort'               => null,
			'exclude_duplicates' => null,
			'cache'              => DAY_IN_SECONDS * 3,
			'template'           => null,
		], $attributes );

		// Execute first.
		$this->set_fields();
		$this->set_field_aliases();
		$this->set_operators();

		$this->set_transient_name();
		$this->set_transient();
		$this->set_limit();
		$this->set_offset();
		$this->set_sort();
		$this->set_exclude_duplicates();
		$this->set_cache_lifetime();

		// Execute last.
		$this->set_filters();
	}

	public function result(): string {

//		$this->get_request();// test code
//		return 'just testing';

		if ( $this->transient_exists() ) {
			$response = $this->transient;
		} else {

			if ( empty( $this->filters ) ) {
				return __( 'No filters found', 'datafeedr-api' );
			}

			$response = dfrapi_api_request( 'search', $this->get_request() )->get_response();

			// @todo check for errors here!

			do_action( 'dfrapi_shortcode_response', $response, $this );

			set_transient( $this->transient_name, maybe_serialize( $response ), $this->get_cache_lifetime() );
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

		error_log( '$request' . ': ' . print_r( $request, true ) );

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

	/**
	 * @param string $field
	 * @param string $operator
	 * @param string|array $value If $value is array, it will be imploded with a comma. Otherwise, it will be used as-is.
	 *
	 * @return string
	 */
	public static function generate_query_filter( string $field, string $operator, $value = '' ): string {
		$value = is_array( $value ) ? implode( ',', $value ) : (string) $value;

		return dfrapi_format_query_filter_string( sprintf( '%s %s %s', $field, $operator, $value ) );
	}

	private function build_query(): array {

		$query = [];

		// @todo still need to handle product_type (product vs coupon)

		$merchant_filter_handled = false;

		foreach ( $this->filters as $filter ) {

			extract( $filter, EXTR_OVERWRITE );
			$field = $this->get_field_attribute( $field, 'field' );

			if ( $field === 'merchant' ) {
				$query                   = $this->handle_merchant_filters( $query, $operator, $value );
				$merchant_filter_handled = true;
			} else {
				$query[ $this->uniqid() ] = dfrapi_generate_query_filter( $field, $operator, $value );
			}
		}

		if ( ! $merchant_filter_handled ) {
			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', $this->selected_merchant_ids() );
		}

		return array_values( $query );
	}

	/**
	 * Returns an array of arrays, where each array is a filter represented by field, operator and value keys.
	 *
	 * Example of $parsed_filters:
	 *
	 *  Array (
	 *      [name.1] => Array (
	 *          [field] => name
	 *          [operator] => LIKE
	 *          [value] => tikka | actik
	 *      )
	 *      [name.2] => Array (
	 *          [field] => name
	 *          [operator] => !LIKE
	 *          [value] => dog | cat
	 *      )
	 *      [merchant_id.3] => Array (
	 *          [field] => merchant_id
	 *          [operator] => !IN
	 *          [value] => 123, 456
	 *      )
	 *      [currency.4] => Array (
	 *          [field] => currency
	 *          [operator] => IN
	 *          [value] => usd, cad
	 *      )
	 *      [brand.5] => Array (
	 *          [field] => brand
	 *          [operator] => LIKE
	 *          [value] => petzl
	 *      )
	 * )
	 *
	 * @param string $filter_string
	 *
	 * @return array
	 */
	private function parse_filter_string( string $filter_string ): array {

		/**
		 * Convert $filter_string to array and clean array.
		 */
		$raw_filters = explode( ';', $filter_string ); // Convert filter string to an array.
		$raw_filters = array_map( 'trim', $raw_filters ); // trim() all filters.
		$raw_filters = array_unique( $raw_filters ); // Remove duplicate filters.
		$raw_filters = array_filter( $raw_filters ); // Remove empty filters.
		$raw_filters = array_values( $raw_filters ); // Just get values.

		// Initialize iterator.
		$i = 1;

		// Initialize arrays to track parsed filters and keep track of used fields.
		$parsed_filters = [];
		$used_fields    = [];

		// Loop through each raw filter and add a fully formatted filter to the $parsed_filters array.
		foreach ( $raw_filters as $filter ) {

			// Get $filter in array form with keys of field, operator and value.
			$parsed_filter = $this->parse_filter( $filter );

			// If the $parsed_filter is empty (in this case an empty array), continue.
			if ( empty( $parsed_filter ) ) {
				continue;
			}

			$field    = $this->get_field_attribute( $parsed_filter['field'], 'field' ); // Example: name, brand, barcode, merchant_id, etc...
			$operator = $parsed_filter['operator']; // Example: IN, NOT_IN, LIKE, LIKE_ALL, etc...
			$value    = $parsed_filter['value'];
			$key      = $field . '.' . $i; // Example: name.1, brand.3, merchant_id.2, etc...

			// If the $field is not a valid filter field, continue.
			if ( ! array_key_exists( $field, $this->fields ) ) {
				continue;
			}

			// Make sure this field has not been used more times than it's allowed. If so, continue.
			$limit = absint( $this->get_field_attribute( $field, 'limit' ) );
			if ( isset( $used_fields[ $field ] ) && $used_fields[ $field ] >= $limit ) {
				continue;
			}

			if ( ! in_array( $operator, $this->get_field_attribute( $field, 'operators' ), true ) ) {
				continue;
			}

			// Get the real operator if this $operator is not a Custom Operator.
			$operator = array_key_exists( $operator, $this->get_custom_operators() )
				? $operator
				: $this->get_operator_attribute( $operator, 'operator' );

			// Replace special syntax in $value with valid syntax to use in API request.
			$value = $this->replace_syntax( $value );

			// If we made it this far, we have a valid filter so add it to the $parsed_filters array keyed by $key.
			$parsed_filters[ $key ] = $this->get_filter_as_array( $field, $operator, $value );

			// Add the current $field to the $used_fields array so that we can check usage on subsequent iterations of current loop.
			$used_fields[ $field ] = isset( $used_fields[ $field ] ) ? $used_fields[ $field ] ++ : 1;

			// Increment iterator.
			$i ++;
		}

		return $parsed_filters;
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
	 * [dfrapi filters='name LIKE Petzl Tikka ; merchant IN 21755, 97391']
	 * -----------------------------------------------------------------------------------------------------------------
	 * This usage will search all products from the merchant 21755 (Peter Glenn Ski & Sports) and 97391 (Sun & Ski)
	 * regardless of whether they have been selected here Datafeedr API > Merchants
	 *
	 *
	 * [dfrapi filters='name LIKE Petzl Tikka ; merchant !IN 21755, 97391']
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
	 * [dfrapi filters='name LIKE puff jacket ; merchant NOT_LIKE patagonia']
	 * -----------------------------------------------------------------------------------------------------------------
	 * This usage will search all products from the user's selected merchants (Datafeedr API > Merchants) but exclude
	 * merchants which match the name ("patagonia").
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
	private function handle_merchant_filters( array $query, string $operator, string $value = '' ): array {

		$selected_merchant_ids = $this->selected_merchant_ids();

		// [dfrapi filters='name LIKE Petzl Tikka ; merchant IN 21755, 97391'] (See method's doc block)
		if ( $operator === 'IN' && ! empty( $value ) ) {
			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', dfrapi_parse_string_of_ids( $value ) );

			return $query;
		}

		// [dfrapi filters='name LIKE Petzl Tikka ; merchant !IN 21755, 97391'] (See method's doc block)
		if ( $operator === '!IN' && ! empty( $value ) ) {
			$excluded_merchant_ids = dfrapi_parse_string_of_ids( $value );
			$included_merchant_ids = array_values( array_diff( $selected_merchant_ids, $excluded_merchant_ids ) );

			if ( ! empty( $included_merchant_ids ) ) {
				$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', $included_merchant_ids );
			}

			return $query;
		}

		// [dfrapi filters='name LIKE puff jacket ; merchant LIKE patagonia'] (See method's doc block)
		if ( $operator === 'LIKE' && ! empty( $value ) ) {

			if ( ! empty( $selected_merchant_ids ) ) {
				$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', $selected_merchant_ids );
			}

			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant', 'LIKE', $value );

			return $query;
		}

		// [dfrapi filters='name NOT_LIKE puff jacket ; merchant LIKE patagonia'] (See method's doc block)
		if ( $operator === '!LIKE' && ! empty( $value ) ) {

			if ( ! empty( $selected_merchant_ids ) ) {
				$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', $selected_merchant_ids );
			}

			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant', '!LIKE', $value );

			return $query;
		}

		// [dfrapi filters='name LIKE Petzl Tikka ; merchant ALL'] (See method's doc block)
		if ( $operator === 'ALL' ) {
			return $query;
		}

		// [dfrapi filters='name LIKE puff jacket ; merchant LIKE_ALL patagonia'] (See method's doc block)
		if ( $operator === 'LIKE_ALL' && ! empty( $value ) ) {
			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant', 'LIKE', $value );

			return $query;
		}

		// [dfrapi filters='name LIKE Petzl Tikka'] (See method's doc block)
		if ( ! empty( $selected_merchant_ids ) ) {
			$query[ $this->uniqid() ] = dfrapi_generate_query_filter( 'merchant_id', 'IN', $selected_merchant_ids );
		}

		return $query;
	}

	private function selected_merchant_ids(): array {

		$ids = null;

		if ( $ids === null ) {
			$ids = array_values( dfrapi_get_selected_merchant_ids() );
		}

		return $ids;
	}

	private function output_buffer( $response ) {
		$response = maybe_unserialize( $response );

		// @todo maybe create variables like $records, $merchants, $networks, etc... here to pass to template

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
		$this->filters = $this->parse_filter_string( $this->attributes['filters'] );
	}

	private function set_fields(): void {
		$this->fields = (array) apply_filters( 'dfrapi_shortcode_fields', self::FIELDS, $this );
	}

	private function set_field_aliases(): void {
		$this->field_aliases = (array) apply_filters( 'dfrapi_shortcode_field_aliases', self::FIELD_ALIASES, $this );
	}

	public function get_fields(): array {
		return $this->fields;
	}

	public function get_field_aliases(): array {
		return $this->field_aliases;
	}

	private function set_operators(): void {
		$this->operators = self::OPERATORS;
	}

	public function get_operators(): array {
		return $this->operators;
	}

	private function get_field_attributes( string $field ): ?array {
		$field = $this->get_canonical_field_name( $field );

		return $this->fields[ $field ] ?? null;
	}

	private function get_field_attribute( string $field, string $attribute ) {
		$attributes = (array) $this->get_field_attributes( $field );

		return $attributes[ $attribute ] ?? null;
	}

	private function get_operator_attribute( string $operator, string $attribute ) {
		return $this->operators[ $operator ][ $attribute ] ?? null;
	}

	private function get_canonical_field_name( string $field_name_or_field_alias_name ): string {
		return array_key_exists( $field_name_or_field_alias_name, $this->get_field_aliases() )
			? $this->get_field_aliases()[ $field_name_or_field_alias_name ]
			: $field_name_or_field_alias_name;
	}

	/**
	 * Returns an array of Operators which are custom.
	 *
	 * Example:
	 *
	 *  Array (
	 *      [ALL] => Array (
	 *          [operator] =>
	 *          [custom] => 1
	 *      )
	 *      [LIKE_ALL] => Array (
	 *          [operator] =>
	 *          [custom] => 1
	 *      )
	 *  )
	 *
	 * @return array
	 */
	private function get_custom_operators(): array {
		return array_filter( $this->get_operators(), static function ( $operator ) {
			return (bool) $operator['custom'];
		} );
	}

	/**
	 * @param string $filter Examples of how the $filter string might look:
	 *      merchant_id !IN 12345, 67890
	 *      image !EMPTY
	 *      currency IN USD, INR
	 *
	 * @return array Return array of Field, Operator and Value. If $filter is empty, return empty array.
	 */
	private function parse_filter( string $filter ): array {

		// Trim string and then make all lowercase.
		$filter = strtolower( trim( $filter ) );

		// If our $filter is empty, return empty array.
		if ( empty( $filter ) ) {
			return [];
		}

		// Get string before first space. This is our field.
		$field = trim( dfrapi_str_before( $filter, ' ' ) );

		// Remove $field from $filter using ltrim().
		$filter = trim( ltrim( $filter, $field ) );

		// Get string before first space. This is now our operator.
		$operator = trim( dfrapi_str_before( $filter, ' ' ) );

		// Remove $operator from $filter using ltrim(). This is our value.
		$value = trim( ltrim( $filter, $operator ) );

		// Set $operator to uppercase.
		$operator = strtoupper( $operator );

		return $this->get_filter_as_array( $field, $operator, $value );
	}

	private function get_filter_as_array( string $field, string $operator, string $value = '' ): array {
		return [ 'field' => $field, 'operator' => $operator, 'value' => $value ];
	}

	private function replace_syntax( string $string ) {
		$map = [ '(' => '"', ')' => '"' ];

		// @todo maybe add apply_filters here for $map

		return str_replace( array_keys( $map ), array_values( $map ), trim( $string ) );
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
		return 'a' . wp_generate_password( 6, false );
	}
}
