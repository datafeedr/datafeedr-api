<?php

defined( 'ABSPATH' ) || exit;

/**
 * These are the Admin pages associated with the Datafeedr API plugin.
 *
 * These are called from their respective classes.
 */
function dfrapi_setting_pages( $key = false ) {

	$pages = array(
		'dfrapi-configuration' => __( 'Configuration', 'datafeedr-api' ),
		'dfrapi-networks'      => __( 'Networks', 'datafeedr-api' ),
		'dfrapi-merchants'     => __( 'Merchants', 'datafeedr-api' ),
		'dfrapi-tools'         => __( 'Tools', 'datafeedr-api' ),
		'dfrapi-export'        => __( 'Export', 'datafeedr-api' ),
		'dfrapi-import'        => __( 'Import', 'datafeedr-api' ),
		'dfrapi-account'       => __( 'Account', 'datafeedr-api' ),
	);

	if ( isset( $pages[ $key ] ) ) {
		return $pages[ $key ];
	}

}

/**
 * This stores messages to be displayed using the 'admin_notices' action.
 */
function dfrapi_admin_messages( $key = false, $msg = '' ) {
	_deprecated_function( __FUNCTION__, '1.3.0', 'dfrapi_admin_notice()' );
}

/**
 * This gets any notices set by the plugin.
 */
function dfrapi_admin_notices( $key, $messages ) {
	_deprecated_function( __FUNCTION__, '1.3.0' );
}

/**
 * Button text for admin notices.
 */
function dfrapi_fix_button( $url, $button_text = false ) {
	_deprecated_function( __FUNCTION__, '1.3.0' );
}

/**
 * Return plan IDs & names.
 */
function dfrapi_get_membership_plans(): array {
	return array(
		10300    => 'Private',
		10200    => 'Free',
		101000   => 'Starter',
		1025000  => 'Basic',
		1125000  => 'Beta Tester',
		10100000 => 'Professional',
		10250000 => 'Enterprise',
		30001    => 'Combo 1',
		30002    => 'Combo 2',
		30003    => 'Combo 3',
	);
}

/**
 * Convert network group names to a css class name.
 */
function dfrapi_group_name_to_css( $group ) {
	if ( is_string( $group ) ) {
		return strtolower(
			str_replace(
				array( " ", "-", "." ),
				"",
				$group
			)
		);
	} elseif ( is_array( $group ) ) {
		$name = str_replace( array( " ", "-", "." ), "", $group['group'] );
		$type = ( $group['type'] == 'coupons' ) ? '_coupons' : '';

		return strtolower( $name . $type );
	}
}

/**
 * Adds global "support" content to "Help" tabs for all Datafeedr plugins.
 */
function dfrapi_help_tab( $screen ) {

	$screen->add_help_tab( array(
		'id'      => 'dfrapi_support_tab',
		'title'   => __( 'Support', 'datafeedr-api' ),
		'content' =>
			'<h2>' . __( "Datafeedr Support", 'datafeedr-api' ) . '</h2>' .
			'<p>' . sprintf( __( 'Find answers to common questions and problems in the <a href="%s?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">documentation</a> and in the <a href="%s?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">support forum</a>', 'datafeedr-api' ), DFRAPI_DOCS_URL, DFRAPI_QNA_URL ) . '. ' . __( 'For additional help, feel free to contact us using the links below.', 'datafeedr-api' ) . '</p>' .
			'<p><a href="' . DFRAPI_ASK_QUESTION_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" class="button button-primary" target="_blank">' . __( 'Post a Question', 'datafeedr-api' ) . '</a> (' . __( 'recommended', 'datafeedr-api' ) . ')</p>' .
			'<p><a href="' . DFRAPI_EMAIL_US_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" class="button" target="_blank">' . __( 'Email Us', 'datafeedr-api' ) . '</a></p>'

	) );

	$screen->add_help_tab( array(
		'id'      => 'dfrapi_bug_tab',
		'title'   => __( 'Found a bug?', 'datafeedr-api' ),
		'content' =>
			'<h2>' . __( "Found a bug?", 'datafeedr-api' ) . '</h2>' .
			'<p>' . sprintf( __( 'If you find a bug within Datafeedr, check the <a href="%s?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">Bug Reports</a> to see if it’s already been reported. Report a new bug with as much description as possible (context, screenshots, error log, etc.) Thank you!', 'datafeedr-api' ), DFRAPI_BUG_REPORTS_URL ) . '</p>' .
			'<p><a href="' . DFRAPI_REPORT_BUG_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" class="button button-primary" target="_blank">' . __( 'Report a bug', 'datafeedr-api' ) . '</a></p>'

	) );

	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', 'datafeedr-api' ) . '</strong></p>' .
		'<p><a href="' . DFRAPI_HOME_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">' . __( 'About Datafeedr', 'datafeedr-api' ) . '</a></p>' .
		'<p><a href="' . DFRAPI_KEYS_URL . '?utm_source=plugin&utm_medium=link&utm_campaign=helptab" target="_blank">' . __( 'Datafeedr API Keys', 'datafeedr-api' ) . '</a></p>'
	);

	return $screen;
}
