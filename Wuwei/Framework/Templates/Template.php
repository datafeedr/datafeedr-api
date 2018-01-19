<?php namespace Datafeedr\Api\Wuwei\Templates;

/**
 * Abstract Class Template.
 *
 * This is the abstract class for Template_Public() and Template_Admin().
 * Handles the $data array and $name of path to template file.
 *
 * @since 2.0.0
 */
abstract class Template {

	/**
	 * The relative path to template file (file extension optional).
	 *
	 * @since 2.0.0
	 * @access public
	 * @var string $name Name of path to template file. Example: 'product/price'.
	 */
	public $name;

	/**
	 * The data to use in the template.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var array $data Optional. Array of data for the template to use.
	 */
	public $data;

	/**
	 * Template Constructor.
	 *
	 * This sets our $name and $data properties.
	 *
	 * @since 2.0.0
	 *
	 * @param string $name Relative path to template file.
	 * @param array $data Optional. Array of data to use in the template file.
	 */
	public function __construct( $name, $data = array() ) {
		$this->set_name( $name );
		$this->set_data( $data );
	}

	/**
	 * Forces our child classes to have this function as this class relies on the get_file()
	 * method to return the full path to the template file.
	 *
	 * Returns the full path to the template file we will use.
	 *
	 * @since 2.0.0
	 *
	 * @return string Full path to the template file.
	 */
	abstract protected function get_file();

	/**
	 * Forces our child classes to have this function as this function returns the
	 * array of data we will use in our themes.
	 *
	 * We needed to make this abstract because our admin template class does not
	 * run the $data array through apply_filters() whereas the public template file does.
	 *
	 * @since 2.0.0
	 *
	 * @return array Array of $data that will be extracted for use in the template file.
	 */
	abstract protected function get_data();

	/**
	 * Sets the file name.
	 *
	 * Given a $name like /product/partials/price, this function will:
	 *
	 *  - strip the leading & trailing forward slashes
	 *  - replace all forward slashes with DIRECTORY_SEPARATOR
	 *  - If the $name does not have an extension, will append ".php" to the $name;
	 *
	 * @since 2.0.0
	 *
	 * @param string $name The template file name. Example: 'product/partials/price
	 */
	private function set_name( $name ) {
		$name = trim( $name, '/' );
		$name = str_replace( '/', DIRECTORY_SEPARATOR, $name );
		$path = pathinfo( $name );

		$this->name = ( ! isset( $path['extension'] ) || empty( $path['extension'] ) ) ? $name . '.php' : $name;
	}

	/**
	 * Sets the $this->data property.
	 *
	 * If $data['attributes'] is not set, then it also sets that array item to an
	 * empty array to avoid "PHP Notice:  Undefined variable: attributes" errors in
	 * the generic templates or anywhere $attributes is used in a template file.
	 *
	 * @since 2.0.0
	 *
	 * @param array $data An array of data to be used in the template file.
	 */
	private function set_data( $data ) {
		if ( ! isset( $data['attributes'] ) ) {
			$data['attributes'] = array();
		}

		$this->data = $data;
	}

	/**
	 * This inserts another template file into a parent template file.
	 *
	 * This basically acts as an include() call in a template file. However,
	 * this passes false to render() so that the results of this insert() call are not
	 * buffered. Our parent template already handles that. No need to buffer the output twice.
	 *
	 * Usage: <?php $this->insert( 'products/product', array( 'product' => $product ) ); ?>
	 *
	 * @since 2.0.0
	 *
	 * @param string $name The name of the relative template file.
	 * @param array $data Optional. An array of data to use in the template file. Will be appended to original $data array.
	 */
	public function insert( $name, $data = array() ) {
		$this->set_name( $name );
		$this->data = array_merge( $this->get_data(), $data );
		$this->render( false );
	}

	/**
	 * Returns formatted list of attributes for an HTML element.
	 *
	 * $attrs should be formatted like:
	 *
	 *  array(
	 *      'href'   => 'http://google.com',
	 *      'target' => '_blank',
	 *      'class'  => 'btn btn-sm',
	 *  )
	 *
	 * @since 2.0.0
	 *
	 * @param array $attrs Optional. Array of attributes.
	 *
	 * @return string HTML.
	 */
	public function attributes( $attrs = array() ) {
		$attributes = '';
		$format     = '%s="%s" ';
		foreach ( $attrs as $k => $v ) {
			$attributes .= sprintf( $format, $k, esc_attr( $v ) );
		}

		return trim( $attributes );
	}

	/**
	 * Includes the proper template file and extracts $data for use from
	 * within the template file.
	 *
	 * @since 2.0.0
	 *
	 * @param boolean $buffer Optional. Whether or not to buffer the output. Default: true.
	 *
	 * @return string|null Return error message if file is missing, or null if we did the include().
	 */
	public function render( $buffer = true ) {

		$file = $this->get_file();

		if ( ! is_file( $file ) ) {
			return 'Template file ' . $file . ' does not exist.';
		}

		$data = $this->get_data();

		extract( $data );

		if ( $buffer ) {
			ob_start();
		}

		include( $file );

		if ( $buffer ) {
			return ob_get_clean();
		}

		return null;
	}
}
