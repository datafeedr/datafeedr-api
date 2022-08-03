<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Barcode_Filter extends Dfrapi_Search_Filter_Abstract {

	public function label(): string {
		return __( 'Barcode', 'datafeedr-api' );
	}

	public static function name(): string {
		return 'barcode';
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

		$barcodes = explode( $separator, $value );
		$barcodes = array_map( 'trim', $barcodes );
		$barcodes = array_filter( $barcodes );
		$barcodes = array_unique( $barcodes );

		return implode( $separator, array_values( $barcodes ) );
	}
}