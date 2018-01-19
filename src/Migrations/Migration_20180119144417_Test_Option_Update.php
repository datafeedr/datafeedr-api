<?php namespace Datafeedr\Api\Migrations;

use Datafeedr\Api\Wuwei\Migrations\Migration;

class Migration_20180119144417_Test_Option_Update implements Migration {

	/**
	 * Indicate what DB version this represents.
	 */
	const VERSION = '20180119144417';

	public function run() {
		delete_option( 'test_option_on_update' );
	}

	public function version() {
		return self::VERSION;
	}
}