<?php
/**
 * Security regression: Datafeedr and Effiliation API requests use HTTPS (audit F-02).
 *
 * Run from the site root:
 *   wp eval-file wp-content/plugins/datafeedr-api/tests/security/api-https.php 2>/dev/null | grep RESULT
 *
 * Makes one live Datafeedr API status request. In the Claude sandbox, allow api.datafeedr.com.
 */

defined( 'ABSPATH' ) || exit;
// 2.2 — HTTPS
$a = dfrapi_api(); $r = new ReflectionProperty( $a, '_url' ); $r->setAccessible( true );
echo "RESULT url=" . $r->getValue( $a ) . "\n";
$s = dfrapi_api_get_status();
echo "RESULT status_ok=" . ( isset( $s['dfrapi_api_error'] ) ? 'NO ' . $s['dfrapi_api_error']['msg'] : 'yes plan_id=' . $s['plan_id'] ) . "\n";
echo "RESULT effiliation=" . dfrapi_get_effiliation_product_feeds_url( 'k' ) . "\n";

