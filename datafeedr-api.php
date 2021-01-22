<?php
/*
Plugin Name: Datafeedr API
Plugin URI: https://www.datafeedr.com
Description: Connect to the Datafeedr API and configure your API settings.
Author: datafeedr.com
Author URI: https://www.datafeedr.com
License: GPL v3
Requires at least: 3.8
Tested up to: 5.7-alpha
Version: 1.1.3

Datafeedr API Plugin
Copyright (C) 2021, Datafeedr - help@datafeedr.com

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

if ( defined( 'DATAFEEDR_DEV' ) && true === DATAFEEDR_DEV ) {

	add_action( 'admin_notices', 'dfrapi_development_version_notice' );
	function dfrapi_development_version_notice() {
		$class   = 'notice notice-error';
		$title   = 'WARNING!!!';
		$message = 'You are now using a development version of the Datafeedr API plugin. Things WILL BREAK. We do NOT recommend using this version of the plugin! You have been warned.';
		echo sprintf(
			'<div class="%1$s"><p><strong>%2$s</strong><br />%3$s</p></div>',
			esc_attr( $class ),
			esc_html( $title ),
			esc_html( $message )
		);
	}

	include( 'datafeedr-api-v2.php' );

} else {
	include( 'datafeedr-api-v1.php' );
}
