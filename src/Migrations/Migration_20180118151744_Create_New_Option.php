<?php namespace Datafeedr\Api\Migrations;

use Datafeedr\Api\Wuwei\Migrations\Migration_Interface;

class Migration_20180118151744_Create_New_Option extends Migration implements Migration_Interface {

	public function run() {
		update_option( 'test_option_on_update', date_i18n( 'U' ) );
		error_log( 'test_option_on_update' . ': created' );
	}
}