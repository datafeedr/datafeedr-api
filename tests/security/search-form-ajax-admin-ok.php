<?php
/**
 * Security regression: an administrator with a valid nonce still gets the network list from the search form AJAX (audit F-01).
 *
 * Run from the site root:
 *   wp eval-file wp-content/plugins/datafeedr-api/tests/security/search-form-ajax-admin-ok.php 2>/dev/null | grep RESULT
 *
 * This one ends in die(), so count the output instead of grepping RESULT:\n *   wp eval-file …/search-form-ajax-admin-ok.php 2>/dev/null | grep -o "type='checkbox'" | wc -l   (expect > 0)
 */

defined( 'ABSPATH' ) || exit;
if ( class_exists( 'Dfrapi_Initialize' ) === false ) { require_once DFRAPI_PATH . 'classes/class-dfrapi-initialize.php'; }
global $dfrapi_initialize;
$init = $dfrapi_initialize ?: new Dfrapi_Initialize();
wp_set_current_user( get_users( [ 'role' => 'administrator', 'number' => 1 ] )[0]->ID );
$_POST = $_REQUEST = [ 'action' => 'search_form', 'command' => 'choose_network', 'value' => '', 'useSelected' => '0', 'dfrapi_security' => wp_create_nonce( 'dfrapi_search_form' ) ];
$init->ajax_search_form();
