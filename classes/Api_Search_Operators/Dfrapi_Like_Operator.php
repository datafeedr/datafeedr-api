<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Like_Operator extends Dfrapi_Search_Operator_Abstract {



	public static function name(): string {
		return 'LIKE';
	}


}