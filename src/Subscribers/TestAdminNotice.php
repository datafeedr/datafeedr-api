<?php namespace Datafeedr\Api\Subscribers;

use Datafeedr\Api\Templates\Template;
use Datafeedr\Api\Wuwei\Event\Subscriber_Interface;

/**
 * Class TestAdminNotice
 *
 * @since 2.0.0
 */
class TestAdminNotice extends Subscriber implements Subscriber_Interface {

	/**
	 * {@inheritdoc}
	 */
	public static function get_subscribed_events() {
		return array(
			'admin_notices' => 'handle',
		);
	}

	/**
	 * Echo notice if needed.
	 *
	 * @since 2.0.0
	 */
	public function handle() {

		$classes = 'notice-info';
		$message = __( 'Testing admin notices and templates. ', 'datafeedr/api' );

		$data = array(
			'classes' => esc_attr( $classes ),
			'message' => esc_html( $message ),
		);

		echo ( new Template( 'admin/notices', $data ) )->render();
	}
}