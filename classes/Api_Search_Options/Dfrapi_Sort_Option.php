<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Sort_Option extends Dfrapi_Search_Option_Abstract {

	public function label(): string {
		return __( 'Sort By', 'datafeedr-api' );
	}

	public static function name(): string {
		return 'sort';
	}

	public function limit(): int {
		return 1;
	}

	public function operators(): array {
		return [];
	}
}