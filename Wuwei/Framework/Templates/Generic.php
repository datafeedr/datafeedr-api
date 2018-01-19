<?php namespace Datafeedr\Api\Wuwei\Templates;

/**
 * Template_Generic class.
 *
 * Extends the Template_Abstract class. Handles the get_file() and get_data() requests.
 *
 * @since 2.0.0
 */
class Generic extends Template {

	/**
	 * Returns full path to generic template file.
	 *
	 * @since 2.0.0
	 *
	 * @return string Full path to template file.
	 */
	public function get_file() {
		$ds   = \DIRECTORY_SEPARATOR;
		$path = realpath( __DIR__ . '/..' ) . $ds . 'Views' . $ds . 'generic' . $ds; // Requires PHP 5.3+
		$file = $path . $this->name;

		return $file;
	}

	/**
	 * Returns the array of data to use in our templates.
	 *
	 * @since 2.0.0
	 *
	 * @return array The data for use in our template file.
	 */
	public function get_data() {
		return $this->data;
	}
}
