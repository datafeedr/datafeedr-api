<?php

/**
 * Datafeedr API PHP Library
 *
 * @version 2.0.3
 *
 * Copyright (c) 2007 ~ 2017, Datafeedr - All Rights Reserved
 *
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

/**
 * Class DatafeedrApi
 *
 * This is the core Datafeedr API class.
 */
class DatafeedrApi {

	protected $_accessId;
	protected $_secretKey;

	protected $_hasZlib;
	protected $_host;
	protected $_retry;
	protected $_retryTimeout;
	protected $_returnObjects;
	protected $_timeout;
	protected $_transport;
	protected $_url;
	protected $_userAgent;

	protected $_status;
	protected $_errors;

	const SORT_DESCENDING = - 1;
	const SORT_ASCENDING = + 1;

	const DEFAULT_URL = 'http://api.datafeedr.com';

	const REQUEST_COMPRESSION_THRESHOLD = 1024;

	const VERSION = '2.0.3';

	/**
	 * DatafeedrApi constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $accessId Datafeedr API Access ID.
	 * @param string $secretKey Datafeedr API Secret Key.
	 * @param array $options Options.
	 *
	 * Possble options:
	 *
	 * - url: API url. Default: 'http://api.datafeedr.com'
	 * - transport: HTTP transport name or function. Default: 'wordpress'
	 * - timeout: HTTP connection timeout, in seconds. Default: 0
	 * - returnObjects: True to return Objects. False to return associative arrays Default: false
	 * - retry: How many times to repeat a request on a temporary failure. Default: 0 (do not repeat)
	 * - retryTimeout: Timeout between retry requests, in seconds. Default: 5
	 *
	 * The `transport` option tells how HTTP requests should be made.
	 * It can be either a string that describes one of built-in transports ("curl", "file", "socket" or "wordpress"),
	 * or a callable object that should accept a URL, an array of headers and a string of post data and
	 * should return an array [int http response status, string response body].
	 *
	 *
	 * @throws DatafeedrError Throws error if any option is invalid.
	 */
	public function __construct( $accessId, $secretKey, $options = null ) {

		$this->_accessId  = $accessId;
		$this->_secretKey = $secretKey;

		$this->_errors = array(
			1 => 'DatafeedrBadRequestError',
			2 => 'DatafeedrAuthenticationError',
			3 => 'DatafeedrLimitExceededError',
			4 => 'DatafeedrQueryError',
			7 => 'DatafeedrExternalError',
			9 => 'DatafeedrServerError',
		);

		$this->_parseOptions( $options );
	}

	/**
	 * Returns API status information.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of API Status information.
	 */
	public function getStatus() {
		$this->apiCall( 'status' );

		return $this->_status;
	}

	/**
	 * Return status information from the last request.
	 *
	 * If no API request has been made, return NULL
	 *
	 * @since 1.0.0
	 *
	 * @return array|null Status information or NULL.
	 */
	public function lastStatus() {
		return $this->_status;
	}

	/**
	 * Return the list of networks.
	 *
	 * @since 1.0.0
	 *
	 * @param  integer|array $networkId Optional. Network ID or an array of network IDs.
	 * @param  boolean $includeEmpty Optional. If FALSE, omit networks with 0 products.
	 * @param  array $fields Optional. An array of fields to retrieve.
	 *
	 * @return array An array of Networks.
	 */
	public function getNetworks( $networkId = null, $includeEmpty = false, $fields = null ) {

		$request = array();

		if ( $networkId ) {
			$request['_ids'] = $this->_intarray( $networkId );
		}

		$request['skip_empty'] = intval( ! $includeEmpty );

		if ( $fields ) {
			$request['fields'] = $fields;
		}

		$response = $this->apiCall( 'networks', $request );

		return $this->_get( $response, 'networks' );
	}

	/**
	 * Return the list of merchants.
	 *
	 * @since 1.0.0
	 *
	 * @param  integer|array $networkId Optional. Network ID or array of Network IDs.
	 * @param  bool $includeEmpty Optional. If FALSE, omit merchants with 0 products.
	 * @param  array $fields Optional. An array of fields to retrieve.
	 *
	 * @return array An array of merchants.
	 */
	public function getMerchants( $networkId = null, $includeEmpty = false, $fields = null ) {

		$request = array();

		if ( $networkId ) {
			$request['source_ids'] = $this->_intarray( $networkId );
		}

		$request['skip_empty'] = intval( ! $includeEmpty );

		if ( $fields ) {
			$request['fields'] = $fields;
		}

		$response = $this->apiCall( 'merchants', $request );

		return $this->_get( $response, 'merchants' );
	}

	/**
	 * Return a list of merchants by their IDs.
	 *
	 * @since 1.0.0
	 *
	 * @param  integer|array $merchantId Merchant ID or array of Merchant IDs.
	 * @param  boolean $includeEmpty Optional. If FALSE, omit merchants with 0 products.
	 * @param  array $fields Optional. An array of fields to retrieve.
	 *
	 * @return array An array of merchants.
	 */
	public function getMerchantsById( $merchantId, $includeEmpty = false, $fields = null ) {

		$request               = array();
		$request['_ids']       = $this->_intarray( $merchantId );
		$request['skip_empty'] = intval( ! $includeEmpty );

		if ( $fields ) {
			$request['fields'] = $fields;
		}

		$response = $this->apiCall( 'merchants', $request );

		return $this->_get( $response, 'merchants' );
	}

	/**
	 * Return a list of searchable fields.
	 *
	 * @todo - Does this return a list of all fields for a specific network or only indexed/searchable fields? Update docs accordingly.
	 *
	 * @since 1.0.0
	 *
	 * @param integer|array $networkId Optional. Network ID or array of network IDs.
	 *
	 * @return array An array of searchable fields.
	 */
	public function getFields( $networkId = null ) {

		$request = array();

		if ( $networkId ) {
			$request['source_ids'] = $this->_intarray( $networkId );
		}

		$response = $this->apiCall( 'fields', $request );

		return $this->_get( $response, 'fields' );
	}

	/**
	 * Return a list of products by their IDs.
	 *
	 * @since 1.0.0
	 *
	 * @param integer|array $productId Product ID or an array of products IDs.
	 * @param array $fields Optional. An array of fields to retrieve.
	 *
	 * @return array An array of Products.
	 */
	public function getProducts( $productId, $fields = null ) {

		$request               = array();
		$request['_ids']       = $this->_intarray( $productId );
		$request['string_ids'] = 1;

		if ( $fields ) {
			$request['fields'] = $fields;
		}

		$response = $this->apiCall( 'get', $request );

		return $this->_get( $response, 'products' );
	}

	/**
	 * Return a list of Zanox merchant IDs ("zmids").
	 *
	 * @since 1.0.0
	 *
	 * @param integer|array $merchantId Merchant ID or an array of merchant IDs.
	 * @param integer $adspaceId Zanox Adspace ID.
	 * @param string $connectId Zanox Connection ID.
	 *
	 * @return array An array of arrays (adspace_id, merchant_id, program_id, zmid).
	 */
	public function getZanoxMerchantIds( $merchantId, $adspaceId, $connectId ) {

		$request = array();

		$request['merchant_ids'] = $this->_intarray( $merchantId );
		$request['adspace_id']   = $adspaceId;
		$request['connect_id']   = $connectId;

		$response = $this->apiCall( 'zanox_merchant_ids', $request );

		return $this->_get( $response, 'zanox_merchant_ids' );
	}

	/**
	 * Return a list of Partnerize campaign references ("camrefs").
	 *
	 * @since 2.0.0
	 *
	 * @param integer|array $merchantId Merchant ID or an array of merchant IDs.
	 * @param string $applicationKey Partnerize application_key.
	 * @param string $userApiKey Partnerize user_api_key.
	 * @param string $publisherId Partnerize publisher_id.
	 *
	 * @return array An array of arrays (campaign_id, camref, merchant_id).
	 */

	public function getPerformanceHorizonCamrefs( $merchantId, $applicationKey, $userApiKey, $publisherId ) {

		$request = array();

		$request['merchant_ids']    = $this->_intarray( $merchantId );
		$request['application_key'] = $applicationKey;
		$request['user_api_key']    = $userApiKey;
		$request['publisher_id']    = $publisherId;

		$response = $this->apiCall( 'performancehorizon_camrefs', $request );

		return $this->_get( $response, 'performancehorizon_camrefs' );
	}

	/**
	 * Return a list of Effiliation affiliate ids.
	 *
	 * @since 2.0.2
	 *
	 * @param integer|array $merchantId Merchant ID or an array of merchant IDs.
	 * @param string $apiKey Effiliation api_key.
	 *
	 * @return array An array of arrays (affiliate_id, merchant_id).
	 */

	public function getEffiliationAffiliateIds( $merchantId, $apiKey ) {

		$request = array();

		$request['merchant_ids'] = $this->_intarray( $merchantId );
		$request['api_key']      = $apiKey;

		$response = $this->apiCall( 'effiliation_affiliate_ids', $request );

		return $this->_get( $response, 'effiliation_affiliate_ids' );
	}

	/**
	 * Create a new DatafeedrSearchRequest object.
	 *
	 * @since 1.0.0
	 *
	 * @return DatafeedrSearchRequest
	 */
	public function searchRequest() {
		return new DatafeedrSearchRequest( $this );
	}

	/**
	 * Create a new DatafeedrMerchantSearchRequest object.
	 *
	 * @since 1.0.0
	 *
	 * @return DatafeedrMerchantSearchRequest
	 */
	public function merchantSearchRequest() {
		return new DatafeedrMerchantSearchRequest( $this );
	}

	/**
	 * Create a new DatafeedrAmazonSearchRequest object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $awsAccessKeyId Amazon Access Key.
	 * @param string $awsSecretKey Amazon Secret Key.
	 * @param string $awsAssociateTag Amazon Associates tag.
	 * @param string $locale The country locale code.
	 *
	 * @return DatafeedrAmazonSearchRequest
	 */
	public function amazonSearchRequest( $awsAccessKeyId, $awsSecretKey, $awsAssociateTag, $locale = 'US' ) {
		return new DatafeedrAmazonSearchRequest( $this, $awsAccessKeyId, $awsSecretKey, $awsAssociateTag, $locale );
	}

	/**
	 * Create a new DatafeedrAmazonLookupRequest object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $awsAccessKeyId Amazon Access Key.
	 * @param string $awsSecretKey Amazon Secret Key.
	 * @param string $awsAssociateTag Amazon Associates tag.
	 * @param string $locale The country locale code.
	 *
	 * @return DatafeedrAmazonLookupRequest
	 */
	public function amazonLookupRequest( $awsAccessKeyId, $awsSecretKey, $awsAssociateTag, $locale = 'US' ) {
		return new DatafeedrAmazonLookupRequest( $this, $awsAccessKeyId, $awsSecretKey, $awsAssociateTag, $locale );
	}

	/**
	 * Perform the raw API call.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action API Action. (Examples: status, merchants, networks, search, etc...)
	 * @param array $request Optional. Request data.
	 *
	 * @throws DatafeedrHTTPError Throws error if request status is not 200.
	 *
	 * @return array Returns $response array.
	 */
	public function apiCall( $action, $request = null ) {

		if ( ! $request ) {
			$request = array();
		}

		$request['aid']       = $this->_accessId;
		$request['timestamp'] = gmdate( 'Y-m-d H:i:s' );

		$message              = $request['aid'] . $action . $request['timestamp'];
		$request['signature'] = hash_hmac( 'sha256', $message, $this->_secretKey, false );

		$postdata = json_encode( $request );
		$url      = $this->_url . '/' . $action;
		$headers  = array(
			'Host: ' . $this->_host,
			'Content-Type: application/json',
			'Accept: application/json',
			'Connection: close',
			'User-Agent: ' . $this->_userAgent
		);

		if ( $this->_hasZlib && strlen( $postdata ) >= self::REQUEST_COMPRESSION_THRESHOLD ) {
			$postdata   = gzcompress( $postdata );
			$headers [] = 'Content-Encoding: deflate';
		}

		$headers [] = 'Content-Length: ' . strlen( $postdata );

		list( $status, $response ) = $this->_performRequest( $url, $headers, $postdata );

		if ( strlen( $response ) ) {
			$response = json_decode( $response, ! $this->_returnObjects );
		}

		$error = $this->_get( $response, 'error' );
		if ( $error ) {
			$type = $this->_get( $response, 'type' );
			$cls  = isset( $this->_errors[ $type ] ) ? $this->_errors[ $type ] : 'DatafeedrError';
			throw new $cls( $this->_get( $response, 'message' ), $error );
		}

		if ( 200 != $status ) {
			throw new DatafeedrHTTPError( "Status $status" );
		}

		$this->_status = $this->_get( $response, 'status' );

		return $response;
	}

	/**
	 * Returns the default set of options.
	 *
	 * @since 2.0.0
	 *
	 * @returns array $options.
	 *
	 */
	protected function _defaultOptions() {
		return array(
			'url'           => self::DEFAULT_URL,
			'transport'     => 'wordpress_or_curl',
			'timeout'       => 30,
			'returnObjects' => false,
			'retry'         => 0,
			'retryTimeout'  => 5
		);
	}

	/**
	 * Parse constructor options.
	 *
	 * @since 2.0.0
	 *
	 * @param array $options .
	 *
	 * @throws DatafeedrError Throws error if any option is invalid.
	 */
	protected function _parseOptions( $options ) {
		$opts = $this->_defaultOptions();

		if ( ! is_null( $options ) ) {
			if ( ! is_array( $options ) ) {
				throw new DatafeedrError( "Options must be an array" );
			}

			foreach ( $options as $key => $value ) {
				if ( isset( $opts[ $key ] ) ) {
					$opts[ $key ] = $value;
				}
			}
		}

		$ur = parse_url( $opts['url'] );
		$tr = $opts['transport'];

		$this->_url           = $opts['url'];
		$this->_host          = $ur['host'];
		$this->_timeout       = intval( $opts['timeout'] );
		$this->_returnObjects = intval( $opts['returnObjects'] );
		$this->_retry         = intval( $opts['retry'] );
		$this->_retryTimeout  = intval( $opts['retryTimeout'] );

		switch ( $tr ) {
			case 'curl':
				$this->_transport = array( $this, '_transportCurl' );
				break;
			case 'file':
				$this->_transport = array( $this, '_transportFile' );
				break;
			case 'socket':
				$this->_transport = array( $this, '_transportSocket' );
				break;
			case 'wordpress':
				if ( ! function_exists( 'wp_remote_post' ) ) {
					throw new DatafeedrError( "Wordpress transport requires wp_remote_post" );
				}
				$this->_transport = array( $this, '_transportWordpress' );
				break;
			case 'wordpress_or_curl':
				if ( ! function_exists( 'wp_remote_post' ) ) {
					$this->_transport = array( $this, '_transportCurl' );
					$tr               = 'curl';
				} else {
					$this->_transport = array( $this, '_transportWordpress' );
					$tr               = 'wordpress';
				}
				break;

			default:
				if ( ! is_callable( $tr ) ) {
					throw new DatafeedrError( "Transport must be a function" );
				}
				$this->_transport = $tr;
				$tr               = 'custom';
		}

		$this->_hasZlib   = function_exists( 'gzcompress' );
		$this->_userAgent = sprintf( 'datafeedr.php.%s/%s/zlib=%s', self::VERSION, $tr,
			$this->_hasZlib ? 'yes' : 'no' );
	}

	/**
	 * Perform an HTTP request.
	 *
	 * @since 2.0.0
	 *
	 * @param string $url
	 * @param array $headers
	 * @param string $postdata
	 *
	 * @throws DatafeedrConnectionError
	 *
	 * @return array An array of (status, responseBody)
	 */
	protected function _performRequest( $url, $headers, $postdata ) {
		$retry = $this->_retry;

		while ( true ) {
			try {
				return call_user_func( $this->_transport, $url, $headers, $postdata );
			} catch ( DatafeedrConnectionError $err ) {
				if ( $retry <= 0 ) {
					throw $err;
				}
				sleep( $this->_retryTimeout );
				$retry --;
			}
		}

		return array();
	}

	/**
	 * Convert an ID or an array of IDs to a simple array of IDs.
	 *
	 * @since 1.0.0
	 *
	 * @param integer|string|array $id_or_ids An ID or an array of IDs.
	 *
	 * @return array An array of IDs.
	 */
	protected function _intarray( $id_or_ids ) {

		if ( is_numeric( $id_or_ids ) ) {
			return array( $id_or_ids );
		}

		if ( is_array( $id_or_ids ) ) {
			return array_values( $id_or_ids );
		}

		return array();
	}

	/**
	 * Returns a specific value from an array or object for a given key or property. If key or
	 * property does not exist, returns $default.
	 *
	 * @since 1.0.0
	 *
	 * @param array|object $obj An array or object to extract value from.
	 * @param string $prop The array key or object property to get the value for.
	 * @param null $default Optional. The value to return if $obj or $prop does not exist.
	 *
	 * @return mixed|null The returned value.
	 */
	protected function _get( $obj, $prop, $default = null ) {

		if ( is_array( $obj ) && isset( $obj[ $prop ] ) ) {
			return $obj[ $prop ];
		}

		if ( is_object( $obj ) && isset( $obj->$prop ) ) {
			return $obj->$prop;
		}

		return $default;
	}

	/**
	 * Perform an HTTP POST request using the CURL library.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url Request url.
	 * @param  array $headers Array of headers.
	 * @param string $postdata Post data.
	 *
	 * @throws DatafeedrConnectionError Throws error if curl_errno() returns an error.
	 *
	 * @return array (int http status, string response body)
	 */
	protected function _transportCurl( $url, $headers, $postdata ) {

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $this->_timeout );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $postdata );
		curl_setopt( $ch, CURLOPT_ENCODING, '' );
		$response = curl_exec( $ch );
		$status   = intval( curl_getinfo( $ch, CURLINFO_HTTP_CODE ) );
		$errno    = curl_errno( $ch );
		$errmsg   = curl_error( $ch );

		curl_close( $ch );

		if ( $errno ) {
			throw new DatafeedrConnectionError( $errmsg, $errno );
		}

		return array( $status, $response );
	}

	/**
	 * Perform a HTTP post request using file functions.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url Request url.
	 * @param  array $headers Array of headers.
	 * @param string $postdata Post data.
	 *
	 * @throws DatafeedrConnectionError Throws error if $response is false.
	 *
	 * @return array (int http status, string response body)
	 */
	protected function _transportFile( $url, $headers, $postdata ) {

		$options  = array(
			'http' => array(
				'method'        => 'POST',
				'content'       => $postdata,
				'header'        => implode( "\r\n", $headers ),
				'ignore_errors' => true,
				'timeout'       => $this->_timeout,
			)
		);
		$context  = stream_context_create( $options );
		$response = file_get_contents( $url, false, $context );

		$status = 200;
		if ( isset( $http_response_header ) && isset( $http_response_header[0] ) ) {
			if ( preg_match( '/HTTP.+?(\d\d\d)/', $http_response_header[0], $match ) ) {
				$status = intval( $match[1] );
			}
		} elseif ( $response === false ) {
			throw new DatafeedrConnectionError( "Invalid response" );
		}

		return array( $status, $response );
	}

	/**
	 * Perform a HTTP post request using sockets.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url Request url.
	 * @param  array $headers Array of headers.
	 * @param string $postdata Post data.
	 *
	 * @throws DatafeedrConnectionError Throws error if $response is invalid.
	 *
	 * @return array (int http status, string response body)
	 */
	protected function _transportSocket( $url, $headers, $postdata ) {

		$parts  = parse_url( $url );
		$errno  = 0;
		$errmsg = '';

		$fp = fsockopen( $parts['host'], 80, $errno, $errmsg, $this->_timeout );
		if ( ! $fp ) {
			throw new DatafeedrConnectionError( $errmsg, $errno );
		}

		fwrite( $fp, "POST " . $parts['path'] . " HTTP/1.1\r\n" );
		fwrite( $fp, implode( "\r\n", $headers ) . "\r\n\r\n" );
		fwrite( $fp, $postdata );

		$buf = '';
		while ( ! feof( $fp ) ) {
			$buf .= fgets( $fp, 1024 );
		}
		fclose( $fp );

		$buf = explode( "\r\n\r\n", $buf, 2 );
		if ( count( $buf ) != 2 ) {
			throw new DatafeedrConnectionError( "Invalid response" );
		}
		if ( preg_match( '/HTTP.+?(\d\d\d)/', $buf[0], $match ) ) {
			$status = intval( $match[1] );
		} else {
			throw new DatafeedrConnectionError( "Invalid status" );
		}

		return array( $status, $buf[1] );
	}

	/**
	 * Perform a HTTP post request using Wordpress functions.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url Request url.
	 * @param  array $headers Array of headers.
	 * @param string $postdata Post data.
	 *
	 * @throws DatafeedrConnectionError Throws error if $response is WP_Error and the error code is 'http_request_failed'.
	 * @throws DatafeedrHTTPError Throws error if $response is WP_Error.
	 *
	 * @return array (int http status, string response body)
	 */
	protected function _transportWordpress( $url, $headers, $postdata ) {

		$ha = array();
		foreach ( $headers as $h ) {
			$h                         = explode( ':', $h, 2 );
			$ha[ strtolower( $h[0] ) ] = $h[1];
		}

		$args = array(
			'method'      => 'POST',
			'headers'     => $ha,
			'body'        => $postdata,
			'httpversion' => '1.1',
			'timeout'     => $this->_timeout,
			'blocking'    => true,
			'compress'    => false,
			'decompress'  => true,
			'user-agent'  => $ha['user-agent']
		);

		$res = wp_remote_post( $url, $args );

		if ( is_wp_error( $res ) ) {
			$code    = $res->get_error_code();
			$message = $res->get_error_message();

			if ( $code === 'http_request_failed' ) {
				throw new DatafeedrConnectionError( $message );
			} else {
				throw new DatafeedrHTTPError( $message );
			}
		}

		return array( $res['response']['code'], $res['body'] );
	}
}

/**
 * Generic Datafeedr API search request.
 */
class DatafeedrSearchRequestBase {

	/**
	 * The DatafeedrAPI object.
	 *
	 * @since 1.0.0
	 * @var DatafeedrAPI $_api
	 */
	protected $_api;

	/**
	 * An array containing the full response of the last API request.
	 *
	 * @since 1.0.0
	 * @var array $_lastResponse Response array.
	 */
	protected $_lastResponse;

	/**
	 * DatafeedrSearchRequestBase constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param DatafeedrApi $api
	 */
	public function __construct( $api ) {
		$this->_api = $api;
	}

	/**
	 * Get the number of found products.
	 *
	 * @since 1.0.0
	 *
	 * @return integer
	 */
	public function getFoundCount() {
		return $this->_responseItem( 'found_count', 0 );
	}

	/**
	 * Get the number of products that can be retrieved from the server.
	 *
	 * @since 1.0.0
	 *
	 * @return integer
	 */
	public function getResultCount() {
		return $this->_responseItem( 'result_count', 0 );
	}

	/**
	 * Returns the full response from the last search.
	 *
	 * Possible items in array include the following:
	 *
	 *      Array (
	 *          'found_count'  => integer,
	 *          'length'       => integer,
	 *          'merchants'    => array,
	 *          'networks'     => array,
	 *          'price_groups' => array,
	 *          'products'     => array,
	 *          'result_count' => integer,
	 *          'status'       => array,
	 *          'time'         => integer,
	 *          'total_found'  => integer,
	 *          'version'      => string,
	 *      )
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of the full response.
	 */
	public function getResponse() {
		return $this->_lastResponse;
	}

	/**
	 * Returns a specific item or property from the response data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prop The item or property to get from the response array or object.
	 * @param mixed $default Return if $prop is not found in the array or object.
	 *
	 * @throws DatafeedrError Throws error if $this->_lastResponse is NULL.
	 *
	 * @return mixed Specific item or property from response data.
	 */
	protected function _responseItem( $prop, $default ) {

		if ( is_null( $this->_lastResponse ) ) {
			throw new DatafeedrError( "Reading from an empty request" );
		}

		if ( is_object( $this->_lastResponse ) && isset( $this->_lastResponse->$prop ) ) {
			return $this->_lastResponse->$prop;
		}

		if ( is_array( $this->_lastResponse ) && isset( $this->_lastResponse[ $prop ] ) ) {
			return $this->_lastResponse[ $prop ];
		}

		return $default;
	}

	/**
	 * Sets the $_lastResponse property to the the full response from the last API request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action The request action (ex. status, search, merchants, networks, etc...)
	 * @param array $request The current API request.
	 */
	function _apiCall( $action, $request = null ) {
		$this->_lastResponse = $this->_api->apiCall( $action, $request );
	}
}

/**
 * Class DatafeedrSearchRequest
 *
 * Search request for Datafeedr API.
 */
class DatafeedrSearchRequest extends DatafeedrSearchRequestBase {

	protected $_query;
	protected $_sort;
	protected $_fields;
	protected $_limit;
	protected $_offset;
	protected $_priceGroups;
	protected $_excludeDuplicates;
	protected $_merchantLimit;

	/**
	 * DatafeedrSearchRequest constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param DatafeedrApi $api
	 */
	public function __construct( $api ) {

		parent::__construct( $api );

		$this->_query             = array();
		$this->_sort              = array();
		$this->_fields            = array();
		$this->_limit             = 0;
		$this->_offset            = 0;
		$this->_priceGroups       = 0;
		$this->_excludeDuplicates = '';
		$this->_merchantLimit     = 0;
	}

	/**
	 * Add a query filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filter Query filter.
	 *
	 * @return DatafeedrSearchRequest Returns $this.
	 */
	public function addFilter( $filter ) {
		$this->_query[] = $filter;

		return $this;
	}

	/**
	 * Adds a sort field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field Field name.
	 * @param integer $order One of DatafeedrApi::SORT_ASCENDING or DatafeedrApi::SORT_DESCENDING
	 *
	 * @throws DatafeedrError Throws error if sort order is invalid.
	 *
	 * @return DatafeedrSearchRequest Returns $this.
	 */
	public function addSort( $field, $order = DatafeedrApi::SORT_ASCENDING ) {

		if ( strlen( $field ) && ( $field[0] == '+' || $field[0] == '-' ) ) {
			$this->_sort [] = $field;
		} elseif ( $order == DatafeedrApi::SORT_ASCENDING ) {
			$this->_sort [] = '+' . $field;
		} elseif ( $order == DatafeedrApi::SORT_DESCENDING ) {
			$this->_sort [] = '-' . $field;
		} else {
			throw new DatafeedrError( "Invalid sort order" );
		}

		return $this;
	}

	/**
	 * Set which fields to retrieve.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields An array of fields to return for each requested item.
	 *
	 * Example:
	 *
	 *      Array (
	 *          'name',
	 *          'price',
	 *          'description',
	 *          'url',
	 *      )
	 *
	 * @return DatafeedrSearchRequest Returns $this.
	 */
	public function setFields( $fields ) {
		$this->_fields = $fields;

		return $this;
	}

	/**
	 * Exclude duplicate filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filter Equality filter in form "field1 field2 | field3".
	 *
	 * @return DatafeedrSearchRequest Returns $this.
	 */
	public function excludeDuplicates( $filter ) {

		if ( is_array( $filter ) ) {
			$filter = implode( ' ', $filter );
		}

		$this->_excludeDuplicates = $filter;

		return $this;
	}

	/**
	 * Set a limit of number of records to return.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $limit The maximum number of records to return.
	 *
	 * @return DatafeedrSearchRequest Returns $this.
	 */
	public function setLimit( $limit ) {
		$this->_limit = $limit;

		return $this;
	}

	/**
	 * Set an offset for the search.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $offset The offset.
	 *
	 * @return DatafeedrSearchRequest Returns $this.
	 */
	public function setOffset( $offset ) {
		$this->_offset = $offset;

		return $this;
	}

	/**
	 * Set a limit of results by merchant.
	 *
	 * @since 2.0.3
	 *
	 * @param integer $limit The limit.
	 *
	 * @return DatafeedrSearchRequest Returns $this.
	 */
	public function setMerchantLimit( $limit ) {
		$this->_merchantLimit = $limit;

		return $this;
	}

	/**
	 * Set a price group count.
	 *
	 * This should be used in conjunction with the $this->getPriceGroups() method.
	 *
	 * Setting $groups to 3 will organize the products into 3 price groups like this:
	 *
	 *    Array (
	 *        [0] => Array (
	 *                [product_count] => 321
	 *                [max] => 9633332
	 *                [min] => 0
	 *            )
	 *        [1] => Array (
	 *                [product_count] => 116
	 *                [max] => 19266665
	 *                [min] => 9633333
	 *            )
	 *        [2] => Array (
	 *                [product_count] => 43
	 *                [max] => 28900000
	 *                [min] => 19266666
	 *            )
	 *    )
	 *
	 * @since 1.0.0
	 *
	 * @param integer $groups Number of price groups to create.
	 *
	 * @return DatafeedrSearchRequest Returns $this.
	 */
	public function setPriceGroups( $groups ) {
		$this->_priceGroups = $groups;

		return $this;
	}

	/**
	 * Get networks found in this request.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getNetworks() {
		return $this->_responseItem( 'networks', array() );
	}

	/**
	 * Get merchants found in this request.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getMerchants() {
		return $this->_responseItem( 'merchants', array() );
	}

	/**
	 * Get price groups found in this request.
	 *
	 * This must be used in conjunction with $this->setPriceGroups();
	 *
	 * Returns an array like this:
	 *
	 *    Array (
	 *        [0] => Array (
	 *                [product_count] => 321
	 *                [max] => 9633332
	 *                [min] => 0
	 *            )
	 *        [1] => Array (
	 *                [product_count] => 116
	 *                [max] => 19266665
	 *                [min] => 9633333
	 *            )
	 *        [2] => Array (
	 *                [product_count] => 43
	 *                [max] => 28900000
	 *                [min] => 19266666
	 *            )
	 *    )
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of price groups and the product count, min and max prices in each group.
	 */
	public function getPriceGroups() {
		return $this->_responseItem( 'price_groups', array() );
	}

	/**
	 * Create a request array to use for querying the API.
	 *
	 * @since 1.0.0
	 *
	 * @return array The $request array().
	 */
	public function getParams() {

		$request = array();

		if ( $this->_query ) {
			$request['query'] = $this->_query;
		}

		if ( $this->_sort ) {
			$request['sort'] = $this->_sort;
		}

		if ( $this->_fields ) {
			$request['fields'] = $this->_fields;
		}

		if ( $this->_limit ) {
			$request['limit'] = $this->_limit;
		}

		if ( $this->_offset ) {
			$request['offset'] = $this->_offset;
		}

		if ( $this->_priceGroups ) {
			$request['price_groups'] = $this->_priceGroups;
		}

		if ( $this->_merchantLimit ) {
			$request['merchant_limit'] = $this->_merchantLimit;
		}

		if ( $this->_excludeDuplicates ) {
			$request['exclude_duplicates'] = $this->_excludeDuplicates;
		}

		$request['string_ids'] = 1;

		return $request;
	}

	/**
	 * Run search and return a list of products.
	 *
	 * @since 1.0.0
	 *
	 * @throws DatafeedrError Throw error if query is empty.
	 *
	 * @return array An array of products.
	 */
	public function execute() {

		$params = $this->getParams();

		if ( ! isset( $params['query'] ) ) {
			throw new DatafeedrError( "Query can't be empty" );
		}

		$this->_apiCall( 'search', $params );

		return $this->_responseItem( 'products', array() );
	}
}

/**
 * Search request for Datafeedr Merchants.
 */
class DatafeedrMerchantSearchRequest extends DatafeedrSearchRequestBase {

	protected $_query;
	protected $_sort;
	protected $_fields;
	protected $_limit;
	protected $_offset;

	/**
	 * DatafeedrMerchantSearchRequest constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param DatafeedrApi $api
	 */
	public function __construct( $api ) {

		parent::__construct( $api );

		$this->_query  = array();
		$this->_sort   = array();
		$this->_fields = array();
		$this->_limit  = 0;
		$this->_offset = 0;
	}

	/**
	 * Add a query filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filter Query filter.
	 *
	 * @return DatafeedrMerchantSearchRequest Returns $this.
	 */
	public function addFilter( $filter ) {
		$this->_query [] = $filter;

		return $this;
	}

	/**
	 * Add a sort field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field Field name.
	 * @param integer $order One of DatafeedrApi::SORT_ASCENDING or DatafeedrApi::SORT_DESCENDING
	 *
	 * @throws DatafeedrError Throw error if invalid sort order.
	 *
	 * @return DatafeedrMerchantSearchRequest Returns $this.
	 */
	public function addSort( $field, $order = DatafeedrApi::SORT_ASCENDING ) {

		if ( strlen( $field ) && ( $field[0] == '+' || $field[0] == '-' ) ) {
			$this->_sort [] = $field;
		} elseif ( $order == DatafeedrApi::SORT_ASCENDING ) {
			$this->_sort [] = '+' . $field;
		} elseif ( $order == DatafeedrApi::SORT_DESCENDING ) {
			$this->_sort [] = '-' . $field;
		} else {
			throw new DatafeedrError( "Invalid sort order" );
		}

		return $this;
	}

	/**
	 * Set which fields to retrieve.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields An array of field names.
	 *
	 * @return DatafeedrMerchantSearchRequest Returns $this.
	 */
	public function setFields( $fields ) {
		$this->_fields = $fields;

		return $this;
	}

	/**
	 * Set a limit.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $limit Number of items to return.
	 *
	 * @return DatafeedrMerchantSearchRequest Returns $this.
	 */
	public function setLimit( $limit ) {
		$this->_limit = $limit;

		return $this;
	}

	/**
	 * Set an offset.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $offset The offset.
	 *
	 * @return DatafeedrMerchantSearchRequest Returns $this.
	 */
	public function setOffset( $offset ) {
		$this->_offset = $offset;

		return $this;
	}

	/**
	 * Get networks found in this request.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getNetworks() {
		return $this->_responseItem( 'networks', array() );
	}

	/**
	 * Get merchants found in this request.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getMerchants() {
		return $this->_responseItem( 'merchants', array() );
	}

	/**
	 * Run search and return an array of merchants.
	 *
	 * @since 1.0.0
	 *
	 * @throws DatafeedrError Throws error if query is empty.
	 *
	 * @return array Array of merchants
	 */
	public function execute() {

		$params = $this->getParams();

		if ( ! isset( $params['query'] ) ) {
			throw new DatafeedrError( "Query can't be empty" );
		}

		$this->_apiCall( 'merchant_search', $params );

		return $this->_responseItem( 'merchants', array() );
	}

	/**
	 * Create a request array to use for querying the API.
	 *
	 * @since 1.0.0
	 *
	 * @return array The $request array().
	 */
	public function getParams() {

		$request = array();

		if ( $this->_query ) {
			$request['query'] = $this->_query;
		}

		if ( $this->_sort ) {
			$request['sort'] = $this->_sort;
		}

		if ( $this->_fields ) {
			$request['fields'] = $this->_fields;
		}

		if ( $this->_limit ) {
			$request['limit'] = $this->_limit;
		}

		if ( $this->_offset ) {
			$request['offset'] = $this->_offset;
		}

		return $request;
	}
}

/**
 * Generic Amazon request.
 */
class DatafeedrAmazonRequest extends DatafeedrSearchRequestBase {

	protected $_found = - 1;

	protected $_hosts;
	protected $_params;
	protected $_locale;
	protected $_awsAccessKeyId;
	protected $_awsSecretKey;
	protected $_awsAssociateTag;

	const AWS_VERSION = "2011-08-01";

	/**
	 * DatafeedrAmazonRequest constructor
	 *
	 * @since 1.0.0
	 *
	 * @param DatafeedrApi $api
	 * @param string $awsAccessKeyId Amazon Access Key.
	 * @param string $awsSecretKey Amazon Secret Key.
	 * @param string $awsAssociateTag Amazon Associate tag.
	 * @param string $locale Optional. Amazon locale (two-letter code).
	 *
	 * @throws DatafeedrError Throws error if Amazon Locale is invalid.
	 */
	public function __construct( $api, $awsAccessKeyId, $awsSecretKey, $awsAssociateTag, $locale = 'US' ) {

		parent::__construct( $api );

		$this->_hosts = array(
            'AU' => 'webservices.amazon.com.au',
			'BR' => 'webservices.amazon.com.br',
			'CA' => 'webservices.amazon.ca',
			'CN' => 'webservices.amazon.cn',
			'DE' => 'webservices.amazon.de',
			'ES' => 'webservices.amazon.es',
			'FR' => 'webservices.amazon.fr',
			'IN' => 'webservices.amazon.in',
			'IT' => 'webservices.amazon.it',
			'JP' => 'webservices.amazon.co.jp',
			'MX' => 'webservices.amazon.com.mx',
            'TR' => 'webservices.amazon.com.tr',
			'UK' => 'webservices.amazon.co.uk',
			'US' => 'webservices.amazon.com',
		);

		$this->_params = array();
		$this->_locale = strtoupper( $locale );

		if ( ! isset( $this->_hosts[ $this->_locale ] ) ) {
			throw new DatafeedrError( 'Invalid Amazon locale' );
		}

		$this->_awsAccessKeyId  = $awsAccessKeyId;
		$this->_awsSecretKey    = $awsSecretKey;
		$this->_awsAssociateTag = $awsAssociateTag;
	}

	/**
	 * Returns all parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getParams() {
		return $this->_params;
	}

	/**
	 * Returns Amazon HTTP Post URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $operation Type of query. (Ex. ItemSearch, ItemLookup)
	 * @param array $params An array of query params. Example:
	 *        Array (
	 *            [AWSAccessKeyId] => f8kCGEgcjV89PHkNdVX8
	 *            [AssociateTag] => mysite-20
	 *            [Brand] => bogner
	 *            [Operation] => ItemSearch
	 *            [ResponseGroup] => ItemAttributes,Images,OfferFull,BrowseNodes,EditorialReview,VariationSummary
	 *            [SearchIndex] => Apparel
	 *            [Service] => AWSECommerceService
	 *            [Timestamp] => 2017-04-04T20:42:43Z
	 *            [Version] => 2011-08-01
	 *        )
	 * @param null|array $defaults . Example:
	 *        Array (
	 *            [ResponseGroup] => ItemAttributes,Images,OfferFull,BrowseNodes,EditorialReview,VariationSummary
	 *            [SearchIndex] => All
	 *        )
	 *
	 * @return string URL for Amazon request.
	 */
	protected function _amazonUrl( $operation, $params, $defaults = null ) {

		$params = array_filter( $params );

		if ( ! is_null( $defaults ) ) {
			foreach ( $defaults as $k => $v ) {
				if ( ! isset( $params[ $k ] ) ) {
					$params[ $k ] = $v;
				}
			}
		}

		$params["Operation"]      = $operation;
		$params["Service"]        = "AWSECommerceService";
		$params["AWSAccessKeyId"] = $this->_awsAccessKeyId;
		$params["AssociateTag"]   = $this->_awsAssociateTag;
		$params["Version"]        = self::AWS_VERSION;
		$params["Timestamp"]      = gmdate( "Y-m-d\\TH:i:s\\Z" );

		ksort( $params );
		$query = array();
		foreach ( $params as $k => $v ) {
			if ( is_array( $v ) ) {
				$v = implode( ',', $v );
			}
			$query [] = $k . '=' . rawurlencode( $v );
		}
		$query = implode( '&', $query );
		$host  = $this->_hosts[ $this->_locale ];
		$path  = "/onca/xml";
		$subj  = sprintf( "GET\n%s\n%s\n%s", $host, $path, $query );
		$sign  = rawurlencode( base64_encode( hash_hmac( "sha256", $subj, $this->_awsSecretKey, true ) ) );

		return "http://{$host}{$path}?{$query}&Signature={$sign}";
	}
}

/**
 * Class DatafeedrAmazonSearchRequest
 *
 * Amazon search request class.
 */
class DatafeedrAmazonSearchRequest extends DatafeedrAmazonRequest {

	/**
	 * Add a parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Parameter name.
	 * @param string $value Parameter value.
	 *
	 * @return DatafeedrAmazonSearchRequest Returns $this.
	 *
	 * @see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
	 */
	public function addParam( $name, $value ) {
		$this->_params[ $name ] = $value;

		return $this;
	}

	/**
	 * Run search and return an array of products.
	 *
	 * IMPORTANT - The Amazon API returns a MAXIMUM of 10 products per API request and a maximum of
	 * 50 products per search query.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of products.
	 */
	public function execute() {

		$defaults = array(
			'ResponseGroup' => 'ItemAttributes,Images,OfferFull,BrowseNodes,EditorialReview,VariationSummary',
			'SearchIndex'   => 'All',
		);

		$url = $this->_amazonUrl( 'ItemSearch', $this->_params, $defaults );

		$this->_apiCall( 'amazon_search', array( 'url' => $url ) );

		return $this->_responseItem( 'products', array() );
	}
}

/**
 * Class DatafeedrAmazonLookupRequest
 *
 * Amazon lookup request.
 */
class DatafeedrAmazonLookupRequest extends DatafeedrAmazonRequest {

	/**
	 * Add a parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Parameter name - one of 'ASIN', 'SKU', 'UPC', 'EAN', 'ISBN'
	 * @param string|array $value Parameter value or an array of values (up to 10).
	 *
	 * @return DatafeedrAmazonLookupRequest Returns $this.
	 *
	 * @see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html
	 */
	public function addParam( $name, $value ) {
		$this->_params[ $name ] = $value;

		return $this;
	}

	/**
	 * Run search and return an array of products.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of products.
	 */
	public function execute() {

		$params = array_filter( $this->_params );
		$types  = array( 'ASIN', 'SKU', 'UPC', 'EAN', 'ISBN', 'asin', 'sku', 'upc', 'ean', 'isbn' );

		foreach ( $types as $type ) {
			if ( isset( $params[ $type ] ) ) {
				$params['IdType'] = strtoupper( $type );
				$params['ItemId'] = $params[ $type ];
				unset( $params[ $type ] );
			}
		}

		$defaults = array(
			'ResponseGroup' => 'ItemAttributes,Images,OfferFull,BrowseNodes,EditorialReview,VariationSummary',
		);

		if ( isset( $params['IdType'] ) && $params['IdType'] != 'ASIN' ) {
			$defaults['SearchIndex'] = 'All';
		}

		$url = $this->_amazonUrl( 'ItemLookup', $params, $defaults );
		$this->_apiCall( 'amazon_search', array( 'url' => $url ) );

		return $this->_responseItem( 'products', array() );
	}
}

/**
 * Class DatafeedrError.
 *
 * Generic Api error.
 */
class DatafeedrError extends Exception {
}

/**
 * Class DatafeedrBadRequestError.
 *
 * API error: Invalid Request.
 */
class DatafeedrBadRequestError extends DatafeedrError {
}

/**
 * Class DatafeedrAuthenticationError.
 *
 * API error: Authentication failed.
 */
class DatafeedrAuthenticationError extends DatafeedrError {
}

/**
 * Class DatafeedrLimitExceededError.
 *
 * API error: Query limit exceeded.
 */
class DatafeedrLimitExceededError extends DatafeedrError {
}

/**
 * Class DatafeedrHTTPError.
 *
 * API error: Unspecified HTTP error.
 */
class DatafeedrHTTPError extends DatafeedrError {
}

/**
 * Class DatafeedrConnectionError.
 *
 * API error: Connection error.
 */
class DatafeedrConnectionError extends DatafeedrError {
}

/**
 * Class DatafeedrQueryError.
 *
 * API error: Error in the search query.
 */
class DatafeedrQueryError extends DatafeedrError {
}

/**
 * Class DatafeedrExternalError.
 *
 * API error: External service error.
 */
class DatafeedrExternalError extends DatafeedrError {
}

/**
 * Class DatafeedrServerError.
 *
 * API error: Internal server error.
 */
class DatafeedrServerError extends DatafeedrError {
}