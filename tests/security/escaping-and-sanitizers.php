<?php
/**
 * Security regression: sanitizers, Import deserialization and output escaping (audit F-03 to F-08, F-12).
 *
 * Run from the site root:
 *   wp eval-file wp-content/plugins/datafeedr-api/tests/security/escaping-and-sanitizers.php 2>/dev/null | grep RESULT
 *
 * Expect every line to be PASS. The Import checks write dfrapi_networks and dfrapi_merchants, then restore both from a backup (see "options restored").
 */

defined( 'ABSPATH' ) || exit;
require_once DFRAPI_PATH . 'functions/admin.php';
foreach ( [ 'networks', 'merchants', 'configuration', 'import', 'export' ] as $c ) { require_once DFRAPI_PATH . "classes/class-dfrapi-$c.php"; }
$X = '"><script>alert(1)</script>';
function out( $k, $ok, $extra = '' ) { echo 'RESULT ' . ( $ok ? 'PASS' : 'FAIL' ) . " $k $extra\n"; }
function has_raw( $html ) { return strpos( $html, '<script>alert(1)' ) !== false; }

// 1. Real saved data round-trips unchanged (backward compat).
$real_n = get_option( 'dfrapi_networks' ); $real_m = get_option( 'dfrapi_merchants' );
$sn = dfrapi_sanitize_networks_option( $real_n );
out( 'real networks round-trip', $sn == $real_n, 'count=' . count( $real_n['ids'] ?? [] ) );
$sm = dfrapi_sanitize_merchants_option( $real_m );
out( 'real merchants round-trip', $sm === $real_m || $sm['ids'] === array_values( array_map( 'strval', $real_m['ids'] ) ), 'count=' . count( $real_m['ids'] ?? [] ) . ' firsttype=' . gettype( $sm['ids'][0] ?? null ) );

// 2. Sanitizer behaviour.
$s = dfrapi_sanitize_networks_option( [ 'ids' => [ '126' => [ 'nid' => '126', 'aid' => " ab%2Fc$X\x01 ", 'tid' => 't1', 'evil' => 'x' ], 'abc' => [ 'nid' => '5' ], '7' => [ 'aid' => 'no-nid' ] ] ] );
out( 'networks sanitize', array_keys( $s['ids'] ) === [ 126 ] && strpos( $s['ids'][126]['aid'], '<' ) === false && strpos( $s['ids'][126]['aid'], '%2F' ) !== false && ! isset( $s['ids'][126]['evil'] ), json_encode( $s ) );
out( 'merchants sanitize', dfrapi_sanitize_merchants_option( [ 'ids' => '12, 34,abc,-1,12,0' ] ) === [ 'ids' => [ '12', '34' ] ], json_encode( dfrapi_sanitize_merchants_option( [ 'ids' => '12, 34,abc,-1,12,0' ] ) ) );

// 3. Import: objects never instantiated, data sanitized. Back up + restore.
$imp = new Dfrapi_Import();
try {
	$payload = '[NETWORKS]' . serialize( [ 'ids' => [ 126 => [ 'nid' => '126', 'aid' => $X, 'obj' => new ArrayObject( [ 1 ] ) ] ] ] ) . '[/NETWORKS]'
	         . '[MERCHANTS]' . serialize( [ 'ids' => [ '55', new ArrayObject( [ 2 ] ) ] ] ) . '[/MERCHANTS]';
	$imp->import_data( $payload );
	$n = get_option( 'dfrapi_networks' ); $m = get_option( 'dfrapi_merchants' );
	$objs = strpos( serialize( [ $n, $m ] ), 'O:' ) !== false;
	out( 'import no objects + sanitized', ! $objs && strpos( $n['ids'][126]['aid'], '<script' ) === false && $m['ids'] === [ '55' ], json_encode( [ $n, $m ] ) );
	$imp->import_data( '[NETWORKS]O:8:"stdClass":0:{}[/NETWORKS]' );
	out( 'import top-level object ignored', get_option( 'dfrapi_networks' ) == $n );
	$imp->import_data( '[NETWORKS]' . serialize( $real_n ) . '[/NETWORKS][MERCHANTS]' . serialize( $real_m ) . '[/MERCHANTS]' );
	out( 'legacy export re-imports identically', get_option( 'dfrapi_networks' ) == $real_n && array_values( array_map( 'strval', (array) get_option( 'dfrapi_merchants' )['ids'] ) ) == array_values( array_map( 'strval', $real_m['ids'] ) ) );
} finally {
	update_option( 'dfrapi_networks', $real_n ); update_option( 'dfrapi_merchants', $real_m );
	out( 'options restored', get_option( 'dfrapi_networks' ) === $real_n && get_option( 'dfrapi_merchants' ) === $real_m );
}

// Fixtures via filters only from here on.
$nets = get_transient( 'dfrapi_all_networks' );
$first = null; foreach ( $real_n['ids'] as $k => $v ) { $first = $k; break; }
foreach ( $nets as &$net ) { if ( (int) $net['_id'] === (int) $first ) { $net['name'] .= $X; } } unset( $net );
add_filter( 'pre_transient_dfrapi_all_networks', function () use ( $nets ) { return $nets; } );
$fx_n = $real_n; $fx_n['ids'][ $first ]['aid'] = $X; $fx_n['ids'][ $first ]['tid'] = "</textarea>$X";
add_filter( 'pre_option_dfrapi_networks', function () use ( $fx_n ) { return $fx_n; } );

// 4. Export.
ob_start(); ( new Dfrapi_Export() )->section_export_network_data(); $h = ob_get_clean();
out( 'export textarea escaped', ! has_raw( $h ) && substr_count( $h, '</textarea>' ) === 1 );

// 5. Networks page.
$np = new Dfrapi_Networks(); $np->load_settings();
ob_start(); $np->field_network_ids(); $h = ob_get_clean();
out( 'networks page escaped', ! has_raw( $h ) && strpos( $h, '&lt;script&gt;alert(1)' ) !== false, 'len=' . strlen( $h ) );

// 6. Merchants page row.
$mp = ( new ReflectionClass( 'Dfrapi_Merchants' ) )->newInstanceWithoutConstructor();
$h = $mp->format_merchant( [ '_id' => '9"x', 'name' => $X, 'product_count' => 3 ], false );
out( 'merchant row escaped', ! has_raw( $h ) && strpos( $h, 'id="merchant_id_9"' ) !== false && strpos( $h, '9"x' ) === false );

// 7. Search form.
$sf = new Dfrapi_SearchForm();
$h = $sf->chooseBox( 'merchant', [], 0, '1,2' . $X );
out( 'chooseBox value escaped', ! has_raw( $h ) && strpos( $h, '"><script' ) === false );
$h = $sf->networksMerchantsNames( 'network', [ (int) $first ] );
out( 'names escaped', ! has_raw( $h ) && strpos( $h, '&lt;script' ) !== false );
$sf->useSelected = 1; $h = $sf->networksMerchantsPopup( 'network', [] );
out( 'popup escaped', ! has_raw( $h ) && strpos( $h, '&lt;script' ) !== false );

// 8. API error + query display.
$h = dfrapi_display_api_request( [ 'query' => [ "name LIKE $X" ], 'sort' => [ $X ], 'limit' => $X, 'offset' => $X, 'exclude_duplicates' => $X ] );
out( 'query display escaped', ! has_raw( $h ) && substr_count( $h, '<br />' ) === 6 );
ob_start(); dfrapi_output_api_error( [ 'dfrapi_api_error' => [ 'msg' => $X, 'code' => $X, 'class' => $X, 'params' => [ 'query' => [ $X ] ] ] ] ); $h = ob_get_clean();
out( 'api error escaped', ! has_raw( $h ) );

// 9. Configuration validate.
$cfg = ( new ReflectionClass( 'Dfrapi_Configuration' ) )->newInstanceWithoutConstructor();
$v = $cfg->validate( [ 'access_id' => ' abc ', 'secret_key' => "k%2F+/=$X", 'amazon_locale' => 'xx', 'transport_method' => 'evil', 'capi_marketplace' => 'us', 'hs_beacon' => 'off' ] );
out( 'configuration validate', $v['access_id'] === 'abc' && strpos( $v['secret_key'], 'k%2F+/=' ) === 0 && strpos( $v['secret_key'], '<' ) === false && $v['amazon_locale'] === 'us' && $v['transport_method'] === 'curl' && $v['capi_marketplace'] === 'US', json_encode( $v ) );
$cur = get_option( 'dfrapi_configuration' );
$rv = $cfg->validate( $cur ); unset( $cur['disable_api'], $rv['disable_api'] );
out( 'real configuration round-trip', $rv == $cur );
