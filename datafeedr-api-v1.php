<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define constants.
 */
define( 'DFRAPI_VERSION', '1.2.15' );
define( 'DFRAPI_URL', plugin_dir_url( __FILE__ ) );
define( 'DFRAPI_PATH', plugin_dir_path( __FILE__ ) );
define( 'DFRAPI_BASENAME', plugin_basename( __FILE__ ) );
define( 'DFRAPI_DOMAIN', 'datafeedr-api' );
define( 'DFRAPI_HOME_URL', 'https://www.datafeedr.com' );
define( 'DFRAPI_KEYS_URL', 'https://members.datafeedr.com/api' );
define( 'DFRAPI_USER_URL', 'https://members.datafeedr.com/' );
define( 'DFRAPI_HELP_URL', 'https://datafeedr.me/contact' );
define( 'DFRAPI_BUG_REPORTS_URL', 'https://datafeedr.me/docs' );
define( 'DFRAPI_QNA_URL', 'https://datafeedr.me/docs' );
define( 'DFRAPI_DOCS_URL', 'https://datafeedr.me/docs' );
define( 'DFRAPI_REPORT_BUG_URL', 'https://datafeedr.me/contact' );
define( 'DFRAPI_ASK_QUESTION_URL', 'https://datafeedr.me/contact' );
define( 'DFRAPI_EMAIL_US_URL', 'https://datafeedr.me/contact' );
define( 'DFRAPI_COMPLEX_QUERY_SCORE', 10000 );
define( 'DFRAPI_EXCESSIVE_MERCHANT_COUNT', 1000 );

/**
 * Require WP 3.8+
 */
add_action( 'admin_init', 'dfrapi_wp_version_check' );
function dfrapi_wp_version_check() {
	$version = get_bloginfo( 'version' );
	if ( version_compare( $version, '3.8', '<' ) ) {
		deactivate_plugins( DFRAPI_BASENAME );
	}
}

/**
 * Notify user that this plugin is deactivated.
 */
add_action( 'admin_notices', 'dfrapi_wp_version_notice' );
function dfrapi_wp_version_notice() {
	$version = get_bloginfo( 'version' );
	if ( version_compare( $version, '3.8', '<' ) ) {
		dfrapi_admin_notice(
			__( 'The Datafeedr API Plugin could not be activated because it requires WordPress version 3.8 or greater. Please upgrade your installation of WordPress.', 'datafeedr-api' ),
			'error',
			__( 'Unsupported WordPress Version', 'datafeedr-api' ),
			'Datafeedr API'
		);

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

/**
 * Load files for all pages.
 */
require_once( DFRAPI_PATH . 'classes/class-datafeedr-plugin-dependency.php' );
require_once( DFRAPI_PATH . 'classes/class-datafeedr-cron.php' );
require_once( DFRAPI_PATH . 'classes/class-datafeedr-timer.php' );
require_once( DFRAPI_PATH . 'classes/class-datafeedr-currency.php' );
require_once( DFRAPI_PATH . 'classes/class-datafeedr-price.php' );
require_once( DFRAPI_PATH . 'classes/class-datafeedr-image-importer.php' );
require_once( DFRAPI_PATH . 'classes/class-dfrapi-image-data.php' );
require_once( DFRAPI_PATH . 'classes/class-dfrapi-image-uploader.php' );
require_once( DFRAPI_PATH . 'functions/functions.php' );
require_once( DFRAPI_PATH . 'functions/upgrade.php' );
require_once( DFRAPI_PATH . 'libraries/datafeedr.php' );
require_once( DFRAPI_PATH . 'libraries/zanox_client.php' );
require_once( DFRAPI_PATH . 'classes/class-dfrapi-searchform.php' );
require_once( DFRAPI_PATH . 'functions/api.php' );
require_once( DFRAPI_PATH . 'functions/filters.php' );
require_once( DFRAPI_PATH . 'functions/actions.php' );

/**
 * Display admin notices for each required plugin that needs to be
 * installed, activated and/or updated.
 *
 * @since 1.0.75
 */
function dfrapi_admin_notice_plugin_dependencies() {

	/**
	 * @var Dfrapi_Plugin_Dependency[] $dependencies
	 */
	$dependencies = array();

	foreach ( $dependencies as $dependency ) {

		$action = $dependency->action_required();

		if ( ! $action ) {
			continue;
		}

		echo '<div class="notice notice-error"><p>';
		echo $dependency->msg( 'Datafeedr API' );
		echo $dependency->link();
		echo '</p></div>';
	}
}

add_action( 'admin_notices', 'dfrapi_admin_notice_plugin_dependencies' );

/**
 * Load files only if we're in the admin section of the site.
 */
if ( is_admin() ) {
	require_once( DFRAPI_PATH . 'classes/class-dfrapi-initialize.php' );
}