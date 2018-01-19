<?php namespace Datafeedr\Api\Wuwei\Shortcode;

/**
 * Interface Contract
 *
 * A shortcode represents the shortcode registered with the WordPress shortcode API.
 *
 * @link https://github.com/postmatic/elevated-comments (copyright)
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
interface Shortcode_Interface {

	/**
	 * Get the tag name used by the shortcode.
	 *
	 * @return string
	 */
	public static function get_name();

	/**
	 * Generate the output of the shortcode.
	 *
	 * @param array $attrs
	 * @param string $content
	 *
	 * @return string
	 */
	public function generate_output( $attrs, $content = '' );
}