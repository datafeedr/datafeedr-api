<?php
/**
 * Security regression: Tools capability, notice and Beacon gating, XML/API-error robustness, Awin bearer header (audit F-09 to F-18).
 *
 * Run from the site root:
 *   wp eval-file wp-content/plugins/datafeedr-api/tests/security/hardening.php 2>/dev/null | grep RESULT
 *
 * The Awin check makes one live Awin request when an Awin network is selected and backs up/restores the cached programme list. In the Claude sandbox, allow api.awin.com and api.datafeedr.com.
 * The two "awin live" checks depend on Awin's API and can fail transiently if Awin throttles (20 requests/minute)
 * or times out. Wait a minute and re-run before treating a failure there as a regression.
 */

defined( 'ABSPATH' ) || exit;
require_once DFRAPI_PATH . 'functions/admin.php';
function out( $k, $ok, $x = '' ) { echo 'RESULT ' . ( $ok ? 'PASS' : 'FAIL' ) . " $k $x\n"; }
$admin  = get_users( [ 'role' => 'administrator', 'number' => 1 ] )[0]->ID;
$editor = get_users( [ 'role' => 'editor', 'number' => 1 ] );
$editor = $editor ? $editor[0]->ID : 0;
$sub    = get_users( [ 'role' => 'subscriber', 'number' => 1 ] )[0]->ID;

// 4.1 Tools capability.
wp_set_current_user( $editor ?: $sub );
out( 'tools cap: ' . ( $editor ? 'editor' : 'subscriber' ) . ' denied', ! current_user_can( dfrapi_tools_capability() ) );
wp_set_current_user( $admin );
out( 'tools cap: admin allowed', current_user_can( dfrapi_tools_capability() ) );

// F-18 notices + F-14 beacon (force "datafeedr page" via $_GET).
$_GET['page'] = 'dfrapi';
foreach ( [ 'subscriber' => $sub, 'admin' => $admin ] as $label => $uid ) {
	wp_set_current_user( $uid );
	ob_start(); do_action( 'admin_notices' ); $n = ob_get_clean();
	ob_start(); dfrapi_include_helpscout_beacon(); $b = ob_get_clean();
	echo "RESULT INFO $label: dfrapi notices=" . substr_count( $n, 'Datafeedr API' ) . " beacon=" . ( strpos( $b, 'Beacon' ) !== false ? 'loaded' : 'none' ) . "\n";
}

// F-16 bad XML.
add_filter( 'pre_http_request', $f = function () { return [ 'headers' => [], 'body' => 'not xml <<<', 'response' => [ 'code' => 200, 'message' => 'OK' ], 'cookies' => [] ]; } );
$r = @dfrapi_get_xml_response( 'https://example.invalid/x.xml' );
remove_filter( 'pre_http_request', $f );
out( 'bad xml returns WP_Error', is_wp_error( $r ), is_wp_error( $r ) ? $r->get_error_code() : gettype( $r ) );

// F-16 html_output_api_error with params (previously fatal).
ob_start(); dfrapi_html_output_api_error( [ 'dfrapi_api_error' => [ 'msg' => 'm', 'code' => 1, 'class' => 'C', 'params' => [ 'query' => [ 'name LIKE x' ] ] ] ] ); $h = ob_get_clean();
out( 'html_output_api_error with params', strpos( $h, 'addFilter' ) !== false );

$admin = get_users( [ 'role' => 'administrator', 'number' => 1 ] )[0]->ID;
$sub   = get_users( [ 'role' => 'subscriber', 'number' => 1 ] )[0]->ID;

// Force the "API keys missing" notice condition without touching the DB.
add_filter( 'pre_option_dfrapi_configuration', function () { return [ 'access_id' => '', 'secret_key' => '' ]; } );
foreach ( [ 'subscriber' => $sub, 'admin' => $admin ] as $label => $uid ) {
	wp_set_current_user( $uid );
	ob_start(); dfrapi_datafeedr_api_keys_do_not_exist_notice(); $n = ob_get_clean();
	out( "notice gating $label", $label === 'admin' ? strpos( $n, 'API Keys Missing' ) !== false : $n === '' );
}
remove_all_filters( 'pre_option_dfrapi_configuration' );

// Awin: real cache miss, with backup/restore of the cached programme list.
foreach ( dfrapi_api_get_all_networks() as $n ) {
	if ( (int) $n['group_id'] === 10006 && in_array( (int) $n['_id'], dfrapi_get_selected_network_ids(), true ) ) { $net = $n; break; }
}
$aid   = dfrapi_get_affiliate_and_tracking_id( $net['_id'], 'aid' );
$key   = 'dfrapi_awin_joined_' . $aid;
$saved = get_transient( $key );
$seen  = null;
add_filter( 'http_request_args', function ( $args, $url ) use ( &$seen ) { if ( strpos( $url, 'api.awin.com' ) !== false ) { $seen = [ $url, $args['headers']['Authorization'] ?? '' ]; } return $args; }, 10, 2 );
delete_transient( $key );
try {
	$res = dfrapi_remove_unapproved_awin_merchants( dfrapi_api_get_all_merchants( $net['_id'] ), $net );
	out( 'awin bearer header used, no token in URL', $seen && strpos( $seen[0], 'accessToken' ) === false && strpos( $seen[1], 'Bearer ' ) === 0, $seen ? preg_replace( '#publishers/[^/]+#', 'publishers/***', $seen[0] ) : 'no request' );
	out( 'awin live result', is_array( $res ), is_wp_error( $res ) ? $res->get_error_message() : 'kept=' . count( $res ) );
	out( 'awin fresh cache matches previous', is_array( get_transient( $key ) ) && count( get_transient( $key ) ) === count( (array) $saved ) );
} finally {
	if ( get_transient( $key ) === false && $saved !== false ) { set_transient( $key, $saved, 12 * HOUR_IN_SECONDS ); echo "RESULT INFO restored previous cache\n"; }
}
