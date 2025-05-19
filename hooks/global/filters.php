<?php

defined( 'ABSPATH' ) || exit;

/**
 * Add custom cron schedule (1 minute)
 *
 * @param $schedules
 *
 * @return mixed
 */
function dfrapi_add_cron_schedules( $schedules ) {
	$schedules['every_minute'] = [
		'interval' => MINUTE_IN_SECONDS,
		'display'  => __( 'Every Minute', 'datafeedr-api' )
	];

	return $schedules;
}

add_filter( 'cron_schedules', 'dfrapi_add_cron_schedules' );
