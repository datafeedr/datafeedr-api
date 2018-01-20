<?php namespace Datafeedr\Api\Migrations;

use Datafeedr\Api\Wuwei\Database\Table;
use Datafeedr\Api\Wuwei\Migrations\Migration_Interface;

class Migration_20180119155002_Add_Deleted_At_Column_Again extends Migration implements Migration_Interface {

	public function run() {
		$table = new Table( 'datafeedr_networks' );

		$result = $table->add_column( 'deleted_at', 'TIMESTAMP NULL DEFAULT NULL AFTER deleted' );

		error_log( 'deleted_at column added' . ': ' . print_r( $result, true ) );
	}
}