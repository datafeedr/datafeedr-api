<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Response {

	/**
	 * @var array|WP_Error $response
	 */
	public $response;

	/**
	 * @var Dfrapi_Api_Request $request
	 */
	private $request;

	/**
	 * @var string $output
	 */
	private $output;

	/**
	 * @var null|WP_Error $error
	 */
	public $error = null;

	/**
	 * @var string $date The date and time in which this response was generated.
	 */
	public $date;

	public function __construct( $response, Dfrapi_Api_Request $request, string $output = 'array' ) {
		$this->response = $response;
		$this->request  = $request;
		$this->output   = $output === 'object' ? 'object' : 'array';
		$this->date     = date_i18n( 'Y-m-d H:i:s' );
		$this->set_error();
	}

	/**
	 * Set $this->error to WP_Error if an error occurred during this request.
	 */
	private function set_error(): void {

		if ( is_wp_error( $this->response ) ) {
			$this->error = $this->response;
		}

		if ( $this->response_code() >= 400 ) {
			$this->error = new WP_Error(
				$this->response_code(),
				$this->error_message(),
				[
					'_response_message' => $this->response_message(),
					'_body_message'     => $this->body_message(),
					'_request_action'   => $this->request->action,
					'_request_data'     => $this->safe_request_data(),
				]
			);
		}
	}

	/**
	 * Create error message for WP_Error.
	 *
	 * @return string
	 */
	private function error_message(): string {
		return sprintf( '%s (%s)', $this->response_message(), $this->body_message() );
	}

	/**
	 * Retrieve message returned
	 *
	 * @return string
	 */
	private function response_message(): string {
		return wp_remote_retrieve_response_message( $this->response );
	}

	/**
	 * Format error message based on values from the body() returned from Datafeedr.
	 *
	 * @return string
	 */
	public function body_message(): string {
		return sprintf(
			__( 'Datafeedr Error {%s} — %s [%s]', 'datafeedr-api' ),
			$this->body_error_code(),
			$this->body_error_message(),
			$this->body_error_details()
		);
	}

	public function body_error_tye(): int {
		return $this->get_prop( 'type', 9 );
	}

	public function body_error_code(): int {
		return $this->get_prop( 'error', 990 );
	}

	public function body_error_message(): string {
		return $this->get_prop( 'message', '' );
	}

	public function body_error_details(): string {
		return $this->get_prop( 'details', '' );
	}

	public function response_code(): int {
		return absint( wp_remote_retrieve_response_code( $this->response ) );
	}

	public function is_ok(): bool {
		return $this->error === null;
	}

	public function body() {
		return json_decode( wp_remote_retrieve_body( $this->response ), ( $this->output === 'array' ) );
	}

	public function json(): string {
		return wp_remote_retrieve_body( $this->response );
	}

	public function status() {
		return $this->get_prop( 'status' );
	}

	/**
	 * An array of stdClass objects containing product/record values.
	 *
	 * @return array
	 */
	public function records(): array {
		return $this->get_prop( 'products', [] );
	}

	/**
	 * An array of stdClass objects containing network group values.
	 *
	 * @return array
	 */
	public function groups(): array {
		return $this->get_prop( 'groups', [] );
	}

	/**
	 * An array of stdClass objects containing network values.
	 *
	 * @return array
	 */
	public function networks(): array {
		return $this->get_prop( 'networks', [] );
	}

	/**
	 * An array containing merchant values.
	 *
	 * @return array
	 */
	public function merchants(): array {
		return $this->get_prop( 'merchants', [] );
	}

	/**
	 * The number of results returned for this request.
	 *
	 * Null if property does not exist.
	 *
	 * @return int|null
	 */
	public function number_of_results_returned(): ?int {
		return $this->get_prop( 'length' );
	}

	/**
	 * The total number of results that are returnable (possibly using offset). Typically, 10,000.
	 *
	 * Null if property does not exist.
	 *
	 * @return int|null
	 */
	public function number_of_results_returnable(): ?int {
		return $this->get_prop( 'result_count' );
	}

	/**
	 * The total number of results matching query. If this value is greater than the number_of_results_returnable()
	 * value, then it won't be possible to access those results which are outside the range of 1 ~ 10,000.
	 *
	 * Null if property does not exist.
	 *
	 * @return int|null
	 */
	public function number_of_results_found(): ?int {
		return $this->get_prop( 'found_count' );
	}

	public function get_date(): string {
		return $this->date;
	}

	private function get_prop( string $prop, $default = null ) {
		return $this->output === 'array' ? ( $this->body()[ $prop ] ?? $default ) : ( $this->body()->{$prop} ?? $default );
	}

	/**
	 * Returns request data with API values obfuscated.
	 */
	public function safe_request_data(): array {

		$data = $this->request->data;

		foreach ( $data as $k => $v ) {
			if ( in_array( $v, [ dfrapi_get_datafeedr_access_id(), dfrapi_get_datafeedr_secret_key() ], true ) ) {
				$len = mb_strlen( $v );
				$beg = mb_substr( $v, 0, 2 );
				$mid = str_repeat( '*', ( $len - 4 ) );
				$end = mb_substr( $v, - 2 );

				$data[ $k ] = $beg . $mid . $end;
			}
		}

		return $data;
	}
}