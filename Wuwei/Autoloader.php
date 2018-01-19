<?php namespace Datafeedr\Api\Wuwei;

/**
 * Autoloads Wuwei Framework classes.
 *
 * @since 2.0.0
 */
class Autoloader {

	/**
	 * Handles the autoloading of the Wuwei\Framework classes.
	 *
	 * @since 2.0.0
	 *
	 * @param string $class_name Name of class requested.
	 */
	function autoload( $class_name ) {

		// If $class_name does not contain the Datafeedr\Api\Wuwei namespace, return. It's not our class.
		if ( 0 !== strpos( $class_name, __NAMESPACE__ ) ) {
			return;
		}

		// Set default directory separator.
		$ds = \DIRECTORY_SEPARATOR;

		// Remove namespace "Datafeedr\Api\Wuwei" from $class_name
		$class_name = str_replace( __NAMESPACE__, '', $class_name );

		// Trim any leading or trailing backslashes
		$class_name = trim( $class_name, '\\' );

		// Replace any remaining backslashes with proper directory separator and append with PHP extension.
		$class_name = str_replace( '\\', $ds, $class_name ) . '.php';

		// Get full path to classes directory: ~/wp-content/plugins/datafeedr-api-v2/src/Wuwei/Framework
		$classes_dir = untrailingslashit( dirname( __FILE__ ) ) . $ds . 'Framework';

		// Full path to file to be included.
		$full_path = $classes_dir . $ds . $class_name;

		require_once( $full_path );
	}

	/**
	 * Registers Autoloader as an SPL autoloader.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $prepend
	 */
	public static function register( $prepend = false ) {
		spl_autoload_register( array( new self(), 'autoload' ), true, $prepend );
	}
}

/**
 * Register the Autoloader.
 *
 * @since 2.0.0
 */
Autoloader::register();