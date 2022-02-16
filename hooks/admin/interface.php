<?php

defined( 'ABSPATH' ) || exit;

/**
 * Includes HelpScout Beacon if enabled and if on a Datafeedr-specific page.
 *
 * @since 1.0.84
 */
function dfrapi_include_helpscout_beacon() {

	$options = get_option( 'dfrapi_configuration', [] );

	if ( isset( $options['hs_beacon'] ) && 'off' === $options['hs_beacon'] ) {
		return;
	}

	if ( ! dfrapi_is_datafeedr_admin_page() ) {
		return;
	}

	include_once DFRAPI_PATH . 'js/helpscout-beacon.php';
}

add_action( 'admin_footer', 'dfrapi_include_helpscout_beacon' );

/**
 * Add link to Configuration page to action links on Plugins page.
 *
 * @param array $links
 *
 * @return array
 */
function dfrapi_add_plugin_action_links( array $links ): array {
	return array_merge(
		$links,
		[
			'config' => sprintf
			(
				'<a href="%1$s">%2$s</a>',
				dfrapi_configuration_page_url(),
				__( 'Configuration', 'datafeedr-api' )
			)
		]
	);
}

add_filter( 'plugin_action_links_' . DFRAPI_BASENAME, 'dfrapi_add_plugin_action_links' );

/**
 * Add links to Documentation and Contact page to meta row on Plugins page.
 *
 * @param array $links
 * @param string $plugin_file
 *
 * @return array
 */
function dfrapi_add_plugin_row_meta( array $links, string $plugin_file ): array {
	if ( $plugin_file === DFRAPI_BASENAME ) {
		$links[] = sprintf( '<a href="' . DFRAPI_DOCS_URL . '" target="_blank" rel="noopener nofollow">%s</a>', __( 'Documentation', 'datafeedr-api' ) );
		$links[] = sprintf( '<a href="' . DFRAPI_HELP_URL . '" target="_blank" rel="noopener nofollow">%s</a>', __( 'Support', 'datafeedr-api' ) );
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'dfrapi_add_plugin_row_meta', 10, 2 );
