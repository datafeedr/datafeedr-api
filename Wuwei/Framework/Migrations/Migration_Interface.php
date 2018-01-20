<?php namespace Datafeedr\Api\Wuwei\Migrations;

/**
 * Interface Migration_Interface
 * @package Datafeedr\Api\Wuwei\Migrations
 *
 * @since 2.0.0
 */
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