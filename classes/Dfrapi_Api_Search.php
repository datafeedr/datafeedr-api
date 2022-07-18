<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search {

	/**
	 * [
	 * 'name like patagonia puff',
	 * 'merchant_id IN 12345, 6789',
	 * 'barcode IN 999123123',
	 * 'sort -name',
	 * ];
	 *
	 * @var array
	 */
	public array $original_params;
	public array $filters;
	public array $options;

	public $context;

	public function __construct( array $params, $context = null ) {
		$this->original_params = $params;
		$this->context         = $context;
		$this->parseParams();
	}

	private function parseParams(): void {
		$this->set_filters();
		$this->set_options();
	}

	private function set_filters(): void {
		$this->filters = Dfrapi_Api_Search_Filters::parse( $this->original_params );
	}

	// Sets limit, offset, sort and duplicate options
	private function set_options(): void {

	}

}
