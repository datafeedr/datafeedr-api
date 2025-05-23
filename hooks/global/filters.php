<?php

defined( 'ABSPATH' ) || exit;

/**
 * Adds a custom cron schedule for running tasks every minute.
 *
 * @param array $schedules An array of existing cron schedules.
 *
 * @return array Modified array of cron schedules including the new schedule.
 */
function dfrapi_add_cron_schedules( $schedules ) {
	$schedules['every_minute'] = [
		'interval' => MINUTE_IN_SECONDS,
		'display'  => __( 'Every Minute', 'datafeedr-api' )
	];

	return $schedules;
}

add_filter( 'cron_schedules', 'dfrapi_add_cron_schedules' );
