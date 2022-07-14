<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Barcode_Search_Field extends Dfrapi_Search_Field_Abstract {

	public function label(): string {
		return __( 'Barcode', 'datafeedr-api' );
	}

	public function field(): string {
		return 'barcode';
	}

	public function limit(): int {
		return 1;
	}

	public function operators(): array {
		return [
			Dfrapi_Api_Search_Operators::IN,
			Dfrapi_Api_Search_Operators::NOT_IN,
		];
	}
}