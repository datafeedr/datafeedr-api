<?php

/**
 * Trait Datafeedr_Timer
 *
 * Imports timer functionality into any class.
 *
 * @since 1.0.72
 */
trait Datafeedr_Timer {

	/**
	 * If used and set to TRUE, microtime() will return a float instead
	 * of a string, as described in the return values section below.
	 *
	 * @since 1.0.72
	 * @access public
	 * @var bool $microtime_as_float
	 */
	public $microtime_as_float = true;

	/**
	 * Start time.
	 *
	 * @since 1.0.72
	 * @access protected
	 * @var float $time_start
	 */
	protected $time_start = 0;

	/**
	 * Stop time.
	 *
	 * @since 1.0.72
	 * @access protected
	 * @var float $time_stop
	 */
	protected $time_stop = 0;

	/**
	 * Starts the timer.
	 *
	 * @since 1.0.72
	 */
	public function start_timer() {
		$this->time_start = microtime( $this->microtime_as_float );
	}

	/**
	 * Stops the timer.
	 *
	 * @since 1.0.72
	 */
	public function stop_timer() {
		$this->time_stop = microtime( $this->microtime_as_float );
	}

	/**
	 * Return rounded elapsed time.
	 *
	 * @param int $precision Optional. How much to round float value. Default 2.
	 *
	 * @return float
	 */
	public function execution_time( $precision = 2 ) {
		$time = $this->elapsed_time();

		return round( $time, $precision );
	}

	/**
	 * Return elapsed time.
	 *
	 * @return float
	 */
	public function elapsed_time() {
		return ( $this->time_stop - $this->time_start );
	}
}