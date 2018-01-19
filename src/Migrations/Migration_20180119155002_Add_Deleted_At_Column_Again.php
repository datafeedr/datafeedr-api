<?php namespace Datafeedr\Api\Migrations;

use Datafeedr\Api\Wuwei\Database\Table;
use Datafeedr\Api\Wuwei\Migrations\Migration;

class Migration_20180119155002_Add_Deleted_At_Column_Again implements Migration {

	/**
	 * Indicate what DB version this represents.
	 */
	const VERSION = '20180119155002';

	public function run() {
		$table = new Table( 'datafeedr_networks' );

		$result = $table->add_column( 'deleted_at', 'TIMESTAMP NULL DEFAULT NULL AFTER deleted' );

		error_log( '$result' . ': ' . print_r( $result, TRUE ) );
	}

	public function version() {
		return self::VERSION;
	}
}