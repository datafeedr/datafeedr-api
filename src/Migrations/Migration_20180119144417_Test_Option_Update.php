<?php namespace Datafeedr\Api\Migrations;

use Datafeedr\Api\Wuwei\Migrations\Migration_Interface;

class Migration_20180119144417_Test_Option_Update extends Migration implements Migration_Interface {

	public function run() {
		delete_option( 'test_option_on_update' );
		error_log( 'test_option_on_update' );
	}
}