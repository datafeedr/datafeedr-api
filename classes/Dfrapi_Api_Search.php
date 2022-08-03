<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search {

	/**
	 * There are the original parameters pass to this constructor of this class.
	 *
	 *  [
	 *      'name like patagonia puff',
	 *      'merchant_id IN 12345, 6789',
	 *      'barcode IN 999123123',
	 *      'sort -name',
	 *  ];
	 *
	 * @var array
	 */
	private array $params;

	/**
	 * Context of the API request.
	 *
	 * @var mixed $context This could be anything passed here to be used in various apply_filters calls. Default: null
	 */
	private $context;

	/**
	 * An array of Dfrapi_Search_Filter_Abstract objects.
	 *
	 * @var Dfrapi_Search_Filter_Abstract[] $filters
	 */
	private array $filters = [];


	private array $options = [];

	/**
	 * Information stored about this search.
	 *
	 * @var array $meta
	 */
	private array $meta = [];

	public function __construct( array $params, $context = null ) {
		$this->params  = array_filter( array_unique( array_map( 'trim', $params ) ) );
		$this->context = $context;
		$this->parseParams();
	}

	private function parseParams(): void {
		$this->set_filters();
		$this->set_options();
	}

	public function get_params(): array {
		return $this->params;
	}

	public function get_context() {
		return $this->context;
	}

	public function get_filters(): array {
		return $this->filters;
	}

	public function get_options(): array {
		return $this->options;
	}

	public function get_meta(): array {
		return $this->meta;
	}

	private function set_filters(): void {

		$filters = [];

		foreach ( $this->get_params() as $param ) {

			$filter = Dfrapi_Api_Search_Filters::factory( $param, $this );

			// If $filter is null, handle the next $param.
			if ( ! $filter ) {
				continue;
			}

			// Update filter usage count.
			$this->increment_filter_usage_count( $filter::name() );

			// If this $filter has exceeded its usage limit, ignore and handle next $param.
			if ( $this->get_filter_usage_count( $filter::name() ) > $filter->limit() ) {
				continue;
			}

			// If we made it this far, add $filter to our $filters array.
			$filters[] = $filter;
		}

		// Remove any null values from array (if any made it this far).
		$this->filters = array_filter( $filters );
	}

	private function get_filter_usage_count( string $filter_name ): int {
		return isset( $this->meta['filter_count'][ $filter_name ] )
			? absint( $this->meta['filter_count'][ $filter_name ] )
			: 0;
	}

	private function increment_filter_usage_count( string $filter_name ): void {
		$this->meta['filter_count'][ $filter_name ] = ( $this->get_filter_usage_count( $filter_name ) + 1 );
	}

	// Sets limit, offset, sort and duplicate options
	private function set_options(): void {

		$options = Dfrapi_Api_Search_Filters::factory( $param, $this );

		$this->options = []; // @todo
	}
}
