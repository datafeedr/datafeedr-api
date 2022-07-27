<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Merchant_Id_Search_Filter extends Dfrapi_Search_Filter_Abstract {

	public function label(): string {
		return __( 'Merchant ID', 'datafeedr-api' );
	}

	public static function name(): string {
		return 'merchant_id';
	}

	public function limit(): int {
		return 1;
	}

	public function operators(): array {
		return [
			Dfrapi_In_Operator::class,
			Dfrapi_Not_In_Operator::class,
		];
	}

	public function format_value( string $value ): string {
		$separator = ',';

		return implode( $separator, dfrapi_parse_string_of_ids( $value, $separator ) );
	}
}