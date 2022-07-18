<?php

defined( 'ABSPATH' ) || exit;

abstract class Dfrapi_Search_Filter_Abstract implements Dfrapi_Search_Filter_Interface {

	/**
	 * @var string $param
	 */
	public string $param;

	public function __construct( string $param ) {
		$this->param = $param;
	}

	public function format(): string {

		$field    = $this->name();
		$operator = $this->operator();


		return '';
	}

	public function operator(): ?string {

		$operator = self::extract_operator_from_param( $this->name(), $this->param );

		if ( in_array( $operator, $this->operators(), true ) ) {
			return ( new Dfrapi_Api_Search_Operators() )->get_operator( $operator );
		}

		return null;
	}

	public static function extract_operator_from_param( $field, $param ): string {
		$param = trim( ltrim( $field, $param ) );

		return strtoupper( trim( dfrapi_str_before( $param, ' ' ) ) );
	}

}