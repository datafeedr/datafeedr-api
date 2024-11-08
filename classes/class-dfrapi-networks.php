<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Networks' ) ) {

	/**
	 * Configuration page.
	 */
	class Dfrapi_Networks {

		private $page = 'dfrapi-networks';
		private $key;
		private $all_networks;
		public $options;

		public function __construct() {

			$this->key          = 'dfrapi_networks';
			$this->all_networks = dfrapi_api_get_all_networks();
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
				dfrapi_admin_notice( __( 'Networks successfully updated!', 'datafeedr-api' ), 'success' );
			}
		}

		function load_settings() {
			$this->options = (array) get_option( $this->key );
			$this->options = array_merge( array(
				'ids' => array(),
			), $this->options );
		}

		function register_settings() {
			register_setting( $this->page, $this->key, array( $this, 'validate' ) );
			add_settings_section( 'networks', __( 'Select Networks', 'datafeedr-api' ), array(
				&$this,
				'section_networks_desc'
			), $this->page );
			add_settings_field( 'ids', __( 'Networks', 'datafeedr-api' ), array(
				&$this,
				'field_network_ids'
			), $this->page, 'networks', array( 'label_for' => 'DFRAPI_HIDE_LABEL' ) );
		}

		function section_networks_desc() {
			echo __( 'Select the affiliate networks you belong to, enter your affiliate ID for each then click <strong>[Save Changes]</strong>.', 'datafeedr-api' );
		}

		function field_network_ids() {
			$groups = $this->get_network_group_names();
			foreach ( $groups as $group ) {
				$num_networks_checked_in_group = $this->num_networks_checked_in_group( $group );
				$active                        = ( $num_networks_checked_in_group != '' ) ? 'active' : '';
				$group_name                    = ( 'AffiliateWindow' == $group ) ? 'Awin' : $group;
				echo '
				<div class="group network_logo_30x30_' . dfrapi_group_name_to_css( $group ) . ' ' . $active . '" id="group_' . dfrapi_group_name_to_css( $group ) . '">
					<div class="meta">
						<span class="name">' . $group_name . '</span>
						<span class="status">
							' . $this->num_missing_affiliate_ids_in_group( $group ) . '
							' . $num_networks_checked_in_group . '
							' . $this->num_networks_in_group( $group ) . '
							' . $this->num_merchants_in_group( $group ) . '
							' . $this->num_products_in_group( $group ) . ' 
						</span>
					</div>
					' . $this->get_groups_networks( $group ) . '
				</div>';
			}
		}

		function get_groups_networks( $group_name ) {
			$html = '
			<div style="display:none;" class="networks">
				<table class="wp-list-table widefat fixed networks_table" cellspacing="0">
					<thead>
						<tr>
							<th class="checkbox_head"> &nbsp; </th>
							<th class="networks_head">' . __( 'Network', 'datafeedr-api' ) . '</th>
							<th class="type_head">' . __( 'Type', 'datafeedr-api' ) . '</th>
							<th class="aid_head">' . __( 'Affiliate ID', 'datafeedr-api' ) . ' <a href="' . $this->map_link( $group_name ) . '" target="_blank" title="' . __( 'Learn how to find your affiliate ID from ', 'datafeedr-api' ) . $group_name . __( ' (opens in new window).', 'datafeedr-api' ) . '"><img src="' . DFRAPI_URL . 'images/icons/help.png" alt="' . __( 'more info', 'datafeedr-api' ) . '" style="vertical-align: middle" /></a> <small style="font-weight:normal;color:#a00;">(' . __( 'required', 'datafeedr-api' ) . ')</small></th>
							<th class="tid_head">' . __( 'Tracking ID', 'datafeedr-api' ) . ' <a href="https://datafeedrapi.helpscoutdocs.com/article/212-tracking-ids" target="_blank" title="' . __( 'Learn more about this field (opens in new window).', 'datafeedr-api' ) . '"><img src="' . DFRAPI_URL . 'images/icons/help.png" alt="' . __( 'more info', 'datafeedr-api' ) . '" style="vertical-align: middle" /></a> <small style="font-weight:normal;color:#999;">(' . __( 'optional', 'datafeedr-api' ) . ')</small></th>
						</tr>
					</thead>
					<tbody>
			';

			$i = 0;

			foreach ( $this->all_networks as $network ) {

				$i ++;
				$checked     = ( array_key_exists( $network['_id'], (array) $this->options['ids'] ) ) ? ' checked="checked"' : '';
				$type        = ( $network['type'] === 'products' ) ? __( 'products', 'datafeedr-api' ) : __( 'coupons', 'datafeedr-api' );
				$type_class  = ( $network['type'] === 'products' ) ? ' dfrapi_label-info"' : ' dfrapi_label-success';
				$no_products = ( $network['product_count'] < 1 ) ? 'no_products' : '';
				$alternate   = ( $i % 2 ) ? '' : ' alternate';

				if ( $network['group'] == $group_name && (int) $network['group_id'] === dfrapi_get_partnerize_group_id() && ! dfrapi_get_ph_keys() ) {
					$html .= sprintf(
						__( '<tr><td colspan="4"><strong>%1$s</strong><br />Please go to <a href="%2$s">Datafeedr API > Configuration</a> and enter your Partnerize API keys before selecting this network.</td></tr>', 'datafeedr-api' ),
						esc_html( $network['name'] ),
						esc_url( dfrapi_configuration_page_url() )
					);

					continue;
				}

				if ( $network['group'] == $group_name && (int) $network['group_id'] === dfrapi_get_effiliation_group_id() && ! dfrapi_get_effiliation_keys() ) {
					$html .= sprintf(
						__( '<tr><td colspan="4"><strong>%1$s</strong><br />Please go to <a href="%2$s">Datafeedr API > Configuration</a> and enter your Effiliation Key before selecting this network.</td></tr>', 'datafeedr-api' ),
						esc_html( $network['name'] ),
						esc_url( dfrapi_configuration_page_url() )
					);

					continue;
				}

				if ( $network['group'] == $group_name && (int) $network['group_id'] === dfrapi_get_belboon_group_id() && is_wp_error( dfrapi_get_belboon_adspace_id() ) ) {
					$html .= sprintf(
						__( '<tr><td colspan="4"><strong>%1$s</strong><br />Please go to <a href="%2$s">Datafeedr API > Configuration</a> and enter your Belboon Adspace ID before selecting this network.</td></tr>', 'datafeedr-api' ),
						esc_html( $network['name'] ),
						esc_url( dfrapi_configuration_page_url() )
					);

					continue;
				}

				if ( $network['group'] == $group_name ) {

					$aid = ( isset( $this->options['ids'][ $network['_id'] ]['aid'] ) ) ? $this->options['ids'][ $network['_id'] ]['aid'] : '';
					$tid = ( isset( $this->options['ids'][ $network['_id'] ]['tid'] ) ) ? $this->options['ids'][ $network['_id'] ]['tid'] : '';

					$html .= '
					<tr 
						class="network ' . $no_products . $alternate . '" 
						id="network_id_' . $network['_id'] . '" 
						nid="' . $network['_id'] . '" 
						key="' . $this->key . '" 
						aid="' . $aid . '" 
						tid="' . $tid . '"
					>
						<td class="network_checkbox">
							<input type="checkbox" id="nid_' . $network['_id'] . '" class="check_network" name="' . $this->key . '[ids][' . $network['_id'] . '][nid]" value="' . $network['_id'] . '"' . $checked . ' />
						</td>
						<td class="network_name">
							<label for="nid_' . $network['_id'] . '">
								' . $network['name'] . '
								<div class="network_info">
									<span class="num_merchants">' . number_format( $network['merchant_count'] ) . ' ' . __( 'merchants', 'datafeedr-api' ) . '  <span class="sep">/</span>
									<span class="num_products">' . number_format( $network['product_count'] ) . ' ' . $type . '</span>
								</div>
							</label>
						</td>
						<td class="network_type">
							<span class="dfrapi_label' . $type_class . '">' . ucfirst( $type ) . '</span>
						</td>
					';

					if ( $network['group_id'] == dfrapi_get_partnerize_group_id() ) {
						$url  = admin_url( 'admin.php?page=dfrapi' );
						$html .= '<td class="aid_input"><a href="' . $url . '" target="_blank">Edit Partnerize Keys</a></td>';
					} elseif ( $network['group_id'] == dfrapi_get_effiliation_group_id() ) {
						$url  = admin_url( 'admin.php?page=dfrapi' );
						$html .= '<td class="aid_input"><a href="' . $url . '" target="_blank">Edit Effiliation Key</a></td>';
					} elseif ( $network['group_id'] == dfrapi_get_belboon_group_id() && is_wp_error( dfrapi_get_belboon_adspace_id() ) ) {
						$url  = admin_url( 'admin.php?page=dfrapi' );
						$html .= '<td class="aid_input"><a href="' . $url . '" target="_blank">Your Belboon Adspace ID is required before you can enter your affiliate ID. Enter Adspace ID.</a></td>';
					} elseif ( $network['group_id'] == 10033 && is_wp_error( dfrapi_get_affiliate_gateway_sid() ) ) {
						$url  = admin_url( 'admin.php?page=dfrapi' );
						$html .= '<td class="aid_input"><a href="' . $url . '" target="_blank">Your Affiliate Gateway SID is required before you can enter your affiliate ID. Enter SID.</a></td>';
					} elseif ( $network['group_id'] == 10045 && is_wp_error( dfrapi_get_adservice_mid() ) ) {
						$url  = admin_url( 'admin.php?page=dfrapi' );
						$html .= '<td class="aid_input"><a href="' . $url . '" target="_blank">Your Adservice Media ID is required before you can enter your affiliate ID. Enter Media ID.</a></td>';
					} else {
						$html .= '<td class="aid_input"><input type="text" name="dfrapi_networks[ids][' . $network['_id'] . '][aid]" value="' . $aid . '" class="aid_input_field" /></td>';
					}

					if ( $group_name == 'NoTrackingIDOption' ) {
						$html .= '<td class="tid_input"><small>n/a</small></td>';
					} else {
						$html .= '<td class="tid_input"><input type="text" name="dfrapi_networks[ids][' . $network['_id'] . '][tid]" value="' . $tid . '" class="tid_input_field" /></td>';
					}

					$html .= '</tr>';
				}
			}

			$html .= '
					</tbody>
				</table>
			</div>
			';

			return $html;
		}

		function get_network_group_names() {
			$networks = $this->all_networks;
			$groups   = array();
			foreach ( $networks as $network ) {
				$groups[] = $network['group'];
			}

			return array_unique( $groups );
		}

		function num_missing_affiliate_ids_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {

				if ( $network['group_id'] == dfrapi_get_partnerize_group_id() ) {
					continue;
				}

				if ( $network['group_id'] == dfrapi_get_effiliation_group_id() ) {
					continue;
				}

				if ( $network['group'] == $group_name ) {
					if ( array_key_exists( $network['_id'], (array) $this->options['ids'] ) ) {
						if ( dfrapi_get_affiliate_id_by_network_id( (int) $network['_id'] ) === false ) {
							$count ++;
						}
					}
				}
			}

			if ( $count > 0 ) {
				$messages = $this->messages();

				return '<span class="num_missing">' . sprintf( translate_nooped_plural( $messages['num_missing'],
						$count, 'datafeedr-api' ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}

			return '';
		}

		function num_networks_checked_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {
				if ( $network['group'] == $group_name ) {
					if ( array_key_exists( $network['_id'], (array) $this->options['ids'] ) ) {
						$count ++;
					}
				}
			}

			if ( $count > 0 ) {
				$messages = $this->messages();

				return '<span class="num_checked">' . sprintf( translate_nooped_plural( $messages['num_checked'], $count, 'datafeedr-api' ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}
		}

		function num_networks_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {
				if ( $network['group'] == $group_name ) {
					$count ++;
				}
			}

			if ( $count > 0 ) {
				$messages = $this->messages();

				return '<span class="num_networks">' . sprintf( translate_nooped_plural( $messages['num_networks'], $count, 'datafeedr-api' ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}
		}

		function num_merchants_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {
				if ( $network['group'] == $group_name ) {
					$count += $network['merchant_count'];
				}
			}

			if ( $count > 0 ) {
				$messages = $this->messages();

				return '<span class="num_merchants">' . sprintf( translate_nooped_plural( $messages['num_merchants'], $count, 'datafeedr-api' ), number_format( $count ) ) . '</span> <span class="sep">/</span> ';
			}
		}

		function num_products_in_group( $group_name ) {
			$count = 0;
			foreach ( $this->all_networks as $network ) {
				if ( $network['group'] == $group_name ) {
					$count += $network['product_count'];
				}
			}

			if ( $count > 0 ) {
				$messages = $this->messages();

				return '<span class="num_products">' . sprintf( translate_nooped_plural( $messages['num_products'], $count, 'datafeedr-api' ), number_format( $count ) ) . '</span>';
			}

		}

		function messages() {
			return array(
				'num_missing'   => _n_noop( '%s missing affiliate ID', '%s missing affiliate IDs' ),
				'num_checked'   => _n_noop( '%s network selected', '%s networks selected' ),
				'num_networks'  => _n_noop( '%s network', '%s networks' ),
				'num_merchants' => _n_noop( '%s merchant', '%s merchants' ),
				'num_products'  => _n_noop( '%s product', '%s products' ),
			);
		}

		function map_link( $name ): string {
			$links = [
				'2Performant'           => 'https://datafeedrapi.helpscoutdocs.com/article/240-how-to-find-your-2performant-affiliate-id',
				'Admitad'               => 'https://datafeedrapi.helpscoutdocs.com/article/241-how-to-find-your-admitad-affiliate-id',
				'ADCELL'                => 'https://datafeedrapi.helpscoutdocs.com/article/235-how-to-find-your-adcell-affiliate-id',
				'Addrevenue'            => 'https://datafeedrapi.helpscoutdocs.com/article/259-how-to-find-your-addrevenue-affiliate-id',
				'Adrecord'              => 'https://datafeedrapi.helpscoutdocs.com/article/115-how-to-find-your-adrecord-affiliate-id',
				'Adservice'             => 'https://datafeedrapi.helpscoutdocs.com/article/251-how-to-find-your-adservice-affiliate-id-and-media-id',
				'Adtraction'            => 'https://datafeedrapi.helpscoutdocs.com/article/116-how-to-find-your-adtraction-affiliate-id',
				'Affiliate4You'         => 'http://www.datafeedr.com/docs/item/265',
				'Awin'                  => 'https://datafeedrapi.helpscoutdocs.com/article/120-how-to-find-your-affiliate-window-affiliate-id',
				'Affiliator'            => 'http://www.datafeedr.com/docs/item/270',
				'Affilinet'             => 'https://datafeedrapi.helpscoutdocs.com/article/121-how-to-find-your-affilinet-affiliate-id',
				'Amazon Local'          => 'http://www.datafeedr.com/docs/item/275',
				'APD'                   => 'https://datafeedrapi.helpscoutdocs.com/article/133-how-to-find-your-apd-affiliate-id',
				'Avangate'              => 'https://datafeedrapi.helpscoutdocs.com/article/122-how-to-find-your-avangate-affiliate-id',
				'AvantLink'             => 'https://datafeedrapi.helpscoutdocs.com/article/123-how-to-find-your-avantlink-affiliate-id',
				'Belboon'               => 'https://datafeedrapi.helpscoutdocs.com/article/125-how-to-find-your-belboon-affiliate-id',
				'BettyMills'            => 'https://datafeedrapi.helpscoutdocs.com/article/129-how-to-find-your-betty-mills-affiliate-id',
				'bol.com'               => 'https://datafeedrapi.helpscoutdocs.com/article/186-how-to-find-your-bol-com-affiliate-id',
				'ClickBank'             => 'https://datafeedrapi.helpscoutdocs.com/article/130-how-to-find-your-clickbank-affiliate-id',
				'ClixGalore'            => 'https://datafeedrapi.helpscoutdocs.com/article/131-how-to-find-your-clixgalore-affiliate-id',
				'Commission Factory'    => 'https://datafeedrapi.helpscoutdocs.com/article/132-how-to-find-your-commission-factory-affiliate-id',
				'Commission Junction'   => 'https://datafeedrapi.helpscoutdocs.com/article/128-how-to-find-your-cj-affiliate-id',
				'Commission Monster'    => 'http://www.datafeedr.com/docs/item/109',
				'Connexity'             => 'https://datafeedrapi.helpscoutdocs.com/article/178-how-to-find-your-connexity-affiliate-id',
				'Daisycon'              => 'https://datafeedrapi.helpscoutdocs.com/article/135-how-to-find-your-daisycon-affiliate-id',
				'DGM'                   => 'http://www.datafeedr.com/docs/item/263',
				'Digital Advisor'       => 'https://datafeedrapi.helpscoutdocs.com/article/250-how-to-find-your-digital-advisor-affiliate-id',
				'Double.net'            => 'https://datafeedrapi.helpscoutdocs.com/article/136-how-to-find-your-double-net-affiliate-id',
				'Effiliation'           => 'https://datafeedrapi.helpscoutdocs.com/article/211-how-to-find-your-effiliation-api-key',
				'FamilyBlend'           => 'https://datafeedrapi.helpscoutdocs.com/article/117-how-to-find-your-familyblend-affiliate-id',
				'FlexOffers'            => 'https://datafeedrapi.helpscoutdocs.com/article/209-how-to-find-your-flexoffers-affiliate-id',
				'FlipKart'              => 'https://datafeedrapi.helpscoutdocs.com/article/137-how-to-find-your-flipkart-affiliate-id',
				'GoAffPro'              => 'https://datafeedrapi.helpscoutdocs.com/article/249-how-to-find-your-goaffpro-affiliate-id',
				'Impact'                => 'https://datafeedrapi.helpscoutdocs.com/article/134-how-to-find-your-impact-radius-affiliate-id',
				'LinkConnector'         => 'https://datafeedrapi.helpscoutdocs.com/article/138-how-to-find-your-linkconnector-affiliate-id',
				'Rakuten'               => 'https://datafeedrapi.helpscoutdocs.com/article/139-how-to-find-your-rakuten-affiliate-id',
				'M4N'                   => 'http://www.datafeedr.com/docs/item/198',
				'MyCommerce'            => 'http://www.datafeedr.com/docs/item/111',
				'OneNetworkDirect'      => 'https://datafeedrapi.helpscoutdocs.com/article/140-how-to-find-your-onenetworkdirect-affiliate-id',
				'Optimise'              => 'https://datafeedrapi.helpscoutdocs.com/article/141-how-to-find-your-optimise-affiliate-id',
				'Paid on Results'       => 'https://datafeedrapi.helpscoutdocs.com/article/142-how-to-find-your-paid-on-results-affiliate-id',
				'Partner-ads'           => 'https://datafeedrapi.helpscoutdocs.com/article/143-how-to-find-your-partner-ads-affiliate-id',
				'Partnerize'            => 'https://datafeedrapi.helpscoutdocs.com/article/195-how-to-find-your-partnerize-publisher-id-and-api-keys',
				'PepperJam'             => 'https://datafeedrapi.helpscoutdocs.com/article/127-how-to-find-your-ebay-enterprise-affiliate-network-affiliate-id',
				'PerformanceHorizon'    => 'https://datafeedrapi.helpscoutdocs.com/article/195-how-to-find-your-performance-horizon-publisher-id-and-api-keys',
				'Profitshare'           => 'https://datafeedrapi.helpscoutdocs.com/article/242-how-to-find-your-profitshare-affiliate-id',
				'RegNow'                => 'http://www.datafeedr.com/docs/item/111',
				'RevResponse'           => 'https://datafeedrapi.helpscoutdocs.com/article/144-how-to-find-your-revresponse-affiliate-id',
				'ShareASale'            => 'https://datafeedrapi.helpscoutdocs.com/article/126-how-to-find-your-shareasale-affiliate-id',
				'Shopello'              => 'https://datafeedrapi.helpscoutdocs.com/article/171-how-to-find-your-shopello-affiliate-id',
				'Snapdeal'              => 'https://datafeedrapi.helpscoutdocs.com/article/172-how-to-find-your-snapdeal-affiliate-id',
				'SuperClix'             => 'https://datafeedrapi.helpscoutdocs.com/article/145-how-to-find-your-superclix-affiliate-id',
				'The Affiliate Gateway' => 'https://datafeedrapi.helpscoutdocs.com/article/225-how-to-find-your-affiliate-gateway-affiliate-id',
				'TimeOne'               => 'https://datafeedrapi.helpscoutdocs.com/article/262-how-to-find-your-timeone-affiliate-id',
				'TradeDoubler'          => 'https://datafeedrapi.helpscoutdocs.com/article/146-how-to-find-your-tradedoubler-affiliate-id',
				'TradeTracker'          => 'https://datafeedrapi.helpscoutdocs.com/article/148-how-to-find-your-tradetracker-affiliate-id',
				'Webgains'              => 'https://datafeedrapi.helpscoutdocs.com/article/147-how-to-find-your-webgains-affiliate-id',
				'Zanox'                 => 'https://datafeedrapi.helpscoutdocs.com/article/149-how-to-find-your-zanox-api-keys',
			];

			return $links[ $name ] ?? ( DFRAPI_DOCS_SEARCH_URL . sanitize_title( $name ) );
		}

		function validate( $input ) {
			$new_input['ids'] = array();
			if ( isset( $input['ids'] ) ) {
				foreach ( $input['ids'] as $k => $v ) {
					if ( isset( $v['nid'] ) ) {
						$new_input['ids'][ $k ] = $v;
					}
				}
			}

			return $new_input;
		}

	} // class Dfrapi_Networks

} // class_exists check
