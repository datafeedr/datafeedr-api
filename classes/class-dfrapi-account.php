<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Account' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Account {

		private $page = 'dfrapi-account';
		private $key;

		public function __construct() {
			$this->key = 'dfrapi_account';
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 70 );
			add_action( 'init', array( $this, 'load_settings' ) );
		}
		
		function load_settings() {	
			$this->options = (array) get_option( $this->key );
			if ( isset( $this->options[0] ) && empty( $this->options[0] ) ) {
				$this->options = $status = dfrapi_api_get_status();
				if ( !array_key_exists( 'dfrapi_api_error', $status ) ) {
					update_option( $this->key, $status );
				}
			}
		}
	
		function admin_menu() {
			add_submenu_page(
				'dfrapi',
				__( 'Account &#8212; Datafeedr API', 'datafeedr-api' ),
				__( 'Account', 'datafeedr-api' ),
				'manage_options', 
				$this->key,
				array( $this, 'output' ) 
			);
		}

		function output() {
			echo '<div class="wrap" id="' . $this->key . '">';
			echo '<h2>' . dfrapi_setting_pages( $this->page ) . ' &#8212; Datafeedr API</h2>';
			settings_fields( $this->page );
			do_settings_sections( $this->page);
			echo '</div>';
		}

		function register_settings() {
			
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			
			// Current Plan
			add_settings_section( 'current_plan', __( 'Current Plan', 'datafeedr-api' ), array( &$this, 'section_current_plan_desc' ), $this->page );
						
			// Only show the following if there is no Error.
			if ( !array_key_exists( 'dfrapi_api_error', $this->options ) ) {
			
				// Current Usage
				add_settings_section( 'current_usage', __( 'Current Usage', 'datafeedr-api' ), array( &$this, 'section_current_usage_desc' ), $this->page );
			}
			
			// Account Links
			add_settings_section( 'account', __( 'Account Links', 'datafeedr-api' ), array( &$this, 'section_account_desc' ), $this->page );
		}
		
		function section_current_plan_desc() {
			
			$plans = dfrapi_get_membership_plans();
			$plan_name = '';
			if ( $this->options['plan_id'] > 0 ) {
				$plan_name .= $plans[$this->options['plan_id']];
				if ( $this->options['plan_id'] != 10250000 ) {
					$plan_name .= ' (';
					$plan_name .= '<a href="' . dfrapi_user_pages( 'change' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiaccountpage" target="_blank" class="dfrapi_plan_link">' . __( 'Upgrade', 'datafeedr-api' ) . '</a>';
					$plan_name .= ')';
				}
			} else {
				$plan_name .= '<em>';
				$plan_name .= __( 'None', 'datafeedr-api' );
				$plan_name .= '</em> (';
				$plan_name .= '<a href="' . dfrapi_user_pages( 'signup' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiaccountpage" target="_blank" class="dfrapi_plan_link">' . __( 'Reactivate your subscription', 'datafeedr-api' ) . '</a>';
				$plan_name .= ')';
			}
			
			echo '
			<table class="widefat account_table" cellspacing="0">
				<tbody>
					<tr class="alternate">
						<td class="row-title">' . __( 'Plan name', 'datafeedr-api' ) . '</td>
						<td class="desc">' . $plan_name . '</td>
					</tr>
			';
			
			if ( !array_key_exists( 'dfrapi_api_error', $this->options ) ) {
				echo '	
					<tr>
						<td class="row-title">' . __( 'Requests per month (RPM)', 'datafeedr-api' ) . '</td>
						<td class="desc">' . number_format( $this->options['max_requests'] ) . '</td>
					</tr>
					<tr class="alternate">
						<td class="row-title">' . __( 'Products per request (PPR)', 'datafeedr-api' ) . '</td>
						<td class="desc">' . number_format( $this->options['max_length'] ) . '</td>
					</tr>
				';
			}
			
			echo '
				</tbody>
			</table>
			';
		}
		
		function section_current_usage_desc() {
			echo '<p>';
			_e( 'View your current API usage, number of API requests remaining and reset date', 'datafeedr-api' );
			echo ' <a href="' . dfrapi_user_pages( 'api' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiaccountpage#api-usage" target="_blank">' . __( 'here', 'datafeedr-api' ) . '</a>.';
			echo '<p>';
		}
	
		function section_account_desc() {
			echo '<p><a href="' . dfrapi_user_pages( 'summary' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiaccountpage" target="_blank">' . __( 'View your Datafeedr account', 'datafeedr-api' ) . '</a></p>';
			echo '<p><a href="' . dfrapi_user_pages( 'change' ) . '?utm_source=plugin&utm_medium=link&utm_campaign=dfrapiaccountpage" target="_blank">' . __( 'Upgrade your plan', 'datafeedr-api' ) . '</a></p>';
		}
		
		function validate( $input ) {
			return $input;
		}
		
	} // class Dfrapi_Account

} // class_exists check
