<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Name_Search_Filter extends Dfrapi_Search_Filter_Abstract {

	public function label(): string {
		return __( 'Product Name', 'datafeedr-api' );
	}

	public function name(): string {
		return 'name';
	}

	public function limit(): int {
		return 99;
	}

	public function operators(): array {
		return [
			Dfrapi_Api_Search_Operators::LIKE,
			Dfrapi_Api_Search_Operators::NOT_LIKE,
		];
	}
}