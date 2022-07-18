<?php

defined( 'ABSPATH' ) || exit;

class Dfrapi_Api_Search_Operators {

	public const IN = 'IN';
	public const NOT_IN = 'NOT_IN';
	public const LIKE = 'LIKE';
	public const NOT_LIKE = 'NOT_LIKE';
	public const EMPTY = 'EMPTY';
	public const NOT_EMPTY = 'NOT_EMPTY';
	public const GTE = 'GTE';
	public const GT = 'GT';
	public const LTE = 'LTE';
	public const LT = 'LT';
	public const EQ = 'EQ';
	public const NOT_EQ = 'NOT_EQ';
	public const ALL = 'ALL';
	public const LIKE_ALL = 'LIKE_ALL';
	public const COUPONS = 'COUPONS';
	public const PRODUCTS = 'PRODUCTS';
	public const ALL_COUPONS = 'ALL_COUPONS';
	public const ALL_PRODUCTS = 'ALL_PRODUCTS';
	public const LIKE_COUPONS = 'LIKE_COUPONS';
	public const LIKE_PRODUCTS = 'LIKE_PRODUCTS';
	public const LIKE_ALL_COUPONS = 'LIKE_ALL_COUPONS';
	public const LIKE_ALL_PRODUCTS = 'LIKE_ALL_PRODUCTS';

	public function get_operator( string $operator ): ?string {
		return $this->map()[ $operator ] ?? null;
	}

	private function map() {
		return [
			self::IN                => 'IN',
			self::NOT_IN            => '!IN',
			self::LIKE              => 'LIKE',
			self::NOT_LIKE          => '!LIKE',
			self::EMPTY             => 'EMPTY',
			self::NOT_EMPTY         => '!EMPTY',
			self::GTE               => '>=',
			self::GT                => '>',
			self::LTE               => '<=',
			self::LT                => '<',
			self::EQ                => '=',
			self::NOT_EQ            => '!=',
			self::ALL               => '',
			self::LIKE_ALL          => '',
			self::COUPONS           => '',
			self::PRODUCTS          => '',
			self::ALL_COUPONS       => '',
			self::ALL_PRODUCTS      => '',
			self::LIKE_COUPONS      => '',
			self::LIKE_PRODUCTS     => '',
			self::LIKE_ALL_COUPONS  => '',
			self::LIKE_ALL_PRODUCTS => '',
		];
	}


}