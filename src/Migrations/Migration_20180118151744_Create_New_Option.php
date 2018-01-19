<?php namespace Datafeedr\Api\Migrations;

use Datafeedr\Api\Wuwei\Migrations\Migration;

class Migration_20180118151744_Create_New_Option implements Migration {

	/**
	 * Indicate what DB version this represents.
	 */
	const VERSION = '20180118151744';

	public function run() {
		update_option( 'test_option_on_update', date_i18n( 'U' ) );
	}

	public function version() {
		return self::VERSION;
	}
}