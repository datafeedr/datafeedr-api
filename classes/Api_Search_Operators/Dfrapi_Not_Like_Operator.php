<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Not_Like_Operator extends Dfrapi_Search_Operator_Abstract {

	public static function name(): string {
		return 'NOT_LIKE';
	}
}