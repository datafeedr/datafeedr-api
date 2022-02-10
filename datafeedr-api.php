<?php
/*
Plugin Name: Datafeedr API
Plugin URI: https://www.datafeedr.com
Description: Connect to the Datafeedr API and configure your API settings.
Author: datafeedr.com
Author URI: https://www.datafeedr.com
Text Domain: datafeedr-api
License: GPL v3
Requires PHP: 7.4
Requires at least: 3.8
Tested up to: 6.0-alpha
Version: 1.2.17

Datafeedr API Plugin
Copyright (C) 2022, Datafeedr - help@datafeedr.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Define constants.
 */
define( 'DFRAPI_VERSION', '1.2.17' );
define( 'DFRAPI_URL', plugin_dir_url( __FILE__ ) ); // https://example.com/wp-content/plugins/datafeedr-api/
define( 'DFRAPI_PATH', plugin_dir_path( __FILE__ ) ); // /absolute/path/to/wp-content/plugins/datafeedr-api/
define( 'DFRAPI_BASENAME', plugin_basename( __FILE__ ) ); // datafeedr-api/datafeedr-api.php
define( 'DFRAPI_PLUGIN_FILE', __FILE__ ); // /absolute/path/to/wp-content/plugins/datafeedr-api/datafeedr-api.php
define( 'DFRAPI_DOMAIN', 'datafeedr-api' ); // Deprecated as of 2022-02-10 14:18:51
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
 * Compatibility Check.
 *
 * @param bool $network_wide
 *
 * @return void
 */
function dfrapi_register_activation( bool $network_wide ) {

	// Check that minimum WordPress requirement has been met.
	$version = get_bloginfo( 'version' );
	if ( version_compare( $version, '3.8', '<' ) ) {
		deactivate_plugins( DFRAPI_BASENAME );
		wp_die( __(
			'The Datafeedr API Plugin could not be activated because it requires WordPress version 3.8 or greater. Please upgrade your installation of WordPress.',
			'datafeedr-api'
		) );
	}

	// Check that plugin is not being activated at the Network level on Multisite sites.
	if ( $network_wide && is_multisite() ) {
		deactivate_plugins( DFRAPI_BASENAME );
		wp_die( __(
			'The Datafeedr API plugin cannot be activated at the Network-level. Please activate the Datafeedr API plugin at the Site-level instead.',
			'datafeedr-api'
		) );
	}
}

register_activation_hook( __FILE__, 'dfrapi_register_activation' );

/**
 * Load Functions
 */
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/functions/functions.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/functions/upgrade.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/functions/api.php';

/**
 * Load files only if we're in the WordPress Admin Area of the site.
 */
if ( is_admin() ) {

	// Core admin functions.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/functions/admin-functions.php';

	// Load required classes.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-env.php'; // Checks environment for any problems.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-configuration.php'; // Configuration page.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-networks.php'; // Networks page.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-merchants.php'; // Merchants page.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-tools.php'; // Tools page.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-export.php'; // Export page.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-import.php'; // Import page.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-account.php'; // Account page.
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-help.php'; // Help tabs.

	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-initialize.php';


	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/hooks/admin/admin-notices.php';
	require_once dirname( DFRAPI_PLUGIN_FILE ) . '/hooks/admin/enqueue-scripts.php';
}

/**
 * Load Classes
 */
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-datafeedr-plugin-dependency.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-datafeedr-cron.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-datafeedr-timer.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-datafeedr-currency.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-datafeedr-price.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-datafeedr-image-importer.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-image-data.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-image-uploader.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/classes/class-dfrapi-searchform.php';

/**
 * Load Libraries
 */
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/libraries/datafeedr.php';

/**
 * Load Hooks
 */
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/functions/filters.php';
require_once dirname( DFRAPI_PLUGIN_FILE ) . '/functions/actions.php';
