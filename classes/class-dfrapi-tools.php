<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Tools' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Tools {

		private $page = 'dfrapi-tools';
		private $key;

		public function __construct() {
			$this->key = 'dfrapi_tools';
			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 40 );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}

		function admin_menu() {
			add_submenu_page(
				'dfrapi',
				__( 'Tools &#8212; Datafeedr API', DFRAPI_DOMAIN ),
				__( 'Tools', DFRAPI_DOMAIN ),
				'manage_options',
				$this->key,
				array( $this, 'output' )
			);
		}

		function admin_notice() {
			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && isset( $_GET['page'] ) && $this->key == $_GET['page'] ) {
				dfrapi_admin_notice( __( 'Updated!', 'datafeedr-api' ), 'success' );
			}
		}

		function output() {
			echo '<div class="wrap" id="' . $this->key . '">';
			echo '<h2>' . dfrapi_setting_pages( $this->page ) . ' &#8212; Datafeedr API</h2>';
			?>

			<script>
			jQuery(function($) {

				$('#dfrapi_delete_cached_api_data').on('click',function(e) {
					$("#dfrapi_delete_cached_api_data_result").hide();
					$("#dfrapi_delete_cached_api_data").text('<?php _e("Deleting...", DFRAPI_DOMAIN); ?>').addClass('button-disabled');
					$.ajax({
						type: "POST",
						url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
						data: {
							action: "dfrapi_delete_cached_api_data",
							dfrapi_security: "<?php echo wp_create_nonce( 'dfrapi_ajax_nonce' ); ?>"
						}
					}).done(function(html) {
						$("#dfrapi_delete_cached_api_data").text('<?php _e("Delete Cached API Data", DFRAPI_DOMAIN); ?>').removeClass('button-disabled');
						$("#dfrapi_delete_cached_api_data_result").show().html(html);

					});
					e.preventDefault();
				}); // $('#dfrapi_delete_cached_api_data').on('click',function(e) {

                $('#dfrapi_test_api_connection').on('click',function(e) {
                    $("#dfrapi_test_api_connection_result").hide();
                    $("#dfrapi_test_api_connection").text('<?php _e("Testing...", DFRAPI_DOMAIN); ?>').addClass('button-disabled');
                    $.ajax({
                        type: "POST",
                        url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                        data: {
                            action: "dfrapi_test_api_connection",
                            dfrapi_security: "<?php echo wp_create_nonce( 'dfrapi_ajax_nonce' ); ?>"
                        }
                    }).done(function(html) {
                        $("#dfrapi_test_api_connection").text('<?php _e("Test Connection", DFRAPI_DOMAIN); ?>').removeClass('button-disabled');
                        $("#dfrapi_test_api_connection_result").show().html(html);

                    });
                    e.preventDefault();
                }); // $('#dfrapi_delete_cached_api_data').on('click',function(e) {

			}); // jQuery(function($) {
			</script>

			<?php

			settings_fields( $this->page );
			do_settings_sections( $this->page);
			echo '</div>';
		}

		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );

			add_settings_section( 'test_connection', __( 'Test API Connection', DFRAPI_DOMAIN ), array( &$this, 'section_test_connection_desc' ), $this->page );
			add_settings_section( 'delete_transient_data', __( 'Delete Cached API Data', DFRAPI_DOMAIN ), array( &$this, 'section_delete_transient_data_desc' ), $this->page );
		}

		function section_delete_transient_data_desc() { ?>
            <p><?php _e( 'Deleting cached data will not affect your store, however, it will require multiple API requests in order to re-build the data. Typically, you only delete cached data when Datafeedr Support instructs you to do so.', DFRAPI_DOMAIN ); ?></p>
            <p><a href="#" id="dfrapi_delete_cached_api_data" class="button"><?php _e("Delete Cached API Data", DFRAPI_DOMAIN); ?></a></p>
            <div id="dfrapi_delete_cached_api_data_result" style="padding: 10px; border: 1px solid silver; display: none; background: #FFF; color: green;"></div>
            <hr />
			<?php
		}

		function section_test_connection_desc() { ?>
            <p><?php _e( 'Test your web server\'s connection to the Datafeedr API servers. This will not count against your overall API request count.', DFRAPI_DOMAIN ); ?></p>
            <p><a href="#" id="dfrapi_test_api_connection" class="button"><?php _e("Test Connection", DFRAPI_DOMAIN); ?></a></p>
            <div id="dfrapi_test_api_connection_result" style="padding: 10px; border: 1px solid silver; display: none; background: #FFF;"></div>
            <hr />
			<?php
		}

		function validate( $input ) {
			return $input;
		}

	} // class Dfrapi_Tools

} // class_exists check
