<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dfrapi_price_formatted( $int, $currency, $context = '' ) {
	$currency_code = dfrapi_price_currency_code( $currency, $context );
	$price         = dfrapi_price_int_converted( $int, $currency_code, $context );

//	return apply_filters( 'dfrapi_format_price_' . $currency, $int );
}

function dfrapi_price_int_converted( $int, $currency, $context = '' ) {
	$decimal_places      = dfrapi_price_decimal_places( $currency, $context );
	$decimal_separator   = dfrapi_price_decimal_separator( $currency, $context );
	$thousands_separator = dfrapi_price_decimal_separator( $currency, $context );

	$price = number_format( ( $int / 100 ), $decimal_places, $decimal_separator, $thousands_separator );

	return apply_filters( 'dfrapi_price_int_converted', $price, $int, $currency, $context );
}

function dfrapi_price_currency_code( $currency, $context = '' ) {
	return apply_filters( 'dfrapi_price_currency_code', $currency, $context );
}

function dfrapi_price_currency_symbol( $currency, $context = '' ) {
	return apply_filters( 'dfrapi_price_currency_symbol', dfrapi_currency_code_to_sign( $currency ), $context );
}

function dfrapi_price_decimal_places( $currency, $context = '' ) {
	return apply_filters( 'dfrapi_price_decimal_places', 2, $currency, $context );
}

function dfrapi_price_decimal_separator( $currency, $context = '' ) {
	$locale = localeconv();

	return apply_filters( 'dfrapi_price_decimal_separator', $locale['decimal_point'], $currency, $context );
}

function dfrapi_price_thousands_separator( $currency, $context = '' ) {
	$locale = localeconv();

	return apply_filters( 'dfrapi_price_thousands_separator', $locale['thousands_sep'], $currency, $context );
}

//add_filter( 'dfrapi_format_price_USD', function ( $int, $product ) {
//	$sign  = dfrapi_currency_code_to_sign( 'USD' );
//	$price = number_format( ( $int / 100 ), 2, '.', ',' );
//
//	return sprintf( '%s%s %s', $sign, $price, 'USD' );
//}, 10, 2 );
//
//echo dfrapi_price_formatted( 12345, 'DKK', 'compset' );