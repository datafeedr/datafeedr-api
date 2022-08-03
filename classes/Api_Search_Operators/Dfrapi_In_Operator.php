<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_In_Operator extends Dfrapi_Search_Operator_Abstract {

	public static function name(): string {
		return 'IN';
	}

}
