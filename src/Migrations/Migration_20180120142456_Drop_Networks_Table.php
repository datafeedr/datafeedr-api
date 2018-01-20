<?php namespace Datafeedr\Api\Migrations;

use Datafeedr\Api\Wuwei\Database\Table;
use Datafeedr\Api\Wuwei\Migrations\Migration_Interface;

class Migration_20180120142456_Drop_Networks_Table extends Migration implements Migration_Interface {

	public function run() {

		$table       = new Table( 'datafeedr_networks' );
		$drop_result = $table->drop();
		error_log( '$drop_result' . ': ' . print_r( $drop_result, true ) );

		$data = [
			"id INT(11) NOT NULL",
			"name TEXT NOT NULL",
			"network_group VARCHAR(100) NOT NULL",
			"type VARCHAR(10) NOT NULL",
			"merchant_count INT(11) NOT NULL DEFAULT '0'",
			"product_count INT(11) NOT NULL DEFAULT '0'",
			"created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
			"updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
			"deleted_at TIMESTAMP NULL DEFAULT NULL",
			"PRIMARY KEY  (id)",
			"KEY name (name(100))",
			"KEY network_group (network_group)",
			"KEY type (type)",
		];

		$create_result = $table->create( $data );

		error_log( '$create_result' . ': ' . print_r( $create_result, true ) );
	}
}