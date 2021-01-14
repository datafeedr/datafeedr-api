<?php

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dfrapi_Cron' ) ) {

	class Dfrapi_Cron {

		/** @var string $hook A name for this cron. */
		public $hook;

		/** @var int $interval How often to run this cron in seconds. */
		public $interval;

		/** @var Closure|string|null $callback Optional. Anonymous function, function name or null to override with your own handle() method. */
		public $callback;

		/** @var array $args Optional. An array of arguments to pass into the callback. */
		public $args;

		/** @var string $recurrence How often the event should subsequently recur. See wp_get_schedules(). */
		public $recurrence;

		/**
		 * Dfrapi_Cron constructor.
		 *
		 * @param string $hook
		 * @param integer $interval
		 * @param null $callback
		 * @param array $args
		 */
		private function __construct( $hook, $interval, $callback = null, $args = [] ) {
			$this->hook     = trim( $hook );
			$this->interval = absint( $interval );
			$this->callback = $callback;
			$this->args     = $args;
			$this->set_recurrence();
			$this->schedule_event();

			add_filter( 'cron_schedules', [ $this, 'add_schedule' ] );
			add_action( $this->hook, [ $this, 'handle' ] );
		}

		/**
		 * Return instance of class.
		 *
		 * @param string $hook
		 * @param integer $interval
		 * @param null $callback
		 * @param array $args
		 *
		 * @return static
		 */
		public static function init( $hook, $interval, $callback = null, $args = [] ) {
			return new static( $hook, $interval, $callback, $args );
		}

		/**
		 * Handles the execution.
		 *
		 * IMPORTANT: If you are extending the Dfrapi_Cron class using inheritance, then you MUST
		 * include a handle() method in your child class.
		 */
		public function handle() {
			if ( is_callable( $this->callback ) ) {
				call_user_func_array( $this->callback, $this->args );
			}
		}

		/**
		 * Schedule the event if it's not already scheduled.
		 */
		public function schedule_event() {
			if ( ! wp_next_scheduled( $this->hook, $this->args ) ) {
				wp_schedule_event( time(), $this->recurrence, $this->hook, $this->args );
			}
		}

		/**
		 * Adds the new recurrence to the array of $schedules.
		 *
		 * @param array $schedules
		 *
		 * @return array
		 */
		public function add_schedule( $schedules ) {
			if ( in_array( $this->recurrence, $this->default_wp_recurrences() ) ) {
				return $schedules;
			}

			$schedules[ $this->recurrence ] = [
				'interval' => $this->interval,
				'display'  => __( 'Every ' . $this->interval . ' seconds', 'datafeedr-api' ),
			];

			return $schedules;
		}

		/**
		 * Sets the recurrence for this cron job. This will either return a default
		 * WordPress recurrence such as 'hourly' or 'twicedaily' or create its own
		 * recurrence such as 'dfrapi_every_7200_seconds' or 'dfrapi_every_43200_seconds'.
		 */
		private function set_recurrence() {
			foreach ( $this->default_wp_schedules() as $recurrence => $schedule ) {
				if ( $this->interval === absint( $schedule['interval'] ) ) {
					$this->recurrence = $recurrence;

					return;
				}
			}

			$this->recurrence = 'dfrapi_every_' . $this->interval . '_seconds';
		}

		/**
		 * Returns an array of default WordPress schedules. As of 2021-01-14, those are:
		 *
		 * Array (
		 *      [hourly] => Array (
		 *           [interval] => 3600
		 *           [display] => Once Hourly
		 *      )
		 *      [twicedaily] => Array (
		 *           [interval] => 43200
		 *           [display] => Twice Daily
		 *      )
		 *      [daily] => Array (
		 *           [interval] => 86400
		 *           [display] => Once Daily
		 *      )
		 *      [weekly] => Array (
		 *           [interval] => 604800
		 *           [display] => Once Weekly
		 *      )
		 * )
		 *
		 * @return array
		 */
		private function default_wp_schedules() {
			return array_filter( wp_get_schedules(), function ( $schedule ) {
				return in_array( $schedule, $this->default_wp_recurrences() );
			}, ARRAY_FILTER_USE_KEY );
		}

		/**
		 * Returns an array of default WordPress recurrences as listed in the wp_get_schedules() function.
		 *
		 * @return string[]
		 */
		private function default_wp_recurrences() {
			return [ 'hourly', 'twicedaily', 'daily', 'weekly' ];
		}
	}
}
