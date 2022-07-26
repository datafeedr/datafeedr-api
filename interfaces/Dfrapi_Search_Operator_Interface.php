<?php

defined( 'ABSPATH' ) || exit;

interface Dfrapi_Search_Operator_Interface {

	/**
	 * @return string
	 */
	public static function name(): string;

}