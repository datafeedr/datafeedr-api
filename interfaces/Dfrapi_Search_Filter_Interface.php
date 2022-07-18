<?php

defined( 'ABSPATH' ) || exit;

interface Dfrapi_Search_Filter_Interface {

	/**
	 * @return string
	 */
	public function label(): string;

	/**
	 * @return string
	 */
	public function name(): string;

	/**
	 * @return int
	 */
	public function limit(): int;

	/**
	 * @return array
	 */
	public function operators(): array;

}