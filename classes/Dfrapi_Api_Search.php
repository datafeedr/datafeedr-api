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
	public array $params;

	/**
	 * Context of the API request.
	 *
	 * @var mixed $context This could be anything passed here to be used in various apply_filters calls. Default: null
	 */
	public $context;

	protected array $filters;
	protected array $options;

	public function __construct( array $params, $context = null ) {
		$this->params  = $params;
		$this->context = $context;
		$this->parseParams();
	}

	public function get_filters(): array {
		return $this->filters;
	}

	public function get_options(): array {
		return $this->options;
	}

	private function parseParams(): void {
		$this->set_filters();
		$this->set_options();
	}

	private function set_filters(): void {
		$this->filters = Dfrapi_Api_Search_Filters::parse( $this->params );
	}

	// Sets limit, offset, sort and duplicate options
	private function set_options(): void {
		$this->options = []; // @todo
	}

}
