<?php

defined( 'ABSPATH' ) || exit;

abstract class Dfrapi_Search_Filter_Abstract implements Dfrapi_Search_Filter_Interface {

	/**
	 * A query param.
	 *
	 *  Examples:
	 *      any LIKE iphone
	 *      name like patagonia
	 *      price GTE 50000
	 *
	 * @var string $param
	 */
	public string $param;

	public function __construct( string $param ) {
		$this->param = $param;
	}

	public function format(): string {

		$field    = static::name();
		$operator = $this->operator();

		return '';
	}

	public function operator(): ?Dfrapi_Search_Operator_Abstract {
		return Dfrapi_Api_Search_Operators::factory( $this );
	}

}
