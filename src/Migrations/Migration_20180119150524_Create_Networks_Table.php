<?php namespace Datafeedr\Api\Migrations;

use Datafeedr\Api\Wuwei\Database\Table;
use Datafeedr\Api\Wuwei\Migrations\Migration;

class Migration_20180119150524_Create_Networks_Table implements Migration {

	/**
	 * Indicate what DB version this represents.
	 */
	const VERSION = '20180119150524';

	public function run() {

		$table = new Table( 'datafeedr_networks' );

		$data = [
			"id int(11) NOT NULL",
			"name text NOT NULL",
			"network_group varchar(100) NOT NULL",
			"type varchar(10) NOT NULL",
			"merchant_count int(11) NOT NULL DEFAULT '0'",
			"product_count int(11) NOT NULL DEFAULT '0'",
			"created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
			"updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
			"PRIMARY KEY  (id)",
			"KEY name (name(100))",
			"KEY network_group (network_group)",
			"KEY type (type)",
		];

		$result = $table->create( $data );

		error_log( '$result' . ': ' . print_r( $result, TRUE ) );

	}

	public function version() {
		return self::VERSION;
	}
}