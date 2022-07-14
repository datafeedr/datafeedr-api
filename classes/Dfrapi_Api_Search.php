<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search {

	/**
	 *  Array
	 *      (
	 *          [0] => Array
	 *          (
	 *              [value] => 87933,12143,37384
	 *              [field] => merchant_id
	 *              [operator] => is
	 *          )
	 *          [2] => Array
	 *          (
	 *              [value] => name|barcode|description|direct_url
	 *              [field] => duplicates
	 *              [operator] => is
	 *          )
	 *          [29] => Array
	 *          (
	 *              [value] => 2
	 *              [field] => merchant_limit
	 *              [operator] => is
	 *          )
	 *      )
	 *  )
	 *
	 * @var array
	 */
	public $originalParams;

	public $query;

	public function __construct( array $params, array $options = [], $context = null ) {
		$this->originalParams = $params;
		$this->parseParams();
	}

	private function parseParams() {
		// do something with $this->originalParams;
		$this->parsedParams = $this->originalParams;
	}
}
