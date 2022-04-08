<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Request {

	public const URL = 'https://api.datafeedr.com/';

	/**
	 * @var string $action
	 */
	public $action;

	/**
	 * @var array $data
	 */
	public $data;

	/**
	 * @var string $output
	 */
	public $output;

	public function __construct( string $action, array $data = [], string $output = 'array' ) {
		$this->action = trim( sanitize_title( $action ) );
		$this->data   = $data;
		$this->output = $output === 'object' ? 'object' : 'array';
	}

	public function get_response(): Dfrapi_Api_Response {
		$response = wp_remote_post( $this->url(), $this->args() );

		return new Dfrapi_Api_Response( $response, $this, $this->output );
	}

	private function args(): array {
		return [
			'header'  => 'Content-Type: application/json',
			'body'    => json_encode( array_merge( $this->query(), $this->keys() ) ),
			'timeout' => 30,
		];
	}

	public function url(): string {
		return self::URL . $this->action;
	}

	public function datafeedr_access_id(): string {
		return dfrapi_get_datafeedr_access_id();
	}

	public function datafeedr_secret_key(): string {
		return dfrapi_get_datafeedr_secret_key();
	}

	public function query(): array {
		return $this->data;
	}

	private function keys(): array {
		return [ 'aid' => $this->datafeedr_access_id(), 'akey' => $this->datafeedr_secret_key() ];
	}
}