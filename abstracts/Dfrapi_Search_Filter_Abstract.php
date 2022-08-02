<?php

defined( 'ABSPATH' ) || exit;

abstract class Dfrapi_Search_Filter_Abstract implements Dfrapi_Search_Filter_Interface {

	/**
	 * A query param.
	 *
	 *  Examples:
	 *      any LIKE iphone
	 *      name like patagonia
	 *      price GTE 50000
	 *
	 * @var string $param
	 */
	public string $param;

	/**
	 * An instance of the current Dfrapi_Api_Search object.
	 *
	 * @var Dfrapi_Api_Search
	 */
	public Dfrapi_Api_Search $search;

	/**
	 * Some queries don't require a value. If so, override in child class and set to false.
	 *
	 * @var bool $expects_value
	 */
	public bool $expects_value = true;

	/**
	 * This s the same as static::name();
	 *
	 * @var string $field
	 */
	public string $field;

	/**
	 * The Operator class
	 *
	 * @var Dfrapi_Search_Operator_Abstract|null $operator
	 * @todo make sure this type-hint works in PHP 7.4
	 */
	public ?Dfrapi_Search_Operator_Abstract $operator;

	/**
	 * The value to use in the query. Could be left as null.
	 *
	 * @var string|null $value
	 */
	public ?string $value = null;

	public function __construct( string $param, Dfrapi_Api_Search $search ) {
		$this->param  = strtolower( trim( $param ) );
		$this->search = $search;
		$this->set_field();
		$this->set_operator();
		$this->set_value();
	}

	/**
	 * Will return null if a $value is expected but doesn't exist.
	 *
	 * @return string|null
	 */
	public function format(): ?string {

		if ( ! $this->operator ) {
			return null;
		}

		if ( ! $this->expects_value ) {
			return sprintf( '%s %s', $this->field, $this->operator::name() );
		}

		if ( $this->value ) {
			return sprintf( '%s %s %s', $this->field, $this->operator::name(), $this->value );
		}

		return null;
	}

	/**
	 * Can be overwritten by child class to apply special formatting to the value.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function format_value( string $value ): string {
		return $value;
	}

	public function is_valid(): bool {

		// If a value is expected but not provided, return false.
		if ( $this->expects_value && ! $this->value ) {
			return false;
		}

		// If the $this->operator is not set, return false.
		if ( ! $this->operator ) {
			return false;
		}

		return true;
	}

	private function set_field(): void {
		$this->field = static::name();
	}

	private function set_operator(): void {
		$this->operator = Dfrapi_Api_Search_Operators::factory( $this );
	}

	private function set_value(): void {

		if ( ! $this->expects_value ) {
			return;
		}

		if ( ! $this->operator ) {
			return;
		}

		$value = $this->param;

		$value = trim( ltrim( $value, $this->field ) ); // Removes any, name, brand, etc...
		$value = trim( ltrim( $value, strtolower( $this->operator::name() ) ) ); // Removes LIKE, NOT_LIKE, IN, NOT_IN...

		/**
		 * @todo
		 * Could possibly add some data cleanup here like uniquifying a list of merchant IDs or
		 * ensuring that prices are formatted properly or cleaning up | statements
		 */


		$this->value = trim( $this->format_value( $value ) );
	}
}
