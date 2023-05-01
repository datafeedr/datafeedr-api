<?php

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue WordPress Admin Area CSS files.
 *
 * @return void
 */
function dfrapi_admin_enqueue_styles() {
	wp_register_style( 'dfrapi_css', DFRAPI_URL . 'css/style.css', false, DFRAPI_VERSION );
	wp_register_style( 'dfrapi_searchform', DFRAPI_URL . 'css/searchform.css', false, DFRAPI_VERSION );
	wp_enqueue_style( 'dfrapi_css' );
	wp_enqueue_style( 'dfrapi_searchform' );
}

add_action( 'admin_enqueue_scripts', 'dfrapi_admin_enqueue_styles' );

/**
 * Enqueue WordPress Admin Area JavaScript files.
 *
 * @return void
 */
function dfrapi_admin_enqueue_scripts() {
	wp_register_script( 'dfrapi_general_js', DFRAPI_URL . 'js/general.js', array( 'jquery' ), DFRAPI_VERSION, true );
	wp_register_script( 'dfrapi_searchfilter_js', DFRAPI_URL . 'js/searchfilter.js', array( 'jquery' ), DFRAPI_VERSION );
	wp_register_script( 'dfrapi_merchants_js', DFRAPI_URL . 'js/merchants.js', array( 'jquery' ), DFRAPI_VERSION );
	wp_register_script( 'dfrapi_searchform_js', DFRAPI_URL . 'js/searchform.js', array( 'jquery' ), DFRAPI_VERSION );
	wp_register_script( 'dfrapi_jquery_reveal_js', DFRAPI_URL . 'js/jquery.reveal.js', array( 'jquery' ), DFRAPI_VERSION );
	wp_enqueue_script( 'dfrapi_general_js' );
	wp_enqueue_script( 'dfrapi_searchfilter_js' );
	if ( ! is_customize_preview() ) {
		wp_enqueue_script( 'dfrapi_merchants_js' );
	}
	wp_enqueue_script( 'dfrapi_searchform_js' );
	wp_enqueue_script( 'dfrapi_jquery_reveal_js' );
}

add_action( 'admin_enqueue_scripts', 'dfrapi_admin_enqueue_scripts' );

