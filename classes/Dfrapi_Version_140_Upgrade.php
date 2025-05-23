<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Version_140_Upgrade' ) ) {

	/**
	 * Dfrapi_Version_140_Upgrade
	 */
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

			// Check current upgrade status.
			$current_status = get_option( 'dfrapi_plugin_upgrade_status' );

			// If there is no current upgrade status, kick things off!
			if ( $current_status === false ) {

				$status = [
					'version_140' => [
						'update_started_at'         => self::now(),
						'update_completed_at'       => null,
						'product_set_update_status' => self::get_dfrps_update_status(), // So we can re-enable if needed
						'update_stages'             => self::get_update_stages(),
					]
				];

				add_option( 'dfrapi_plugin_upgrade_status', $status, '', false );

				self::disable_product_set_updates();

				self::schedule_upgrade_action_event();

			}
		}

		/**
		 * @return string DateTime of now (in user's timezone)
		 */
		public static function now(): string {
			return current_time( 'mysql' );
		}

		/**
		 * Schedules the upgrade action event if it is not already scheduled.
		 *
		 * @return void
		 */
		public static function schedule_upgrade_action_event() {
			if ( ! wp_next_scheduled( 'dfrapi_handle_version_140_upgrade_action' ) ) {
				wp_schedule_event(
					time(),
					'every_minute',
					'dfrapi_handle_version_140_upgrade_action'
				);
			}
		}

		/**
		 * Returns true if migration is in progress.
		 *
		 * @return bool
		 */
		public static function migration_is_in_progress(): bool {
			return self::version_140_update_has_started() && ! self::version_140_update_is_complete();
		}

		/**
		 * Retrieves the upgrade status configuration for the plugin.
		 *
		 * @return array
		 */
		public static function get_upgrade_status(): array {
			return get_option( 'dfrapi_plugin_upgrade_status', [] );
		}

		/**
		 * Return true if "update_started_at" is not null.
		 *
		 * @return bool
		 */
		public static function version_140_update_has_started(): bool {
			return self::get_upgrade_status()['version_140']['update_started_at'] !== null;
		}

		/**
		 * Return true if migration is complete.
		 *
		 * @return bool True if migration is complete.
		 */
		public static function version_140_update_is_complete(): bool {
			return self::get_upgrade_status()['version_140']['update_completed_at'] !== null;
		}

		/**
		 * Returns an array of update stages with their corresponding status arrays.
		 *
		 * @return array
		 */
		public static function get_update_stages(): array {

			/**
			 * We are not doing the following:
			 *
			 * 'postmeta__dfrps_cpt_previous_update_info' Not doing this as it would represent an inaccurate state
			 * 'dfrps_temp_product_data' Not doing because this is already using r7 IDs.
			 */
			return [
				'postmeta__dfrps_product_id'               => self::get_stage_status_array(),
				'postmeta__dfrps_cpt_query'                => self::get_stage_status_array(),
				'postmeta__dfrps_cpt_temp_query'           => self::get_stage_status_array(),
				'postmeta__dfrps_cpt_manually_added_ids'   => self::get_stage_status_array(),
				'postmeta__dfrps_cpt_manually_blocked_ids' => self::get_stage_status_array(),
				'dfrcs_compsets'                           => self::get_stage_status_array(),
				'woocommerce_sku'                          => self::get_stage_status_array(),
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

		/**
		 * Handles each stage of the upgrade process.
		 *
		 * @return void
		 */
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

			// Handle dfrcs_compsets stage
			if ( is_null( $status['version_140']['update_stages']['dfrcs_compsets']['completed_at'] ) ) {
				$this->handle_dfrcs_compsets( $status );

				return;
			}

			// Handle woocommerce_sku stage
			if ( is_null( $status['version_140']['update_stages']['woocommerce_sku']['completed_at'] ) ) {
				$this->handle_woocommerce_skus( $status );

				return;
			}

			// Update "update_completed_at". We're done!
			$this->mark_update_as_complete( $status );
		}

		/**
		 * Handles the processing and updates of the `_dfrps_product_id` post meta during a specific stage of migration.
		 *
		 * This method retrieves a batch of postmeta records with specific criteria, converts their values to a new format,
		 * updates the database, and tracks the progress of the updates in the provided status array.
		 *
		 * @param array $status An associative array that contains migration status details, including tracking information
		 *                      for the `_dfrps_product_id` processing stage.
		 *
		 * @return void
		 */
		private function handle_postmeta_dfrps_product_id_stage( array $status ): void {

			global $wpdb;

			$v5_ids_updated = (int) $status['version_140']['update_stages']['postmeta__dfrps_product_id']['v5_ids_updated'];

			if ( is_null( $status['version_140']['update_stages']['postmeta__dfrps_product_id']['started_at'] ) ) {
				$status['version_140']['update_stages']['postmeta__dfrps_product_id']['started_at'] = self::now();
			}

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

				// Loop through IDs, updating each one and then updating the `last_processed_id` param.
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

		/**
		 * Handles the processing of postmeta rows with the meta_key `_dfrps_cpt_query`, updating associated IDs and statuses.
		 *
		 * @param array $status An associative array representing the process and update status for version 1.4.0, including stages and update counts.
		 *
		 * @return void
		 */
		private function handle_postmeta_dfrps_cpt_query( array $status ): void {

			global $wpdb;

			$field_key = 'postmeta__dfrps_cpt_query';

			// Get the current number of V5 IDs which have been updated.
			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];

			// If this is the first iteration, set the "started_at" date.
			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			// Query all postmeta rows where meta_key = "_dfrps_cpt_temp_query" and limit it to 10 starting at the last processed meta_id.
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

				// No results returned from the query, so mark this stage as compete.
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

								// Get all IDs from the query.
								$all_ids          = dfrapi_explode_and_uniquify( $param['value'] );
								$extracted_v5_ids = dfrapi_extract_v5_ids( $all_ids );

								// If there are no V5 IDs, continue to the next query param.
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

		/**
		 * Handles processing of postmeta rows with meta_key `_dfrps_cpt_temp_query` for temporary query updates.
		 *
		 * This method processes a batch of postmeta rows, updates any identified V5 IDs to V7 IDs, and maintains progress
		 * tracking for the operation. It also serializes and updates the respective postmeta entries in the database.
		 *
		 * @param array $status An associative array representing the current update status, including progress tracking
		 *                      data such as `last_processed_id`, `v5_ids_updated`, and timestamps for the relevant update stage.
		 *
		 * @return void
		 */
		private function handle_postmeta_dfrps_cpt_temp_query( array $status ): void {

			global $wpdb;

			$field_key = 'postmeta__dfrps_cpt_temp_query';

			// Get the current number of V5 IDs which have been updated.
			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];

			// If this is the first iteration, set the "started_at" date.
			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			// Query all postmeta rows where meta_key = "_dfrps_cpt_temp_query" and limit it to 10 starting at the last processed meta_id.
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

				// No results returned from the query, so mark this stage as compete.
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

								// Get all IDs from the query.
								$all_ids          = dfrapi_explode_and_uniquify( $param['value'] );
								$extracted_v5_ids = dfrapi_extract_v5_ids( $all_ids );

								// If there are no V5 IDs, continue to the next query param.
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

		/**
		 * Handles the processing and updating of postmeta entries with the meta_key "_dfrps_cpt_manually_added_ids".
		 * The method processes batches of entries, extracts specific IDs, converts and updates them,
		 * and maintains the processing status in the provided status array.
		 *
		 * @param array $status The current status array containing tracking data for the update process,
		 *                      including information about stages, last processed ID, and update counts.
		 *
		 * @return void
		 */
		private function handle_postmeta_dfrps_cpt_manually_added_ids( array $status ): void {

			global $wpdb;

			$field_key = 'postmeta__dfrps_cpt_manually_added_ids';

			// Get the current number of V5 IDs which have been updated.
			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];
            
			// If this is the first iteration, set the "started_at" date.
			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			// Query all postmeta rows where meta_key = "_dfrps_cpt_manually_added_ids" and limit it to 10 starting at the last processed meta_id.
			$results = $wpdb->get_results(
				$wpdb->prepare( "
        			SELECT meta_id, meta_value
        			FROM {$wpdb->postmeta}
        			WHERE meta_key = %s
        			AND meta_id > %d
        			ORDER BY meta_id ASC
        			LIMIT 10",
					'_dfrps_cpt_manually_added_ids',
					(int) $status['version_140']['update_stages'][ $field_key ]['last_processed_id']
				)
			);

			if ( empty( $results ) ) {

				// No results returned from the query, so mark this stage as compete.
				$status['version_140']['update_stages'][ $field_key ]['completed_at'] = self::now();

			} else {

				// For each _dfrps_cpt_manually_added_ids stored
				foreach ( $results as $row ) {

					// Get the stored query and unserialize it
					$query = maybe_unserialize( $row->meta_value );

					if ( is_array( $query ) ) {

						// Get all IDs from the query.
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

							$v5_ids_updated += dfrapi_get_v5_v7_diff_count( $converted_v5_ids );
						}
					}

					$status['version_140']['update_stages'][ $field_key ]['last_processed_id'] = $row->meta_id;
					$status['version_140']['update_stages'][ $field_key ]['v5_ids_updated']    = $v5_ids_updated;
				}
			}

			$this->update_status( $status );
		}

		/**
		 * Handles processing of postmeta rows for manually blocked IDs related to the "dfrps_cpt" functionality.
		 * Updates the postmeta values in the database by converting V5 IDs to V7 IDs and tracks progress status.
		 *
		 * @param array $status Array containing versioning and update stage details for the current process.
		 *
		 * @return void
		 */
		private function handle_postmeta_dfrps_cpt_manually_blocked_ids( array $status ): void {

			global $wpdb;

			$field_key = 'postmeta__dfrps_cpt_manually_blocked_ids';

			// Get the current number of V5 IDs which have been updated.
			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];

			// If this is the first iteration, set the "started_at" date.
			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			// Query all postmeta rows where meta_key = "_dfrps_cpt_manually_blocked_ids" and limit it to 10 starting at the last processed meta_id.
			$results = $wpdb->get_results(
				$wpdb->prepare( "
        			SELECT meta_id, meta_value
        			FROM {$wpdb->postmeta}
        			WHERE meta_key = %s
        			AND meta_id > %d
        			ORDER BY meta_id ASC
        			LIMIT 10",
					'_dfrps_cpt_manually_blocked_ids',
					(int) $status['version_140']['update_stages'][ $field_key ]['last_processed_id']
				)
			);

			if ( empty( $results ) ) {

				// No results returned from the query, so mark this stage as compete.
				$status['version_140']['update_stages'][ $field_key ]['completed_at'] = self::now();

			} else {

				// For each _dfrps_cpt_manually_blocked_ids stored
				foreach ( $results as $row ) {

					// Get the stored query and unserialize it
					$query = maybe_unserialize( $row->meta_value );

					if ( is_array( $query ) ) {

						// Get all IDs from the query.
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

							$v5_ids_updated += dfrapi_get_v5_v7_diff_count( $converted_v5_ids );
						}
					}

					$status['version_140']['update_stages'][ $field_key ]['last_processed_id'] = $row->meta_id;
					$status['version_140']['update_stages'][ $field_key ]['v5_ids_updated']    = $v5_ids_updated;
				}
			}

			$this->update_status( $status );
		}

		/**
		 * Processes and updates the `dfrcs_compsets` component sets for database synchronization.
		 *
		 * This method iterates through database records, transforms data by converting V5 IDs to V7 IDs,
		 * and updates the corresponding database entries. It also tracks the processing progress in the status array.
		 *
		 * @param array $status The current status of the update process, including metadata such as the last processed ID
		 *                      and the number of IDs updated.
		 *
		 * @return void
		 */
		private function handle_dfrcs_compsets( array $status ): void {

			global $wpdb;

			$field_key = 'dfrcs_compsets';

			// Get the current number of V5 IDs which have been updated.
			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];

			// If this is the first iteration, set the "started_at" date.
			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			$table = $wpdb->prefix . DFRCS_TABLE;

			// Query all "dfrcs_compsets" rows and limit it to 10 starting at the last processed id.
			$results = $wpdb->get_results(
				$wpdb->prepare( "
        			SELECT id, hash, added, removed
        			FROM {$table}
        			WHERE ( ( added IS NOT NULL AND added != '') OR (removed IS NOT NULL AND removed != '' ) )
        			AND id > %d
        			ORDER BY id ASC
        			LIMIT 10",
					(int) $status['version_140']['update_stages'][ $field_key ]['last_processed_id']
				)
			);

			if ( empty( $results ) ) {

				// No results returned from the query, so mark this stage as compete.
				$status['version_140']['update_stages'][ $field_key ]['completed_at'] = self::now();
				$this->update_status( $status );

			} else {

				foreach ( $results as $row ) {

					$compset_id   = (int) $row->id;
					$compset_hash = $row->hash;
					$added        = maybe_unserialize( $row->added );
					$removed      = maybe_unserialize( $row->removed );

					$added_v5_ids = dfrapi_extract_v5_ids( $added );
					$added_v7_ids = dfrapi_extract_v7_ids( $added );

					$removed_v5_ids = dfrapi_extract_v5_ids( $removed );
					$removed_v7_ids = dfrapi_extract_v7_ids( $removed );

					if ( count( $added_v5_ids ) === 0 && count( $removed_v5_ids ) === 0 ) {
						$status['version_140']['update_stages'][ $field_key ]['last_processed_id'] = $compset_id;
						$this->update_status( $status );
						continue;
					}

					$new_added_ids   = $added;
					$new_removed_ids = $removed;

					// Handle 'added' ids
					if ( count( $added_v5_ids ) > 0 ) {
						$converted_added_v5_ids = dfrapi_get_v7_ids_from_v5_ids( $added_v5_ids );
						$v5_ids_updated         += dfrapi_get_v5_v7_diff_count( $converted_added_v5_ids );
						$new_added_ids          = array_merge( $added_v7_ids, array_values( $converted_added_v5_ids ) );
					}

					// Handle 'removed' ids
					if ( count( $removed_v5_ids ) > 0 ) {
						$converted_removed_v5_ids = dfrapi_get_v7_ids_from_v5_ids( $removed_v5_ids );
						$v5_ids_updated           += dfrapi_get_v5_v7_diff_count( $converted_removed_v5_ids );
						$new_removed_ids          = array_merge( $removed_v7_ids, array_values( $converted_removed_v5_ids ) );
					}

					$wpdb->update(
						$table,
						[ 'added' => serialize( $new_added_ids ), 'removed' => serialize( $new_removed_ids ) ],
						[ 'hash' => $compset_hash ],
						[ '%s', '%s' ],
						[ '%s' ]
					);

					$status['version_140']['update_stages'][ $field_key ]['v5_ids_updated']    = $v5_ids_updated;
					$status['version_140']['update_stages'][ $field_key ]['last_processed_id'] = $compset_id;
					$this->update_status( $status );
				}
			}
		}

		/**
		 * Handles the processing and updating of WooCommerce product SKUs for a specific migration step.
		 *
		 * This method retrieves SKUs with certain conditions, converts them to a new format,
		 * updates the database with the converted values, and tracks the processing status.
		 *
		 * @param array $status The current status of the migration process,
		 *                      including tracking information for update stages and IDs processed.
		 *
		 * @return void
		 */
		private function handle_woocommerce_skus( array $status ): void {

			global $wpdb;

			$field_key = 'woocommerce_sku';

			$v5_ids_updated = (int) $status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'];

			if ( is_null( $status['version_140']['update_stages'][ $field_key ]['started_at'] ) ) {
				$status['version_140']['update_stages'][ $field_key ]['started_at'] = self::now();
			}

			$results = $wpdb->get_results(
				$wpdb->prepare( "
        			SELECT pm.meta_id, pm.post_id, pm.meta_value, p.post_type
        			FROM {$wpdb->postmeta} AS pm
        			INNER JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID
        			WHERE pm.meta_key = %s
        			AND CHAR_LENGTH(pm.meta_value) < %d
        			AND pm.meta_id > %d
        			AND p.post_type = 'product'
        			ORDER BY pm.meta_id ASC
        			LIMIT 100",
					'_sku',
					19,
					(int) $status['version_140']['update_stages'][ $field_key ]['last_processed_id']
				)
			);

			if ( empty( $results ) ) {
				$status['version_140']['update_stages'][ $field_key ]['completed_at'] = self::now();
				$this->update_status( $status );

				return;
			}

			$v5_ids = [];

			foreach ( $results as $row ) {
				$v5_ids[] = $row->meta_value;
			}

			// Get converted IDs.
			$ids = dfrapi_get_v7_ids_from_v5_ids( $v5_ids );

			// Loop through IDs, updating each one and then updating the `last_processed_id` param.
			foreach ( $results as $row ) {

				$result = $wpdb->update(
					$wpdb->postmeta,
					[ 'meta_value' => $ids[ $row->meta_value ] ],
					[ 'meta_id' => $row->meta_id ],
					[ '%s' ],   // format for meta_value
					[ '%d' ]    // format for meta_id
				);

				$status['version_140']['update_stages'][ $field_key ]['last_processed_id'] = $row->meta_id;

				if ( $result ) {
					$v5_ids_updated ++;
					$status['version_140']['update_stages'][ $field_key ]['v5_ids_updated'] = $v5_ids_updated;
				}
			}

			$this->update_status( $status );
		}

		/**
		 * Marks the update process as complete for version 1.4.0.
		 *
		 * @param array $status The current status array containing update information.
		 *
		 * @return void
		 */
		private function mark_update_as_complete( array $status ): void {

			// Update "update_completed_at" value with timestamp.
			$status['version_140']['update_completed_at'] = self::now();
			$this->update_status( $status );

			// Enable Product Set updates if they were originally enabled.
			if ( $status['version_140']['product_set_update_status'] === 'enabled' ) {
				self::enable_product_set_updates();
			}

			// Remove scheduled event.
			wp_unschedule_event(
				wp_next_scheduled( 'dfrapi_handle_version_140_upgrade_action' ),
				'dfrapi_handle_version_140_upgrade_action'
			);
		}

		/**
		 * Updates the plugin upgrade status in the database.
		 *
		 * @param array $status The status to be saved.
		 *
		 * @return void
		 */
		private function update_status( array $status ) {
			update_option( 'dfrapi_plugin_upgrade_status', $status );
		}

		/**
		 * Displays an admin notice regarding the progress of the Datafeedr update process.
		 *
		 * This notice is shown if the update to version 1.4.0 is not yet complete. It notifies
		 * the user that updates to product IDs in the database are in progress, disables
		 * certain automated processes during the update, and ensures the update's scheduled event is set.
		 *
		 * @return void
		 */
		public function admin_notice() {

			if ( self::version_140_update_is_complete() ) {
				return;
			}

			$status = self::get_upgrade_status();

			if ( empty( $status ) ) {
				return;
			}

			// Ensure that the Cron is scheduled! Sometimes it doesn't get scheduled.
			self::schedule_upgrade_action_event();
			?>
            <div class="notice notice-info">
                <p><strong><?php _e( 'Datafeedr Update in Progress&hellip;', 'datafeedr-api' ); ?></strong></p>
                <p><?php _e( 'Datafeedr is currently performing updates to the product IDs stored in the database. This process could take an hour or longer. During this time, automated processes like Product Set updates have been disabled. They will be re-enabled once this process is complete. No action is required. This process is fully automated.', 'datafeedr-api' ); ?></p>
				<?php echo self::get_progress_table( $status ); ?>
            </div>
			<?php
		}

		/**
		 * Generates and returns an HTML table summarizing the progress of an update process.
		 *
		 * @param array $status An associative array containing the progress details of the update,
		 *                      including start and completion times, stages, and IDs updated.
		 *
		 * @return string An HTML string representing the progress table. Returns an empty string if no status is provided.
		 */
		public static function get_progress_table( array $status ): string {

			if ( empty( $status ) ) {
				return '';
			}

			ob_start();
			?>
            <table>
                <tr>
                    <th style="text-align: left; white-space: nowrap; vertical-align: top;"><?php _e( 'Update Started:', 'datafeedr-api' ); ?></th>
                    <td><?php esc_html_e( $status['version_140']['update_started_at'] ); ?></td>
                </tr>
				<?php foreach ( array_keys( self::get_update_stages() ) as $stage ): ?>
                    <tr>
                        <th style="text-align: left; white-space: nowrap; vertical-align: top;">
							<?php _e( 'Updating', 'datafeedr-api' ); ?>
							<?php esc_html_e( str_replace( 'postmeta__', '_', $stage ) ); ?>:
                        </th>
                        <td>
							<?php if ( is_null( $status['version_140']['update_stages'][ $stage ]['started_at'] ) ): ?>
								<?php _e( 'Not started yet', 'datafeedr-api' ); ?>
							<?php else: ?>
								<?php _e( 'Started at ', 'datafeedr-api' ); ?>
								<?php esc_html_e( $status['version_140']['update_stages'][ $stage ]['started_at'] ); ?>
								<?php if ( ! is_null( $status['version_140']['update_stages'][ $stage ]['completed_at'] ) ): ?>
                                    — <?php _e( 'Completed at ', 'datafeedr-api' ); ?>
									<?php esc_html_e( $status['version_140']['update_stages'][ $stage ]['completed_at'] ); ?>
								<?php endif; ?>
                                — <?php esc_html_e( $status['version_140']['update_stages'][ $stage ]['v5_ids_updated'] ); ?>
								<?php echo _n( 'ID updated', 'IDs updated', $status['version_140']['update_stages'][ $stage ]['v5_ids_updated'], 'datafeedr-api' ) ?>
							<?php endif; ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
                <tr>
                    <th style="text-align: left; white-space: nowrap; vertical-align: top;"><?php _e( 'Update Completed:', 'datafeedr-api' ); ?></th>
                    <td><?php esc_html_e( $status['version_140']['update_completed_at'] ?? '—' ); ?></td>
                </tr>
            </table>
			<?php
			return ob_get_clean();
		}
	}
}
