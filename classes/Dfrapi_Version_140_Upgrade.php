<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Version_140_Upgrade' ) ) {

	class Dfrapi_Version_140_Upgrade {

		public function __construct() {
			add_action( 'admin_notices', [ $this, 'admin_notice' ] );
		}

		/**
		 * Sets the initial tracking data for this upgrade.
		 *
		 * Options table: "dfrapi_plugin_upgrade_status"
		 *
		 * @return void
		 */
		public static function set_initial_status(): void {

			$status = [
				'version_140' => [
					'update_started_at'         => self::now(),
					'update_completed_at'       => null,
					'product_set_update_status' => self::get_dfrps_update_status(), // So we can re-enable if needed.
					'update_stages'             => self::get_update_stages(),
				]
			];

			add_option( 'dfrapi_plugin_upgrade_status', $status, '', false );

			self::disable_product_set_updates();

			if ( ! wp_next_scheduled( 'dfrapi_handle_version_140_upgrade_action' ) ) {
				wp_schedule_event( time(), 'every_minute', 'dfrapi_handle_version_140_upgrade_action' );
			}
		}

		public static function now(): string {
			return current_time( 'mysql' );
		}

		/**
		 * @return array
		 */
		public static function get_upgrade_status(): array {
			return get_option( 'dfrapi_plugin_upgrade_status', [] );
		}

		public static function version_140_update_is_complete(): bool {
			return self::get_upgrade_status()['version_140']['update_completed_at'] !== null;
		}

		/**
		 * Update stages.
		 *
		 * @return array[]
		 */
		public static function get_update_stages(): array {
			return [
				'postmeta__dfrps_product_id'               => self::get_stage_status_array(),
				'postmeta__dfrps_cpt_temp_query'           => self::get_stage_status_array(),
				'postmeta__dfrps_cpt_manually_added_ids'   => self::get_stage_status_array(),
				'postmeta__dfrps_cpt_manually_blocked_ids' => self::get_stage_status_array(),
//				'postmeta__dfrps_cpt_previous_update_info' => self::get_stage_status_array(),
				'postmeta__dfrps_cpt_query'                => self::get_stage_status_array(),
				'dfrcs_compsets'                           => self::get_stage_status_array(),
				'dfrps_temp_product_data'                  => self::get_stage_status_array(),
			];
		}

		/**
		 * Initial status for all update stages
		 *
		 * @return array
		 */
		public static function get_stage_status_array(): array {
			return [
				'started_at'        => null,
				'completed_at'      => null,
				'v5_ids_updated'    => 0,
				'last_processed_id' => 0,
			];
		}

		/**
		 * Returns true if the Product Set plugin is installed and active. Otherwise, returns false.
		 *
		 * @return bool
		 */
		public static function dfrps_is_active(): bool {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return is_plugin_active( 'datafeedr-product-sets/datafeedr-product-sets.php' );
		}

		/**
		 * Returns the Product Sets plugin's configuration array.
		 *
		 * @return array
		 */
		public static function get_dfrps_configuration(): array {
			return get_option( 'dfrps_configuration', [ 'updates_enabled' => 'disabled' ] );
		}

		/**
		 * Returns the current "update" status of the Product Sets plugin.
		 *
		 * @return string Either "enabled" or "disabled".
		 */
		public static function get_dfrps_update_status(): string {
			$config = self::get_dfrps_configuration();

			return $config['updates_enabled'];
		}

		/**
		 * Disables the Product Sets plugin's updates.
		 *
		 * @return void
		 */
		public static function disable_product_set_updates(): void {
			if ( self::dfrps_is_active() ) {
				$config                    = self::get_dfrps_configuration();
				$config['updates_enabled'] = 'disabled';
				update_option( 'dfrps_configuration', $config );
			}
		}

		/**
		 * Enables the Product Sets plugin's updates.
		 *
		 * @return void
		 */
		public static function enable_product_set_updates(): void {
			if ( self::dfrps_is_active() ) {
				$config                    = self::get_dfrps_configuration();
				$config['updates_enabled'] = 'enabled';
				update_option( 'dfrps_configuration', $config );
			}
		}

		public function process_stage(): void {

			$status = self::get_upgrade_status();

			if ( empty( $status ) ) {
				return;
			}

			if ( self::version_140_update_is_complete() ) {
				return;
			}

			// Handle postmeta__dfrps_product_id stage
			if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_product_id']['completed_at'] ) ) {
				$this->handle_postmeta_dfrps_product_id_stage( $status );

				return;
			}

			// Handle postmeta__dfrps_cpt_query stage
			if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_query']['completed_at'] ) ) {
				$this->handle_postmeta_dfrps_cpt_query( $status );

				return;
			}

			// Handle postmeta__dfrps_cpt_temp_query stage
			if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_temp_query']['completed_at'] ) ) {
				$this->handle_postmeta_dfrps_cpt_temp_query( $status );

				return;
			}

			// Handle postmeta__dfrps_cpt_manually_added_ids stage
			if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_added_ids']['completed_at'] ) ) {
				$this->handle_postmeta_dfrps_cpt_manually_added_ids( $status );

				return;
			}

			// Handle postmeta__dfrps_cpt_manually_blocked_ids stage
			if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_blocked_ids']['completed_at'] ) ) {
				$this->handle_postmeta_dfrps_cpt_manually_blocked_ids( $status );

				return;
			}
		}

		private function handle_postmeta_dfrps_product_id_stage( array $status ): void {

			$v5_ids_updated = (int) $status['version_140']['update_stages']['postmeta__dfrps_product_id']['v5_ids_updated'];

			if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_product_id']['started_at'] ) ) {
				$status['version_140']['update_stages']['postmeta__dfrps_product_id']['started_at'] = self::now();
			}

			global $wpdb;

			$results = $wpdb->get_results(
				$wpdb->prepare( "
        			SELECT meta_id, meta_value
        			FROM {$wpdb->postmeta}
        			WHERE meta_key = %s
        			AND CHAR_LENGTH(meta_value) < %d
        			AND meta_id > %d
        			ORDER BY meta_id ASC
        			LIMIT 100",
					'_dfrps_product_id',
					19,
					$status['version_140']['update_stages']['postmeta__dfrps_product_id']['last_processed_id']
				)
			);

			if ( empty( $results ) ) {

				$status['version_140']['update_stages']['postmeta__dfrps_product_id']['completed_at'] = self::now();

			} else {

				$v5_ids = [];

				foreach ( $results as $row ) {
					$v5_ids[] = $row->meta_value;
				}

				// Get converted IDs.
				$ids = dfrapi_get_v7_ids_from_v5_ids( $v5_ids );

				// Loop through IDs updating each one and then updating the `last_processed_id` param.
				foreach ( $results as $row ) {

					$result = $wpdb->update(
						$wpdb->postmeta,
						[ 'meta_value' => $ids[ $row->meta_value ] ],
						[ 'meta_id' => $row->meta_id ],
						[ '%s' ],   // format for meta_value
						[ '%d' ]    // format for meta_id
					);

					if ( $result ) {
						$v5_ids_updated ++;
						$status['version_140']['update_stages']['postmeta__dfrps_product_id']['last_processed_id'] = $row->meta_id;
						$status['version_140']['update_stages']['postmeta__dfrps_product_id']['v5_ids_updated']    = $v5_ids_updated;
					}
				}
			}

			$this->update_status( $status );
		}

		private function handle_postmeta_dfrps_cpt_query( array $status ): void {

			global $wpdb;

			$field_key = 'postmeta__dfrps_cpt_query';

			// Get current number of V5 IDs which have been updated.
			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];

			// If this is the first iteration, set the "started_at" date.
			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			// Query all postmeta rows where meta_key = "_dfrps_cpt_temp_query" and limit it to 10 starting at last processed meta_id.
			$results = $wpdb->get_results(
				$wpdb->prepare( "
        			SELECT meta_id, meta_value
        			FROM {$wpdb->postmeta}
        			WHERE meta_key = %s
        			AND meta_id > %d
        			ORDER BY meta_id ASC
        			LIMIT 10",
					'_dfrps_cpt_query',
					$status['version_140']['update_stages'][ $field_key ]['last_processed_id']
				)
			);

			if ( empty( $results ) ) {

				// No results returned from the query so mark this stage as compete.
				$status['version_140']['update_stages'][ $field_key ]['completed_at'] = self::now();

			} else {

				// For each cpt_temp_query stored
				foreach ( $results as $row ) {

					// Get the stored query and unserialize it
					$query = maybe_unserialize( $row->meta_value );

					if ( is_array( $query ) ) {

						// Loop through each query param
						foreach ( $query as $key => $param ) {

							// IDs found, let's update!
							if ( isset( $param['field'] ) && $param['field'] === 'id' ) {

								// Get all IDs from query.
								$all_ids          = dfrapi_explode_and_uniquify( $param['value'] );
								$extracted_v5_ids = dfrapi_extract_v5_ids( $all_ids );

								// If there are no V5 IDs, continue to next query param.
								if ( empty( $extracted_v5_ids ) ) {
									continue;
								}

								$extracted_v7_ids = dfrapi_extract_v7_ids( $all_ids );
								$converted_v5_ids = dfrapi_get_v7_ids_from_v5_ids( $extracted_v5_ids );

								// New set of product IDs.
								$new_ids = array_merge( $extracted_v7_ids, array_values( $converted_v5_ids ) );

								// Update $query value field.
								$query[ $key ]['value'] = implode( ', ', $new_ids );

								$result = $wpdb->update(
									$wpdb->postmeta,
									[ 'meta_value' => serialize( $query ) ],
									[ 'meta_id' => $row->meta_id ],
									[ '%s' ],   // format for meta_value
									[ '%d' ]    // format for meta_id
								);

								if ( $result ) {
									$v5_ids_updated += count( $converted_v5_ids );
								}
							}
						}
					}

					$status['version_140']['update_stages'][ $field_key ]['last_processed_id'] = $row->meta_id;
					$status['version_140']['update_stages'][ $field_key ]['v5_ids_updated']    = $v5_ids_updated;
				}
			}

			$this->update_status( $status );
		}

		private function handle_postmeta_dfrps_cpt_temp_query( array $status ): void {

			global $wpdb;

			$field_key = 'postmeta__dfrps_cpt_temp_query';

			// Get current number of V5 IDs which have been updated.
			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];

			// If this is the first iteration, set the "started_at" date.
			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			// Query all postmeta rows where meta_key = "_dfrps_cpt_temp_query" and limit it to 10 starting at last processed meta_id.
			$results = $wpdb->get_results(
				$wpdb->prepare( "
        			SELECT meta_id, meta_value
        			FROM {$wpdb->postmeta}
        			WHERE meta_key = %s
        			AND meta_id > %d
        			ORDER BY meta_id ASC
        			LIMIT 10",
					'_dfrps_cpt_temp_query',
					$status['version_140']['update_stages'][ $field_key ]['last_processed_id']
				)
			);

			if ( empty( $results ) ) {

				// No results returned from the query so mark this stage as compete.
				$status['version_140']['update_stages'][ $field_key ]['completed_at'] = self::now();

			} else {

				// For each cpt_temp_query stored
				foreach ( $results as $row ) {

					// Get the stored query and unserialize it
					$query = maybe_unserialize( $row->meta_value );

					if ( is_array( $query ) ) {

						// Loop through each query param
						foreach ( $query as $key => $param ) {

							// IDs found, let's update!
							if ( isset( $param['field'] ) && $param['field'] === 'id' ) {

								// Get all IDs from query.
								$all_ids          = dfrapi_explode_and_uniquify( $param['value'] );
								$extracted_v5_ids = dfrapi_extract_v5_ids( $all_ids );

								// If there are no V5 IDs, continue to next query param.
								if ( empty( $extracted_v5_ids ) ) {
									continue;
								}

								$extracted_v7_ids = dfrapi_extract_v7_ids( $all_ids );
								$converted_v5_ids = dfrapi_get_v7_ids_from_v5_ids( $extracted_v5_ids );

								// New set of product IDs.
								$new_ids = array_merge( $extracted_v7_ids, array_values( $converted_v5_ids ) );

								// Update $query value field.
								$query[ $key ]['value'] = implode( ', ', $new_ids );

								$result = $wpdb->update(
									$wpdb->postmeta,
									[ 'meta_value' => serialize( $query ) ],
									[ 'meta_id' => $row->meta_id ],
									[ '%s' ],   // format for meta_value
									[ '%d' ]    // format for meta_id
								);

								if ( $result ) {
									$v5_ids_updated += count( $converted_v5_ids );
								}
							}
						}
					}

					$status['version_140']['update_stages'][ $field_key ]['last_processed_id'] = $row->meta_id;
					$status['version_140']['update_stages'][ $field_key ]['v5_ids_updated']    = $v5_ids_updated;
				}
			}

			$this->update_status( $status );
		}

		private function handle_postmeta_dfrps_cpt_manually_added_ids( array $status ): void {

			global $wpdb;

			$field_key = 'postmeta__dfrps_cpt_manually_added_ids';

			// Get current number of V5 IDs which have been updated.
			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];

			// If this is the first iteration, set the "started_at" date.
			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			// Query all postmeta rows where meta_key = "_dfrps_cpt_manually_added_ids" and limit it to 10 starting at last processed meta_id.
			$results = $wpdb->get_results(
				$wpdb->prepare( "
        			SELECT meta_id, meta_value
        			FROM {$wpdb->postmeta}
        			WHERE meta_key = %s
        			AND meta_id > %d
        			ORDER BY meta_id ASC
        			LIMIT 10",
					'_dfrps_cpt_manually_added_ids',
					$status['version_140']['update_stages'][ $field_key ]['last_processed_id']
				)
			);

			if ( empty( $results ) ) {

				// No results returned from the query so mark this stage as compete.
				$status['version_140']['update_stages'][ $field_key ]['completed_at'] = self::now();

			} else {

				// For each _dfrps_cpt_manually_added_ids stored
				foreach ( $results as $row ) {

					// Get the stored query and unserialize it
					$query = maybe_unserialize( $row->meta_value );

					if ( is_array( $query ) ) {

						// Get all IDs from query.
						$all_ids          = $query;
						$extracted_v5_ids = dfrapi_extract_v5_ids( $all_ids );

						// If there are V5 IDs, update them.
						if ( ! empty( $extracted_v5_ids ) ) {

							$extracted_v7_ids = dfrapi_extract_v7_ids( $all_ids );
							$converted_v5_ids = dfrapi_get_v7_ids_from_v5_ids( $extracted_v5_ids );

							// New set of product IDs.
							$new_ids = array_merge( $extracted_v7_ids, array_values( $converted_v5_ids ) );

							$wpdb->update(
								$wpdb->postmeta,
								[ 'meta_value' => serialize( $new_ids ) ],
								[ 'meta_id' => $row->meta_id ],
								[ '%s' ],   // format for meta_value
								[ '%d' ]    // format for meta_id
							);

							$v5_ids_updated += count( $converted_v5_ids );
						}
					}

					$status['version_140']['update_stages'][ $field_key ]['last_processed_id'] = $row->meta_id;
					$status['version_140']['update_stages'][ $field_key ]['v5_ids_updated']    = $v5_ids_updated;
				}
			}

			$this->update_status( $status );
		}

		private function handle_postmeta_dfrps_cpt_manually_blocked_ids( array $status ): void {

			global $wpdb;

			$field_key = 'postmeta__dfrps_cpt_manually_blocked_ids';

			// Get current number of V5 IDs which have been updated.
			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];

			// If this is the first iteration, set the "started_at" date.
			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			// Query all postmeta rows where meta_key = "_dfrps_cpt_manually_blocked_ids" and limit it to 10 starting at last processed meta_id.
			$results = $wpdb->get_results(
				$wpdb->prepare( "
        			SELECT meta_id, meta_value
        			FROM {$wpdb->postmeta}
        			WHERE meta_key = %s
        			AND meta_id > %d
        			ORDER BY meta_id ASC
        			LIMIT 10",
					'_dfrps_cpt_manually_blocked_ids',
					$status['version_140']['update_stages'][ $field_key ]['last_processed_id']
				)
			);

			error_log( '$results' . ': ' . print_r( $results, true ) );

			if ( empty( $results ) ) {

				// No results returned from the query so mark this stage as compete.
				$status['version_140']['update_stages'][ $field_key ]['completed_at'] = self::now();

			} else {

				// For each _dfrps_cpt_manually_blocked_ids stored
				foreach ( $results as $row ) {

					// Get the stored query and unserialize it
					$query = maybe_unserialize( $row->meta_value );
					error_log( '$query' . ': ' . print_r( $query, true ) );

					if ( is_array( $query ) ) {

						// Get all IDs from query.
						$all_ids          = $query;
						$extracted_v5_ids = dfrapi_extract_v5_ids( $all_ids );

						// If there are V5 IDs, update them
						if ( ! empty( $extracted_v5_ids ) ) {

							$extracted_v7_ids = dfrapi_extract_v7_ids( $all_ids );
							$converted_v5_ids = dfrapi_get_v7_ids_from_v5_ids( $extracted_v5_ids );

							// New set of product IDs.
							$new_ids = array_merge( $extracted_v7_ids, array_values( $converted_v5_ids ) );

							$wpdb->update(
								$wpdb->postmeta,
								[ 'meta_value' => serialize( $new_ids ) ],
								[ 'meta_id' => $row->meta_id ],
								[ '%s' ],   // format for meta_value
								[ '%d' ]    // format for meta_id
							);

							$v5_ids_updated += count( $converted_v5_ids );
						}
					}

					$status['version_140']['update_stages'][ $field_key ]['last_processed_id'] = $row->meta_id;
					$status['version_140']['update_stages'][ $field_key ]['v5_ids_updated']    = $v5_ids_updated;
				}
			}

			$this->update_status( $status );
		}

		private function update_status( array $status ) {
			update_option( 'dfrapi_plugin_upgrade_status', $status );
		}

		public function admin_notice() {
			$status = self::get_upgrade_status();
			?>
			<div class="notice notice-info">
				<p><strong><?php _e( 'Datafeedr Update in Progress&hellip;', 'datafeedr-api' ); ?></strong></p>
				<p><?php _e( 'Datafeedr is currently performing updates to the product IDs stored in the database. This process could take an hour or longer. During this time, automated processes like Product Set updates have been disabled. They will be re-enabled once this process is complete. No action is required. This process is fully automated.', 'datafeedr-api' ); ?></p>
				<table>
					<tr>
						<th style="text-align: left"><?php _e( 'Update Started:', 'datafeedr-api' ); ?></th>
						<td><?php esc_html_e( $status['version_140']['update_started_at'] ); ?></td>
					</tr>
					<tr>
						<th style="text-align: left"><?php _e( 'Updating _dfrps_product_id:', 'datafeedr-api' ); ?></th>
						<td>
							<?php if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_product_id']['started_at'] ) ) : ?>
								<?php _e( 'Not started yet', 'datafeedr-api' ); ?>
							<?php else : ?>
								<?php _e( 'Started at ', 'datafeedr-api' ); ?>
								<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_product_id']['started_at'] ); ?>
								<?php if ( ! is_null( $status['version_140']['update_stages']['postmeta__dfrps_product_id']['completed_at'] ) ) : ?>
									— <?php _e( 'Completed at ', 'datafeedr-api' ); ?>
									<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_product_id']['completed_at'] ); ?>
								<?php endif; ?>
								— <?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_product_id']['v5_ids_updated'] ); ?>
								<?php echo _n( 'ID updated', 'IDs updated', $status['version_140']['update_stages']['postmeta__dfrps_product_id']['v5_ids_updated'], 'datafeedr-api' ) ?>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th style="text-align: left"><?php _e( 'Updating _dfrps_cpt_query:', 'datafeedr-api' ); ?></th>
						<td>
							<?php if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_query']['started_at'] ) ) : ?>
								<?php _e( 'Not started yet', 'datafeedr-api' ); ?>
							<?php else : ?>
								<?php _e( 'Started at ', 'datafeedr-api' ); ?>
								<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_query']['started_at'] ); ?>
								<?php if ( ! is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_query']['completed_at'] ) ) : ?>
									— <?php _e( 'Completed at ', 'datafeedr-api' ); ?>
									<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_query']['completed_at'] ); ?>
								<?php endif; ?>
								— <?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_query']['v5_ids_updated'] ); ?>
								<?php echo _n( 'ID updated', 'IDs updated', $status['version_140']['update_stages']['postmeta__dfrps_cpt_query']['v5_ids_updated'], 'datafeedr-api' ) ?>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th style="text-align: left"><?php _e( 'Updating _dfrps_cpt_temp_query:', 'datafeedr-api' ); ?></th>
						<td>
							<?php if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_temp_query']['started_at'] ) ) : ?>
								<?php _e( 'Not started yet', 'datafeedr-api' ); ?>
							<?php else : ?>
								<?php _e( 'Started at ', 'datafeedr-api' ); ?>
								<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_temp_query']['started_at'] ); ?>
								<?php if ( ! is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_temp_query']['completed_at'] ) ) : ?>
									— <?php _e( 'Completed at ', 'datafeedr-api' ); ?>
									<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_temp_query']['completed_at'] ); ?>
								<?php endif; ?>
								— <?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_temp_query']['v5_ids_updated'] ); ?>
								<?php echo _n( 'ID updated', 'IDs updated', $status['version_140']['update_stages']['postmeta__dfrps_cpt_temp_query']['v5_ids_updated'], 'datafeedr-api' ) ?>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th style="text-align: left"><?php _e( 'Updating _dfrps_cpt_manually_added_ids:', 'datafeedr-api' ); ?></th>
						<td>
							<?php if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_added_ids']['started_at'] ) ) : ?>
								<?php _e( 'Not started yet', 'datafeedr-api' ); ?>
							<?php else : ?>
								<?php _e( 'Started at ', 'datafeedr-api' ); ?>
								<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_added_ids']['started_at'] ); ?>
								<?php if ( ! is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_added_ids']['completed_at'] ) ) : ?>
									— <?php _e( 'Completed at ', 'datafeedr-api' ); ?>
									<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_added_ids']['completed_at'] ); ?>
								<?php endif; ?>
								— <?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_added_ids']['v5_ids_updated'] ); ?>
								<?php echo _n( 'ID updated', 'IDs updated', $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_added_ids']['v5_ids_updated'], 'datafeedr-api' ) ?>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th style="text-align: left"><?php _e( 'Updating _dfrps_cpt_manually_blocked_ids:', 'datafeedr-api' ); ?></th>
						<td>
							<?php if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_blocked_ids']['started_at'] ) ) : ?>
								<?php _e( 'Not started yet', 'datafeedr-api' ); ?>
							<?php else : ?>
								<?php _e( 'Started at ', 'datafeedr-api' ); ?>
								<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_blocked_ids']['started_at'] ); ?>
								<?php if ( ! is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_blocked_ids']['completed_at'] ) ) : ?>
									— <?php _e( 'Completed at ', 'datafeedr-api' ); ?>
									<?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_blocked_ids']['completed_at'] ); ?>
								<?php endif; ?>
								— <?php esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_blocked_ids']['v5_ids_updated'] ); ?>
								<?php echo _n( 'ID updated', 'IDs updated', $status['version_140']['update_stages']['postmeta__dfrps_cpt_manually_blocked_ids']['v5_ids_updated'], 'datafeedr-api' ) ?>
							<?php endif; ?>
						</td>
					</tr>
<!--					<tr>-->
<!--						<th style="text-align: left">--><?php //_e( 'Updating _dfrps_cpt_previous_update_info:', 'datafeedr-api' ); ?><!--</th>-->
<!--						<td>-->
<!--							--><?php //if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_previous_update_info']['started_at'] ) ) : ?>
<!--								--><?php //_e( 'Not started yet', 'datafeedr-api' ); ?>
<!--							--><?php //else : ?>
<!--								--><?php //_e( 'Started at ', 'datafeedr-api' ); ?>
<!--								--><?php //esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_previous_update_info']['started_at'] ); ?>
<!--								--><?php //if ( ! is_null( $status['version_140']['update_stages']['postmeta__dfrps_cpt_previous_update_info']['completed_at'] ) ) : ?>
<!--									— --><?php //_e( 'Completed at ', 'datafeedr-api' ); ?>
<!--									--><?php //esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_previous_update_info']['completed_at'] ); ?>
<!--								--><?php //endif; ?>
<!--								— --><?php //esc_html_e( $status['version_140']['update_stages']['postmeta__dfrps_cpt_previous_update_info']['v5_ids_updated'] ); ?>
<!--								--><?php //echo _n( 'ID updated', 'IDs updated', $status['version_140']['update_stages']['postmeta__dfrps_cpt_previous_update_info']['v5_ids_updated'], 'datafeedr-api' ) ?>
<!--							--><?php //endif; ?>
<!--						</td>-->
<!--					</tr>-->
					<tr>
						<th style="text-align: left"><?php _e( 'Updating dfrcs_compsets:', 'datafeedr-api' ); ?></th>
						<td>
							<?php if ( is_null( $status['version_140']['update_stages']['dfrcs_compsets']['started_at'] ) ) : ?>
								<?php _e( 'Not started yet', 'datafeedr-api' ); ?>
							<?php else : ?>
								<?php _e( 'Started at ', 'datafeedr-api' ); ?>
								<?php esc_html_e( $status['version_140']['update_stages']['dfrcs_compsets']['started_at'] ); ?>
								<?php if ( ! is_null( $status['version_140']['update_stages']['dfrcs_compsets']['completed_at'] ) ) : ?>
									— <?php _e( 'Completed at ', 'datafeedr-api' ); ?>
									<?php esc_html_e( $status['version_140']['update_stages']['dfrcs_compsets']['completed_at'] ); ?>
								<?php endif; ?>
								— <?php esc_html_e( $status['version_140']['update_stages']['dfrcs_compsets']['v5_ids_updated'] ); ?>
								<?php echo _n( 'ID updated', 'IDs updated', $status['version_140']['update_stages']['dfrcs_compsets']['v5_ids_updated'], 'datafeedr-api' ) ?>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th style="text-align: left"><?php _e( 'Updating dfrps_temp_product_data:', 'datafeedr-api' ); ?></th>
						<td>
							<?php if ( is_null( $status['version_140']['update_stages']['dfrps_temp_product_data']['started_at'] ) ) : ?>
								<?php _e( 'Not started yet', 'datafeedr-api' ); ?>
							<?php else : ?>
								<?php _e( 'Started at ', 'datafeedr-api' ); ?>
								<?php esc_html_e( $status['version_140']['update_stages']['dfrps_temp_product_data']['started_at'] ); ?>
								<?php if ( ! is_null( $status['version_140']['update_stages']['dfrps_temp_product_data']['completed_at'] ) ) : ?>
									— <?php _e( 'Completed at ', 'datafeedr-api' ); ?>
									<?php esc_html_e( $status['version_140']['update_stages']['dfrps_temp_product_data']['completed_at'] ); ?>
								<?php endif; ?>
								— <?php esc_html_e( $status['version_140']['update_stages']['dfrps_temp_product_data']['v5_ids_updated'] ); ?>
								<?php echo _n( 'ID updated', 'IDs updated', $status['version_140']['update_stages']['dfrps_temp_product_data']['v5_ids_updated'], 'datafeedr-api' ) ?>
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</div>
			<?php
		}


	}
}