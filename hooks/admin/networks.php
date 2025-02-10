<?php

defined( 'ABSPATH' ) || exit;

/**
 * Removes the tracking ID field for specific affiliate networks.
 *
 * Filters whether a network supports tracking IDs by checking if the network's
 * group ID is in a predefined list of networks that should not show the
 * tracking ID field.
 *
 * @since 1.3.23
 *
 * @param bool $supports_tracking_id Whether the network supports tracking IDs.
 * @param array $network The network data array.
 *
 * @return bool Whether the network should support tracking IDs.
 */
function dfrapi_remove_tracking_id_field_for_specific_networks( bool $supports_tracking_id, array $network ): bool {

	$group_ids = [
		10055, // PriceRunner
	];

	if ( in_array( $network['group_id'], $group_ids ) ) {
		$supports_tracking_id = false;
	}

	return $supports_tracking_id;
}

add_filter( 'dfrapi_network_supports_tracking_id', 'dfrapi_remove_tracking_id_field_for_specific_networks', 10, 2 );
