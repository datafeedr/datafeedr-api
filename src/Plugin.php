<?php namespace Datafeedr\Api;

use Datafeedr\Api\Wuwei\Event\Manager as Event_Manager;
use Datafeedr\Api\Wuwei\Shortcode\Shortcode_Interface;

/**
 * Class Plugin.
 *
 * @since 2.0.0
 */
class Plugin {

	/**
	 * Plugin version.
	 *
	 * @since 2.0.0
	 * @var string VERSION Plugin version.
	 */
	const VERSION = '2.0.0dev1';

	/**
	 * Database version.
	 *
	 * @since 2.0.0
	 * @var string DB_VERSION Database version.
	 */
	const DB_VERSION = '20180119105502';

	/**
	 * The plugin event manager.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var \Datafeedr\Api\Wuwei\Event\Manager
	 */
	private $event_manager;

	/**
	 * Flag to track if the plugin is loaded.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var bool
	 */
	private $loaded;

	/**
	 * The full path to the main plugin file.
	 *
	 * Example: ~/wp-content/plugins/datafeedr-api/datafeedr-api-v2.php
	 *
	 * @since 2.0.0
	 * @access private
	 * @var string $file
	 */
	private $file;

	/**
	 * Plugin constructor.
	 *
	 * @param string $file
	 */
	public function __construct( $file ) {
		$this->loaded = false;
		$this->file   = $file;
	}

	/**
	 * Checks if the plugin is loaded.
	 *
	 * @return bool
	 */
	public function is_loaded() {
		return $this->loaded;
	}

	/**
	 * Loads the plugin into WordPress.
	 */
	public function load() {

		if ( $this->is_loaded() ) {
			return;
		}

		$this->event_manager = new Event_Manager();

		foreach ( $this->get_subscribers() as $subscriber ) {
			$this->event_manager->add_subscriber( $subscriber );
		}

		/**
		 * Register Shortcodes.
		 *
		 * @since 2.0.0
		 *
		 * @link https://carlalexander.ca/designing-class-assemble-plugin/#comment-3260130577
		 */
		foreach ( $this->get_shortcodes() as $shortcode ) {
			$this->register_shortcode( $shortcode );
		}

		$this->loaded = true;
	}

	/**
	 * Register the given shortcode with the WordPress shortcode API.
	 *
	 * @since 2.0.0
	 *
	 * @param \Datafeedr\Api\Wuwei\Shortcode\Shortcode_Interface $shortcode
	 */
	private function register_shortcode( Shortcode_Interface $shortcode ) {
		add_shortcode( $shortcode::get_name(), array( $shortcode, 'generate_output' ) );
	}

	/**
	 * Get the plugin event subscribers (ie. actions and filters).
	 *
	 * @since 2.0.0
	 *
	 * @return \Datafeedr\Api\Wuwei\Event\Subscriber_Interface[]
	 */
	private function get_subscribers() {
		return [
			new Subscribers\TestAdminNotice(),
		];
	}

	/**
	 * Get the plugin shortcodes.
	 *
	 * @since 2.0.0
	 *
	 * @return \Datafeedr\Api\Wuwei\Shortcode\Shortcode_Interface[]
	 */
	private function get_shortcodes() {
		return [
			new Shortcodes\TestShortcodeMsg(),
		];
	}

	/**
	 * Returns full path to the plugin's directory.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function path() {
		return plugin_dir_path( $this->file );
	}

	/**
	 * Returns full URL to the plugin's directory.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function url() {
		return plugin_dir_url( $this->file );
	}
}
