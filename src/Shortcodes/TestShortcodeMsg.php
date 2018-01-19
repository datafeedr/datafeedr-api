<?php namespace Datafeedr\Api\Shortcodes;

use Datafeedr\Api\Wuwei\Shortcode\Shortcode_Interface;

/**
 * Class TestShortcodeMsg
 *
 * Add a test Shortcode.
 *
 * @since 2.0.0
 */
class TestShortcodeMsg implements Shortcode_Interface {

	/**
	 * {@inheritdoc}
	 */
	public static function get_name() {
		return 'datafeedr-test-shortcode';
	}

	/**
	 * {@inheritdoc}
	 */
	public function generate_output( $attrs, $content = '' ) {
		return '<strong>Our shortcodes are working.</strong>';
	}
}