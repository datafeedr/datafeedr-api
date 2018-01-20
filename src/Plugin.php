<?php namespace Datafeedr\Api;

use Datafeedr\Api\Wuwei\Event\Manager as Event_Manager;
use Datafeedr\Api\Wuwei\Migrations\Migration_Interface;
use Datafeedr\Api\Wuwei\Shortcode\Shortcode_Interface;
use Datafeedr\Api\Wuwei\Migrations\Migration;

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
	const DB_VERSION = '20180120142457';

	/**
	 * Database version option name.
	 *
	 * @since 2.0.0
	 * @var string DB_VERSION_OPTION_NAME
	 */
	const DB_VERSION_OPTION_NAME = 'datafeedr_api_db_version';

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
	 * Is the user's current Database version.
	 *
	 * @since 2.0.0
	 * @access private
	 * @var string $current_db_version
	 */
	private $current_db_version;

	/**
	 * Plugin constructor.
	 *
	 * @param string $file
	 */
	public function __construct( $file ) {
		$this->loaded             = false;
		$this->file               = $file;
		$this->current_db_version = get_option( self::DB_VERSION_OPTION_NAME, '0' );
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
	 * Returns true if the $new_version is greater than the $current_version. Else returns false.
	 *
	 * @since 2.0.0
	 *
	 * @param string $new_version
	 * @param string $current_version
	 *
	 * @return bool
	 */
	public function version_is_old( $new_version, $current_version ) {
		return ( version_compare( $new_version, $current_version, '>' ) ) ? true : false;
	}

	/**
	 * Loads the plugin into WordPress.
	 */
	public function load() {

		if ( $this->is_loaded() ) {
			return;
		}

		/**
		 * If our DB version is out of date, run our migrations.
		 */
		if ( $this->version_is_old( self::DB_VERSION, $this->current_db_version ) ) {
			$this->run_migrations();
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
	 * Perform any outstanding migration then update the DB_VERSION_OPTION_NAME option with
	 * the migration version.
	 *
	 * @since 2.0.0
	 */
	public function run_migrations() {

		if ( wp_doing_ajax() ) {
			return;
		}

		foreach ( $this->get_migrations() as $migration ) {

			$migration_version = $migration->version();

			if ( ! $this->version_is_old( $migration_version, $this->current_db_version ) ) {
				continue;
			}

			$migration->run();

			update_option( self::DB_VERSION_OPTION_NAME, $migration_version, true );
		}
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
	 * @return Migration_Interface[]
	 */
	public function get_migrations() {
		return [
			new Migrations\Migration_20180118151744_Create_New_Option(),
			new Migrations\Migration_20180119144417_Test_Option_Update(),
			new Migrations\Migration_20180119150524_Create_Networks_Table(),
			new Migrations\Migration_20180119152249_Add_Deleted_At_Column(),
			new Migrations\Migration_20180119155002_Add_Deleted_At_Column_Again(),
			new Migrations\Migration_20180120142456_Drop_Networks_Table(),
			new Migrations\Migration_20180120142457_Drop_Networks_Table(),
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
