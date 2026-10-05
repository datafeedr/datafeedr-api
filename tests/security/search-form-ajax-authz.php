<?php
/**
 * Security regression: search form AJAX rejects requests without a valid nonce and capability (audit F-01).
 *
 * Run from the site root:
 *   wp eval-file wp-content/plugins/datafeedr-api/tests/security/search-form-ajax-authz.php 2>/dev/null | grep RESULT
 *
 * Expect three "DIE code=403" lines and "sanitized-ok". Needs a subscriber and an administrator user.
 */

defined( 'ABSPATH' ) || exit;
add_filter( 'wp_doing_ajax', '__return_true' );
add_filter( 'wp_die_ajax_handler', function () { return function ( $m, $t, $a ) { throw new Exception( 'DIE code=' . ( $a['response'] ?? '?' ) . ' msg=' . ( is_scalar( $m ) ? $m : '' ) ); }; } );
if ( class_exists( 'Dfrapi_Initialize' ) === false ) { require_once DFRAPI_PATH . 'classes/class-dfrapi-initialize.php'; }
global $dfrapi_initialize;
$init = $dfrapi_initialize ?: new Dfrapi_Initialize();
$sub   = get_users( [ 'role' => 'subscriber', 'number' => 1 ] )[0]->ID;
$admin = get_users( [ 'role' => 'administrator', 'number' => 1 ] )[0]->ID;
function probe( $init, $uid, $with_nonce, $label ) {
	wp_set_current_user( $uid );
	$_POST = $_REQUEST = [ 'action' => 'search_form', 'command' => 'choose_network', 'value' => '', 'useSelected' => '0' ];
	if ( $with_nonce ) { $_POST['dfrapi_security'] = $_REQUEST['dfrapi_security'] = wp_create_nonce( 'dfrapi_search_form' ); }
	ob_start();
	try { $init->ajax_search_form(); } catch ( Exception $e ) { ob_end_clean(); echo "RESULT $label => " . $e->getMessage() . "\n"; return; }
}
probe( $init, $sub, false, 'subscriber no-nonce' );
probe( $init, $sub, true,  'subscriber nonce   ' );
probe( $init, $admin, false, 'admin no-nonce     ' );
// value sanitizing
$_POST = [ 'command' => 'names_network', 'value' => '126,abc,-5,0,126,<b>x</b>' ];
$s = new Dfrapi_SearchForm(); $s->ajaxHandler();
echo "RESULT sanitized-ok\n";
