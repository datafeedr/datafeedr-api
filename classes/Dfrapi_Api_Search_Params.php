<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search_Params {

	/**
	 * @var string|null $param
	 */
	private ?string $param;

	private Dfrapi_Api_Search $search;

	/**
	 * The type of $param. Options are "filter", "option" or null.
	 *
	 * @var string|null $type
	 */
	public ?string $type;

	/**
	 * The key or name of this $param. Example: name, barcode, brand, sort, limit, etc...
	 *
	 * @var string|null $key
	 */
	public ?string $key;

	public function __construct( $param, Dfrapi_Api_Search $search ) {
		$this->set_param( $param );
		$this->set_key();
		$this->set_type();
		$this->search = $search;
	}

	/**
	 * @return Dfrapi_Search_Filter_Interface[]
	 * @todo maybe add apply_filters here so this can be extended.
	 */
	public static function filters(): array {
		return [
			Dfrapi_Any_Filter::class,
			Dfrapi_Name_Filter::class,
			Dfrapi_Id_Filter::class,
			Dfrapi_Barcode_Filter::class,
			Dfrapi_Merchant_Id_Filter::class,
		];
	}

	/**
	 * @return Dfrapi_Search_Option_Interface[]
	 * @todo maybe add apply_filters here so this can be extended.
	 */
	public static function options(): array {
		return [
			Dfrapi_Sort_Option::class,
		];
	}

	public static function get_filter_names(): array {
		return array_map( static function ( $filter ) {
			return $filter::name();
		}, self::filters() );
	}

	public static function get_option_names(): array {
		return array_map( static function ( $option ) {
			return $option::name();
		}, self::options() );
	}

	public function get_class_name( array $classes ): ?string {
		return array_values( array_filter( $classes, function ( $class ) {
				return strtolower( $class::name() ) === $this->key;
			} ) )[0] ?? null;
	}

	public function filter_factory(): ?Dfrapi_Search_Filter_Abstract {
		$class = $this->get_class_name( self::filters() );

		return $class ? new $class( $this->param, $this->search ) : null;
	}

	public function option_factory(): ?Dfrapi_Search_Option_Abstract {
		$class = $this->get_class_name( self::options() );

		return $class ? new $class( $this->param, $this->search ) : null;
	}

	/**
	 * Returns true if $this->param and $this->key and $this->type all have values. Otherwise, returns false.
	 *
	 * @return bool
	 */
	public function is_valid(): bool {

		if ( ! $this->param ) {
			return false;
		}

		if ( ! $this->key ) {
			return false;
		}

		if ( ! $this->type ) {
			return false;
		}

		return true;
	}

	private function set_param( $param ): void {

		// Convert $param array to a string.
		if ( is_array( $param ) ) {
			$param = sprintf( '%s %s %s',
				trim( $param['field'] ),
				trim( $param['operator'] ?? '' ),
				trim( $param['value'] ?? '' )
			);
		}

		if ( is_string( $param ) ) {
			$param       = strtolower( trim( $param ) );
			$this->param = empty( $param ) ? null : $param;
		}
	}

	/**
	 *
	 *
	 * Only set the key if $this->param exists.
	 *
	 * @return void
	 */
	private function set_key(): void {
		if ( $this->param ) {
			$this->key = dfrapi_str_before( $this->param, ' ' );
		}
	}

	/**
	 *
	 * Only set the type if $this->key is exists.
	 *
	 *
	 * @return void
	 */
	private function set_type(): void {

		if ( $this->key ) {

			if ( in_array( $this->key, self::get_filter_names(), true ) ) {
				$this->type = 'filter';
			}

			if ( in_array( $this->key, self::get_option_names(), true ) ) {
				$this->type = 'option';
			}
		}
	}
}
