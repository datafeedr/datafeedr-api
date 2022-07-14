<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search_Fields {

	public function getFields(): array {
		return [
			new Dfrapi_Any_Search_Field,
			new Dfrapi_Name_Search_Field,
			new Dfrapi_Id_Search_Field,
			new Dfrapi_Barcode_Search_Field,
		];
	}

	public function getField( string $field_name ) {
		return array_values( array_filter( $this->getFields(), static function ( Dfrapi_Search_Field_Interface $k ) use ( $field_name ) {
				return $k->field() === $field_name;
			} ) )[0] ?? null;
	}

	public static function fields(): array {
		return [
			'any'          => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'any',
				'limit'     => 5,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'id'           => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'id',
				'limit'     => 1,
				'operators' => [ 'IN', 'NOT_IN' ],
			],
			'barcode'      => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'barcode',
				'limit'     => 1,
				'operators' => [ 'IN', 'NOT_IN' ],
			],
			'name'         => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'name',
				'limit'     => 5,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'description'  => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'description',
				'limit'     => 3,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'tags'         => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'tags',
				'limit'     => 2,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'category'     => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'category',
				'limit'     => 2,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'currency'     => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'currency',
				'limit'     => 1,
				'operators' => [ 'IN', 'NOT_IN' ],
			],
			'brand'        => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'brand',
				'limit'     => 1,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'merchant'     => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'merchant',
				'limit'     => 1,
				'operators' => [ 'LIKE', 'NOT_LIKE', 'IN', 'NOT_IN', 'ALL', 'LIKE_ALL' ],
			],
			'source'       => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'source',
				'limit'     => 1,
				'operators' => [
					'LIKE',
					'NOT_LIKE',
					'IN',
					'NOT_IN', // @todo need ALL_NOT_IN for source and merchant
					'ALL',
					'LIKE_ALL',
					'COUPONS',
					'PRODUCTS',
					'ALL_COUPONS',
					'ALL_PRODUCTS',
					'LIKE_COUPONS',
					'LIKE_PRODUCTS',
					'LIKE_ALL_COUPONS',
					'LIKE_ALL_PRODUCTS',
				],
			],
			'price'        => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'price',
				'limit'     => 2,
				'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			],
			'saleprice'    => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'saleprice',
				'limit'     => 2,
				'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			],
			'finalprice'   => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'finalprice',
				'limit'     => 2,
				'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			],
			'salediscount' => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'salediscount',
				'limit'     => 2,
				'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			],
			'time_updated' => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'time_updated',
				'limit'     => 2,
				'operators' => [ 'EQ', 'GTE', 'LTE', 'GT', 'LT' ],
			],
			'color'        => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'color',
				'limit'     => 2,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'material'     => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'material',
				'limit'     => 2,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'size'         => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'size',
				'limit'     => 2,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'gender'       => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'gender',
				'limit'     => 2,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'condition'    => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'condition',
				'limit'     => 2,
				'operators' => [ 'LIKE', 'NOT_LIKE' ],
			],
			'onsale'       => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'onsale',
				'limit'     => 1,
				'operators' => [ 'EQ' ],
			],
			'instock'      => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'instock',
				'limit'     => 1,
				'operators' => [ 'EQ' ],
			],
			'direct_url'   => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'direct_url',
				'limit'     => 1,
				'operators' => [ 'empty', 'NOT_EMPTY' ],
			],
			'image'        => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'image',
				'limit'     => 1,
				'operators' => [ 'EQ' ],
			],
			'has_barcode'  => [
				'label'     => __( 'LABEL', 'datafeedr - api' ),
				'field'     => 'has_barcode',
				'limit'     => 1,
				'operators' => [ 'EQ' ],
			],
		];
	}

	public static function operators(): array {
		return [
			'IN'                => [ 'operator' => 'IN', 'custom' => false, ],
			'NOT_IN'            => [ 'operator' => '! IN', 'custom' => false, ],
			'LIKE'              => [ 'operator' => 'LIKE', 'custom' => false, ],
			'NOT_LIKE'          => [ 'operator' => '! LIKE', 'custom' => false, ],
			'empty'             => [ 'operator' => 'empty', 'custom' => false, ],
			'NOT_EMPTY'         => [ 'operator' => '! empty', 'custom' => false, ],
			'GTE'               => [ 'operator' => ' >= ', 'custom' => false, ],
			'GT'                => [ 'operator' => ' > ', 'custom' => false, ],
			'LTE'               => [ 'operator' => ' <= ', 'custom' => false, ],
			'LT'                => [ 'operator' => ' < ', 'custom' => false, ],
			'EQ'                => [ 'operator' => ' = ', 'custom' => false, ],
			'NOT_EQ'            => [ 'operator' => ' != ', 'custom' => false, ],
			'ALL'               => [ 'operator' => '', 'custom' => true, ],
			'LIKE_ALL'          => [ 'operator' => '', 'custom' => true, ],
			'COUPONS'           => [ 'operator' => '', 'custom' => true, ],
			'PRODUCTS'          => [ 'operator' => '', 'custom' => true, ],
			'ALL_COUPONS'       => [ 'operator' => '', 'custom' => true, ],
			'ALL_PRODUCTS'      => [ 'operator' => '', 'custom' => true, ],
			'LIKE_COUPONS'      => [ 'operator' => '', 'custom' => true, ],
			'LIKE_PRODUCTS'     => [ 'operator' => '', 'custom' => true, ],
			'LIKE_ALL_COUPONS'  => [ 'operator' => '', 'custom' => true, ],
			'LIKE_ALL_PRODUCTS' => [ 'operator' => '', 'custom' => true, ],
		];
	}
}