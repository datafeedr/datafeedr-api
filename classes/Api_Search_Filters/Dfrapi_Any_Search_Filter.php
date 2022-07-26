<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Any_Search_Filter extends Dfrapi_Search_Filter_Abstract {

	public function label(): string {
		return __( 'Any Field', 'datafeedr-api' );
	}

	public static function name(): string {
		return 'any';
	}

	public function limit(): int {
		return 99;
	}

	public function format(): string {

//		if ( dfrapi_str_starts_with( strtolower( $param . ' ' ), $names ) ) {
//
//		}
		return '';
	}

	public function operators(): array {
		return [
			Dfrapi_Api_Search_Operators::LIKE,
			Dfrapi_Api_Search_Operators::NOT_LIKE,
		];
	}
}