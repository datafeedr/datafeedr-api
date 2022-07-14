<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Any_Search_Field extends Dfrapi_Search_Field_Abstract {

	public function label(): string {
		return __( 'Any Field', 'datafeedr-api' );
	}

	public function field(): string {
		return 'any';
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