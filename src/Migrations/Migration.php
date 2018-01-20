<?php namespace Datafeedr\Api\Migrations;

/**
 * Class Migration
 * @package Datafeedr\Api\Migrations
 *
 * @since 2.0.0
 *
 * Contains the version() method for extracting the version number from the
 * Migration class name.
 */
abstract class Migration {

	/**
	 * Returns the migration version by extracting it from the Migration class name.
	 *
	 * For example, if the class is named Migration_20180118151744_Create_New_Option()
	 *
	 * Then this function will return 20180118151744.
	 *
	 * If the version was not found in this class name, this function will return a very high number
	 * which should never be considered a proper DB version number because I don't expect there will
	 * still be WordPress websites in the year 3000. ;)
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function version() {
		$called_class = get_called_class();
		$class_parts  = explode( '_', $called_class );
		foreach ( $class_parts as $part ) {
			if ( is_numeric( $part ) ) {
				return $part;
			}
		}

		/**
		 * Return a high version number because one was not found in the called class name.
		 *
		 * This will prevent the migration script from rerunning the same migration over and over
		 * because no version was returned.
		 */
		return '30000111111111';
	}
}
