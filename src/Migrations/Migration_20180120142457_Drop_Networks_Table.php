<?php namespace Datafeedr\Api\Migrations;

use Datafeedr\Api\Wuwei\Database\Table;
use Datafeedr\Api\Wuwei\Migrations\Migration_Interface;

class Migration_20180120142457_Drop_Networks_Table extends Migration implements Migration_Interface {

	public function run() {

		$table = new Table( 'datafeedr_networks' );

		if ( $table->exists() ) {
			return true;
		}

		$data = [
			'id INT(11) NOT NULL COMMENT "Affiliate network ID."',
			'name TEXT NOT NULL COMMENT "Affiliate network name."',
			'network_group VARCHAR(100) NOT NULL COMMENT "Affiliate network general group name."',
			'type VARCHAR(10) NOT NULL COMMENT "The type of records this network supports. Either product or coupon."',
			'merchant_count INT(11) NOT NULL DEFAULT "0" COMMENT "The number of merchants supported by this affiliate network."',
			'product_count INT(11) NOT NULL DEFAULT "0" COMMENT "The number of products supported by this affiliate network."',
			'created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "The timestamp when network was added to this database."',
			'updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "The timestamp when network was updated in this database."',
			'deleted_at TIMESTAMP NULL DEFAULT NULL COMMENT "The timestamp when network was deleted from this database."',
			'PRIMARY KEY  (id)',
			'KEY name (name(100))',
			'KEY network_group (network_group)',
			'KEY type (type)',
		];

		$create_result = $table->create( $data );

		error_log( '$create_result' . ': ' . print_r( $create_result, true ) );
	}
}