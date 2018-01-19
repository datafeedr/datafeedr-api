<?php namespace Datafeedr\Api;

/**
 * Exit if accessed directly
 *
 * @since 2.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Require helper functions.
 *
 * @since 2.0.0
 */
require_once dirname( __FILE__ ) . '/src/functions.php';

/**
 * Autoload Wuwei Framework Classes.
 *
 * @since 2.0.0
 */
require_once dirname( __FILE__ ) . '/Wuwei/Autoloader.php';

/**
 * Autoload Plugin Classes.
 *
 * @since 2.0.0
 */
require_once dirname( __FILE__ ) . '/src/Autoloader.php';

/**
 * Load Plugin
 *
 * Sets priority to 20. Other dependencies should load after (ie. use a higher priority number).
 *
 * @since 2.0.0
 */
$datafeedr_api = new Plugin( __FILE__ );
add_action( 'plugins_loaded', array( $datafeedr_api, 'load' ), 20 );