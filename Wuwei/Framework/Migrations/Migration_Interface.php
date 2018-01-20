<?php namespace Datafeedr\Api\Wuwei\Migrations;

interface Migration_Interface {

	/**
	 * The function that performs the migration.
	 */
	public function run();

	/**
	 * Returns the DB version this migration relates to.
	 *
	 * @return string
	 */
	public function version();
}