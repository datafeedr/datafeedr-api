<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Import' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Import {

		private $page = 'dfrapi-import';
		private $key;

		public function __construct() {
			$this->key = 'dfrapi_import';
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 60 );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );		
		}
	
		function admin_menu() {
			add_submenu_page(
				'dfrapi',
				__( 'Import &#8212; Datafeedr API', DFRAPI_DOMAIN ), 
				__( 'Import', DFRAPI_DOMAIN ), 
				'manage_options', 
				$this->key,
				array( $this, 'output' ) 
			);
		}
		
		function admin_notice() {
			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && isset( $_GET['page'] ) && $this->key == $_GET['page'] ) {
				echo '<div class="updated"><p>';
				_e( 'Settings successfully imported!', DFRAPI_DOMAIN );
				echo '</p></div>';
			}
		}

		function output() {
			echo '<div class="wrap" id="' . $this->key . '">';
			echo '<h2>' . dfrapi_setting_pages( $this->page ) . ' &#8212; Datafeedr API</h2>';
			echo '<form method="post" action="options.php">';
			wp_nonce_field( 'update-options' );
			settings_fields( $this->page );
			do_settings_sections( $this->page);
			submit_button( __( 'Import Data', DFRAPI_DOMAIN ) );
			echo '</form>';		
			echo '</div>';
		}
	
		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			add_settings_section( 'import_data', __( 'Import Data', DFRAPI_DOMAIN ), array( &$this, 'section_import_data_desc' ), $this->page );
			add_settings_field( 'import_data_textarea', __( 'Data to Import', DFRAPI_DOMAIN ), array( &$this, 'field_import_data_textarea' ), $this->page, 'import_data' );
		}
	
		function section_import_data_desc() { 
			echo '<p>' . __( 'Import data from another website when you want to use the same selection of networks and/or merchants as you have already configured on another website.', DFRAPI_DOMAIN ) . '</p>';
			echo '<p>' . __( 'You can import network and merchant data at the same time.', DFRAPI_DOMAIN ) . '</p>';
			echo '<p class="dfrapi_warning">';
			echo '<strong>' . __( 'WARNING: ', DFRAPI_DOMAIN ) . '</strong> ';
			echo __( 'Importing will overwrite all of your current ', DFRAPI_DOMAIN );
			echo ' <a href="' . admin_url( 'admin.php?page=dfrapi_networks' ) . '">'. __( 'network settings', DFRAPI_DOMAIN ) . '</a>';
			echo __( ' and/or ', DFRAPI_DOMAIN );
			echo ' <a href="' . admin_url( 'admin.php?page=dfrapi_merchants' ) . '">'. __( 'merchant settings', DFRAPI_DOMAIN ) . '</a>';
			echo '.</p>';
		}

		function field_import_data_textarea() {
			echo '<textarea rows="10" cols="100%" name="' . $this->key . '[data]"></textarea></p>';
			echo '<p class="description">' . __( 'Paste the exported data into the box, then click [Import Data].' ) . '</p>';
		}
		
		function validate( $input ) {
			if ( isset( $input ) && is_array( $input ) && !empty( $input ) ) {
				foreach( $input as $key => $value ) {
					if ( $key == 'data' && ( strlen( $value ) > 16 ) ) {
						$this->import_data( $value );
					}
				}
			}
		}
		
		function import_data( $value ) {
			
			// Import networks
			preg_match ("/(\[NETWORKS\]).*(\[\/NETWORKS\])/i", $value, $networks);
			if ( isset( $networks[0] ) ) {
				$networks = str_replace( array("[NETWORKS]", "[/NETWORKS]"), "", $networks[0]);
				$networks = trim( $networks );
				if ( strlen( $networks ) > 1 ) {
					update_option( 'dfrapi_networks', unserialize( $networks ) );
				}
			}
			
			// Import merchants
			preg_match ("/(\[MERCHANTS\]).*(\[\/MERCHANTS\])/i", $value, $merchants);
			if ( isset( $merchants[0] ) ) {
				$merchants = str_replace( array("[MERCHANTS]", "[/MERCHANTS]"), "", $merchants[0] );
				$merchants = trim( $merchants );
				if ( strlen( $merchants ) > 1 ) {
					update_option( 'dfrapi_merchants', unserialize( $merchants ) );
				}
			}
			
		}
		
		
	} // class Dfrapi_Import

} // class_exists check












