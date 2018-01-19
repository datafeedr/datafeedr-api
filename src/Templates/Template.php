<?php namespace Datafeedr\Api\Templates;

use Datafeedr\Api\Wuwei\Templates\Template as Template_Abstract;

/**
 * Template class.
 *
 * Extends the Wuwei Template_Abstract class.
 *
 * @since 2.0.0
 */
class Template extends Template_Abstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_file() {

		$path = trailingslashit( realpath( __DIR__ . '/..' ) );
		$path = trailingslashit( $path . 'Views' );
		$file = $path . $this->name;

		/**
		 * Filter the full path to our template file. Examples of a full path might be:
		 *
		 *  - /home/user/public_html/wp-content/plugins/plugin-name/src/Template/price.php
		 *
		 * This filter allows you to modify the file name used when rendering the template.
		 *
		 * @since 2.0.0
		 *
		 * @param string $file The full path to our template file.
		 * @param object $this The Template object.
		 */
		return apply_filters( 'datafeedr/api/template/get_file/file', $file, $this );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data() {

		$data = $this->data;

		/**
		 * Filter the data we will have access to when rendering the template. The $data array is passed
		 * through the extract() function which sets its array keys as variables and are made available
		 * in the template files.
		 *
		 * @since 2.0.0
		 *
		 * @param array $data An array of data to populate the template.
		 * @param object $this The Template object.
		 */
		return apply_filters( 'datafeedr/api/template/get_data/data', $data, $this );
	}
}