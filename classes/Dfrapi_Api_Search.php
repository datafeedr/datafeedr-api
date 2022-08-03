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

		foreach ( $this->get_params() as $param ) {

			$param = new Dfrapi_Api_Search_Params( $param, $this );

			if ( ! $param->is_valid() ) {
				continue;
			}

			if ( $param->type === 'filter' ) {
				$this->handle_filter( $param );
			}

			if ( $param->type === 'option' ) {
				$this->handle_option( $param );
			}

		}
	}

	private function handle_filter( Dfrapi_Api_Search_Params $param ): void {

		// Get an instance of a Dfrapi_Search_Filter_Abstract object.
		if ( ! $filter = $param->filter_factory() ) {
			return;
		}

		// Update filter usage count.
		$this->increment_usage_count( $filter::name() );

		// If this $filter has exceeded its usage limit, don't add to $this->filters array.
		if ( $this->get_usage_count( $filter::name() ) > $filter->limit() ) {
			return;
		}

		// If we made it this far, add $filter to our $filters array.
		$this->filters[] = $filter;
	}

	private function handle_option( Dfrapi_Api_Search_Params $param ): void {

		// Get an instance of a Dfrapi_Search_Option_Abstract object.
		if ( ! $option = $param->option_factory() ) {
			return;
		}

		// Update option usage count.
		$this->increment_usage_count( $option::name() );

		// If this $option has exceeded its usage limit, don't add to $this->options array.
		if ( $this->get_usage_count( $option::name() ) > $option->limit() ) {
			return;
		}

		// If we made it this far, add $option to our $this->options array.
		$this->options[] = $option;
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

	private function get_usage_count( string $name ): int {
		return isset( $this->meta['usage_count'][ $name ] )
			? absint( $this->meta['usage_count'][ $name ] )
			: 0;
	}

	private function increment_usage_count( string $name ): void {
		$this->meta['usage_count'][ $name ] = ( $this->get_usage_count( $name ) + 1 );
	}
}
