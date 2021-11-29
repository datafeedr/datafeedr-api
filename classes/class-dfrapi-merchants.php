<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Dfrapi_Merchants' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Merchants {

		private $page = 'dfrapi-merchants';
		private $key;
		private $all_networks;
		private $users_networks;
		public $options;

		public function __construct() {
			$this->key = 'dfrapi_merchants';
			$this->all_networks = dfrapi_api_get_all_networks();
			$this->set_users_networks();
			add_action( 'init', array( $this, 'load_settings' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			add_action( 'admin_notices', array( $this, 'api_errors' ) );
		}

		function api_errors() {
			if ( array_key_exists( 'dfrapi_api_error', $this->all_networks ) ) {
				$html = '';
				$html .= '<div class="notice notice-error">';
				$html .= '<p>';
				$html .= $this->all_networks;
				$html .= '</p>';
				$html .= '</div>';
				echo $html;
			}
		}

		function admin_notice() {
			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && isset( $_GET['page'] ) && $this->key == $_GET['page'] ) {
				echo '<div class="updated"><p>';
				_e( 'Merchants successfully updated!', DFRAPI_DOMAIN );
				echo '</p></div>';
			}
		}

		function load_settings() {
			$this->options = (array) get_option( $this->key );
			if ( isset( $this->options['ids'] ) && is_string( $this->options['ids'] ) && ( $this->options['ids'] != '' ) ) {
				$this->options['ids'] = explode( ",", $this->options['ids'] );
			} elseif ( ! isset( $this->options['ids'] ) ) {
				$this->options['ids'] = array();
			}
		}

		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			add_settings_section( 'merchants', __( 'Select Merchants', DFRAPI_DOMAIN ), array( &$this, 'section_merchants_desc' ), $this->page );
			add_settings_field( 'ids', __( 'Merchants', DFRAPI_DOMAIN ), array( &$this, 'field_merchant_ids' ), $this->page, 'merchants', array( 'label_for' => 'DFRAPI_HIDE_LABEL' ) );
		}

		function section_merchants_desc() {
			echo __( 'Select merchants from your affiliate networks then click <strong>[Save Changes]</strong>.', DFRAPI_DOMAIN );
		}

		function field_merchant_ids() {
			if ( is_array( $this->options['ids'] ) && !empty( $this->options['ids'] ) ) {
	            $ids = htmlspecialchars(implode(',', $this->options['ids']));
	        } else {
	        	$ids = '';
	        }

            echo "<input type='hidden' id='ids' name='".$this->key."[ids]' value='$ids' />";

			foreach ( $this->users_networks as $user_network ) {
				$network = $this->get_network_info( $user_network );

				if ( empty( $network ) ) {
					 _e( 'No networks have been selected.', DFRAPI_DOMAIN );
					break;
				}

				$num_networks_checked_in_network = $this->num_networks_checked_in_network( $network['_id'] );
				$active = ( preg_match( "/num_checked_none/", $num_networks_checked_in_network ) ) ? '' : 'active';
				echo '
				<div class="network network_logo_30x30_' . dfrapi_group_name_to_css( $network ) . ' ' . $active .'" id="network_' . $network['_id'] . '">
					<div class="meta">
						<span class="name">' . $network['name'] . '</span>
						<span class="status">
							' . $num_networks_checked_in_network . '
							' . $this->num_merchants_in_network( $network ) . '
							' . $this->num_products_in_network( $network ) . ' 
						</span>
					</div>
					' . $this->list_merchants( $network ) . '
				</div>
				';
			}
		}

        function format_merchant($merchant, $selected) {

            $no_products = ( $merchant['product_count'] < 1 ) ? 'no_products hidden' : '';

            $button = '<span class="merchant_hint_remove">' . __( 'Click to remove', DFRAPI_DOMAIN ) . "</span>"
                    . '<span class="merchant_hint_add">' . __( 'Click to add', DFRAPI_DOMAIN ) . "</span>";

            return '
                <div class="merchant ' . $no_products . '" id="merchant_id_' . $merchant['_id'] . '">
                    <div class="merchant_hint">
                        ' . $button . '
                    </div>
                    <div class="merchant_name">
                        ' . $merchant['name'] . '
                    </div>
                    <div class="merchant_info">
                    	' . $this->num_products_in_network( $merchant )  . '
                    </div>
                </div>
            ';
        }

		function list_merchants( $network ) {

			$network_id = $network['_id'];
			$merchants  = apply_filters( 'dfrapi_list_merchants', dfrapi_api_get_all_merchants( $network_id ), $network );

			// Show errors if any
			if ( is_wp_error( $merchants ) ) {
				$html = '<div style="color:red;margin-left: 36px; line-height: 1.3em; margin-bottom: 10px ">' . $merchants->get_error_message() . '</div>';

				return $html;
			}

			if ( array_key_exists( 'dfrapi_api_error', $merchants ) ) {
				return dfrapi_html_output_api_error( $merchants );
			}

			$html = '
				<div style="display:none;" class="merchants" id="merchants_for_nid_' . $network_id . '">
					<div class="merchant_actions">
						<span class="filter_action">
							' . __( 'Search', DFRAPI_DOMAIN ) . ': <input type="text"> 
							<a class="reset_search button" title="' . __( 'Clear search', DFRAPI_DOMAIN ) . '">&times;</a>
						</span>
						<span class="sep">|</span>
						<span class="hide_action">
							<a style="display:none" class="hide_empty_merchants button">' . __( 'Hide Empty Merchants', DFRAPI_DOMAIN ) . '</a>
							<a class="show_empty_merchants button">' . __( 'Show Empty Merchants', DFRAPI_DOMAIN ) . '</a>
						</span>
					</div>
				';

            $left = $right = '';

            foreach ( $merchants as $merchant ) {
                $selected = in_array( $merchant['_id'], $this->options['ids'] );
                $row = $this->format_merchant($merchant, $selected);

                if ( $selected ) {
                    $right .= $row;
                } else {
                    $left .= $row;
                }
            } // foreach ( $merchants as $merchant )

            $html .= '
                <div class="dfrapi_panes">
                    <div class="dfrapi_pane_left">
                        <div class="dfrapi_pane_title">
                            <span>' . __( 'Available Merchants', DFRAPI_DOMAIN ) . '</span>
						</span>

                        </div>
                        <div class="dfrapi_pane_content">
                            ' . $left . '
                        </div>
                    </div>
                    <div class="dfrapi_pane_right">
                        <div class="dfrapi_pane_title">
                            <span>' . __( 'Selected Merchants', DFRAPI_DOMAIN ) . '</span>
							<a class="remove_all button">' . __( 'Remove All', DFRAPI_DOMAIN ) . '</a>
                        </div>
                        <div class="dfrapi_pane_content">
                            ' . $right . '
                        </div>
                    </div>
                </div>
            ';

            $html .= "</div>";

			return $html;
		}

		function get_network_info( $network_id ) {
			foreach ( $this->all_networks as $network ) {
				if ( $network['_id'] == $network_id ) {
					return $network;
				}
			}
		}

		function set_users_networks() {
			$network_ids = array();
			$networks = (array) get_option( 'dfrapi_networks' );
			foreach ( $networks['ids'] as $network ) {
				if ( isset( $network['nid'] ) && !empty( $network['nid'] ) ) {
					$network_ids[] = $network['nid'];
				}
			}
			$this->users_networks = $network_ids;
		}

		function num_networks_checked_in_network( $network_id ) {
			$count = 0;
			$merchants = dfrapi_api_get_all_merchants( $network_id );
			foreach ( $merchants as $merchant) {
				if ( $merchant['source_id'] == $network_id ) {
					if ( in_array( $merchant['_id'], $this->options['ids'] ) ) {
						$count++;
					}
				}
			}

			$messages = $this->messages();

			if ( $count > 0 ) {
				return '<span class="num_checked_some">' . sprintf( translate_nooped_plural( $messages['num_checked'], $count, DFRAPI_DOMAIN ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			} else {
				return '<span class="num_checked_none">' . sprintf( translate_nooped_plural( $messages['num_checked'], $count, DFRAPI_DOMAIN ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}
		}

		function num_merchants_in_network( $network ) {
			$count = ( $network['merchant_count'] > 0 ) ? $network['merchant_count'] : 0;
			if ( $count > 0 ) {
				$messages = $this->messages();
				return '<span class="num_merchants">' . sprintf( translate_nooped_plural( $messages['num_merchants'], $count, DFRAPI_DOMAIN ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}
		}

		function num_products_in_network( $network ) {
			$count = ( $network['product_count'] > 0 ) ? $network['product_count'] : 0;
			$messages = $this->messages();
			return '<span class="num_products">' . sprintf( translate_nooped_plural( $messages['num_products'], $count, DFRAPI_DOMAIN ), number_format( $count ) ) . '</span>';
		}

		function validate( $input ) {

			if ( !isset( $input ) || !is_array( $input ) || empty( $input ) ) { return $input; }

			$new_input = array();

			foreach( $input as $key => $value ) {

				// Validate "ids"
				if ( $key == 'ids' ) {
					if ( is_array( $value ) ) {
						$new_input['ids'] = $value;
					} else {
						if ( trim( $value ) == '' ) {
							$new_input['ids'] = array();
						} else {
							$new_input['ids'] = explode( ",", $value );
						}
					}
				}

			} // foreach

			return $new_input;
		}

		function messages() {
			return array(
				'num_merchants' => _n_noop( '%s merchant', '%s merchants' ),
				'num_products'  => _n_noop( '%s product', '%s products' ),
				'num_checked' 	=> _n_noop( '%s merchant selected', '%s merchants selected' ),
			);
		}

	} // class Dfrapi_Merchants

} // class_exists check
