<?php

function dfrapi_api_get_status() {
	$api = dfrapi_api( dfrapi_get_transport_method() );
	try {
		$status = $api->getStatus();
		dfrapi_api_update_status( $api );
		return $status;
	} catch( Exception $err ) {
		return dfrapi_api_error( $err );
	}
	
}

/**
 * Removed configuration. Always returns 'wordpress'. 2017-02-21 10:23:10
 */
function dfrapi_get_transport_method() {
	return 'wordpress';
	// $configuration = (array) get_option( 'dfrapi_configuration' );
	// $transport = ( isset( $configuration['transport_method'] ) ) ? $configuration['transport_method'] : $transport;
	// return $transport;
}

/**
 * This instantiates the Datafeedr API Library and returns the $api object.
 */
function dfrapi_api( $transport='curl', $timeout=0, $returnObjects=FALSE ) {
	
	$configuration = (array) get_option( 'dfrapi_configuration' );

	if ( isset( $configuration['disable_api'] ) && ( $configuration['disable_api'] == 'yes' ) ) {
		$configuration['disable_api'] = 'no';
		update_option( 'dfrapi_configuration', $configuration );
	}

	$access_id = false;
	$secret_key = false;
	$transport = dfrapi_get_transport_method();
	
	if ( isset( $configuration['access_id'] ) && ( $configuration['access_id'] != '' ) ) {
		$access_id = $configuration['access_id'];
	}
	
	if ( isset( $configuration['secret_key'] ) && ( $configuration['secret_key'] != '' ) ) {
		$secret_key = $configuration['secret_key'];
	}		
		
	if ( $access_id && $secret_key ) {
		
		$options = array(
			'transport'     => 'wordpress',
			'timeout'       => 60,
			'returnObjects' => false,
			'retry'         => 0, // The number of retries if an API request times-out.
			'retryTimeout'  => 5, // The number of seconds to wait between retries.
		);

		$options = apply_filters( 'dfrapi_api_options', $options );

		$api = new DatafeedrApi( $access_id, $secret_key, $options );

		return $api;
		
	} else {
		return false;
	}
}

/**
 * Creates an associate array with the API's error details.
 */
function dfrapi_api_error( $error, $params=FALSE ) {
	
	// Change "request_count" to "max_requests" because sometimes there's
	// not even enough API requests left to update the Account info with 
	// the most update to date information.
	if ( $error->getCode() == 301 ) {
		$account = get_option( 'dfrapi_account', array() );
		$account['request_count'] = $account['max_requests'];
		update_option( 'dfrapi_account', $account );
	}

	return array( 
		'dfrapi_api_error' => array(
			'class' => get_class( $error ),
			'code' => $error->getCode(),
			'msg' => $error->getMessage(),
			'params' => $params,
		)
	);
}

/**
 * Creates the proper API request from the $query.
 */
function dfrapi_api_query_to_filters( $query, $useSelected=TRUE ) {
    $sform = new Dfrapi_SearchForm();
    return $sform->makeFilters( $query, $useSelected );
}

/**
 * Returns a parameter value from the $query array.
 */
function dfrapi_api_get_query_param( $query, $param ) {
	if ( is_array( $query ) && !empty( $query ) ) {
		foreach( $query as $k => $v ) {
			if ( $v['field'] == $param ) {
				return array(
					'field' 	=> @$v['field'],
					'operator' 	=> @$v['operator'],
					'value' 	=> @$v['value'],
				);
			}
		}
	}
	return false;
}

/**
 * This updates the "dfrapi_account" option with the most recent
 * API status information for this user.
 */
function dfrapi_api_update_status( &$api ) {
	if ( $status = $api->lastStatus() ) {
		$account = get_option( 'dfrapi_account', array() );
		$account['user_id'] 		= $status['user_id'];
		$account['plan_id'] 		= $status['plan_id'];
		$account['bill_day'] 		= $status['bill_day'];
		$account['max_total'] 		= $status['max_total'];
		$account['max_length'] 		= $status['max_length'];
		$account['max_requests'] 	= $status['max_requests'];
		$account['request_count'] 	= $status['request_count'];
		$account['network_count'] 	= $status['network_count'];
		$account['product_count'] 	= $status['product_count'];
		$account['merchant_count'] 	= $status['merchant_count'];
		update_option( 'dfrapi_account', $account );
	}
}

/**
 * This returns all affiliate networks' information. 
 * This accepts an array of source_ids (network ids)
 * to return a subset of networks.
 */
function dfrapi_api_get_all_networks( $nids=array() ) {
	$option_name = 'dfrapi_all_networks';
	$use_cache = wp_using_ext_object_cache( false );
	$networks = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );
	if ( false === $networks || empty ( $networks ) ) {
		$api = dfrapi_api( dfrapi_get_transport_method() );
		try {
			$networks = $api->getNetworks( $nids, TRUE );
			dfrapi_api_set_network_types( $networks );
			dfrapi_api_update_status( $api );
		} catch( Exception $err ) {
			return dfrapi_api_error( $err );
		}
		$use_cache = wp_using_ext_object_cache( false );
		set_transient( $option_name, $networks, DAY_IN_SECONDS );
		wp_using_ext_object_cache( $use_cache );
	}
	dfrapi_update_transient_whitelist( $option_name );
	return $networks;
}

/**
 * Returns a Zanox zmid value.
 */
function dfrapi_api_get_zanox_zmid( $merchant_id, $adspace_id ) {

	$option_name = 'zmid_' . $merchant_id . '_' . $adspace_id;
	$use_cache   = wp_using_ext_object_cache( false );
	$zmid        = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );

	if ( $zmid ) {
		return $zmid;
	}

	$keys = dfrapi_get_zanox_keys();
	$api  = dfrapi_api();

	try {
		$zmid = $api->getZanoxMerchantIds(
			$merchant_id,
			$adspace_id,
			$keys['connection_key']
		);
	} catch ( Exception $err ) {
		$zmid = 'dfrapi_unapproved_zanox_merchant';
	}

	$use_cache = wp_using_ext_object_cache( false );
	set_transient( $option_name, $zmid, WEEK_IN_SECONDS );
	wp_using_ext_object_cache( $use_cache );

	dfrapi_update_transient_whitelist( $option_name );

	return $zmid;
}

/**
 * Returns a Performance Horizon camref value.
 */
function dfrapi_api_get_ph_camref( $merchant_id ) {

	$option_name = 'camref_' . $merchant_id;
	$use_cache   = wp_using_ext_object_cache( false );
	$camref      = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );

	if ( $camref ) {
		return $camref;
	}

	$keys = dfrapi_get_ph_keys();
	$api  = dfrapi_api();

	try {
		$camref = $api->getPerformanceHorizonCamrefs(
			$merchant_id,
			$keys['application_key'],
			$keys['user_api_key'],
			$keys['publisher_id']
		);
	} catch ( Exception $err ) {
		$camref = 'dfrapi_unapproved_ph_merchant';
	}

	$use_cache = wp_using_ext_object_cache( false );
	set_transient( $option_name, $camref, WEEK_IN_SECONDS );
	wp_using_ext_object_cache( $use_cache );

	dfrapi_update_transient_whitelist( $option_name );

	return $camref;
}

/**
 * This creates 2 options in the options table each time the option 
 * "dfrapi_all_networks" is updated with new network information from the API.
 * 
 * - dfrapi_product_networks
 * - dfrapi_coupon_networks
 * 
 * These are just helper options to figure out if a network is a "product"
 * network or a "coupon" network.
 */
function dfrapi_api_set_network_types( $networks ) {
	$product_networks = array();
	$coupon_networks = array();
	foreach( $networks as $network ) {
		if ( $network['type'] == 'products' ) {
			$product_networks[$network['_id']] = $network;
		} elseif ( $network['type'] == 'coupons' ) {
			$coupon_networks[$network['_id']] = $network;
		}
	}
	update_option( 'dfrapi_product_networks', $product_networks );
	update_option( 'dfrapi_coupon_networks', $coupon_networks );
}

/**
 * This stores all merchants for a given source_id ($nid).
 * 
 * It is possible to pass "all" to this function however this creates
 * memory_limit errors when memory is set to less than 64MB.
 */
function dfrapi_api_get_all_merchants( $nid ) {
	$option_name = 'dfrapi_all_merchants_for_nid_' . $nid;
	$use_cache = wp_using_ext_object_cache( false );
	$merchants = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );
	if ( false === $merchants || empty ( $merchants ) ) {		
		$api = dfrapi_api( dfrapi_get_transport_method() );
		try {
			$merchants = $api->getMerchants( array( intval( $nid ) ), TRUE );
			dfrapi_api_update_status( $api );
		} catch( Exception $err ) {
			return dfrapi_api_error( $err );
		}
		$use_cache = wp_using_ext_object_cache( false );
		set_transient( $option_name, $merchants, DAY_IN_SECONDS );
		wp_using_ext_object_cache( $use_cache );
	}	
	dfrapi_update_transient_whitelist( $option_name );
	return $merchants;
}

/**
 * This retuns merchant or merchants' information by merchant_id or
 * an array of merchant IDs.
 */
function dfrapi_api_get_merchants_by_id( $ids, $includeEmpty=FALSE ) {
	$name = false;
	if ( is_array( $ids ) ) {
		sort( $ids, SORT_NUMERIC );
		$id_string = implode( ",", $ids );
		$name = md5( $id_string );
	} elseif ( $ids != '' ) {
		$name = trim( $ids );
	}
	if ( !$name ) { return; }
	$name = substr( $name, 0, 20 );
	$option_name = 'dfrapi_merchants_byid_' . $name;
	$use_cache = wp_using_ext_object_cache( false );
	$merchants = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );
	if ( false === $merchants || empty ( $merchants ) ) {
		$api = dfrapi_api( dfrapi_get_transport_method() );
		try {
			$merchants = $api->getMerchantsById( $ids, $includeEmpty );
			dfrapi_api_update_status( $api );
		} catch( Exception $err ) {
			return dfrapi_api_error( $err );
		}
		$use_cache = wp_using_ext_object_cache( false );
		set_transient( $option_name, $merchants, DAY_IN_SECONDS );
		wp_using_ext_object_cache( $use_cache );
	}	
	dfrapi_update_transient_whitelist( $option_name );
	return $merchants;
}

/**
 * Returns a $response array containing:
 * - ids: the query passed to the function.
 * - products: array of products.
 * - last_status: value of $api->lastStatus().
 * - found_count: value of $search->getFoundCount().
 * 
 * If the API throws an exception, that will return dfrapi_api_error( $err );
 * 
 * @param array $ids An array of product IDs.
 * @param int $ppp The number of products to return in 1 API request. Max is dictated by API, not plugin.
 * @param int $page The page number for returning products. This is used to figure the offset.
 */
function dfrapi_api_get_products_by_id( $ids, $ppp=20, $page=1 ) {

	$response = array();

	// Return false if no $ids or no $postid
	if ( empty( $ids ) ) { return $response; }
	
	// Make sure $page is a positive integer.
	$page = intval( abs( $page ) );
	
	// Make sure $ppp is a positive integer.
	$ppp = intval( abs( $ppp ) );
	
	// Make sure $ppp is not greater than "max_length".
	$account = (array) get_option( 'dfrapi_account' );
	if ( $ppp > $account['max_length'] ) {
		$ppp = $account['max_length'];
	}

	// The maximum number of results a request to the API can return.
	// Changing this will only break your site. It's not overridable.
	$max_total = $account['max_total'];
			
	// Determine offset.
	$offset = ( ( $page - 1 ) * $ppp );
	
	// Make sure $limit doesn't go over 10,000.
	if ( ( $offset + $ppp ) > $max_total ) { 
		$ppp = ( $max_total - $offset );
	}
	
	// If $ppp is negative, return empty array();
	if ( $ppp < 1 ) {
		return array();
	}
	
	// If offset is greater than 10,000 return empty array();
	if  ( $offset >= ( $max_total - $ppp ) ) {
		return array();
	}
	
	try {

		// Initialize API.
		$api = dfrapi_api( dfrapi_get_transport_method() );
		if ( ! $api ) {
			return $response;
		}

		// Get a range of product IDs to query.
		$id_range = array_slice( $ids, $offset, $ppp );

		// Return immediately if $id_range is empty.
		if ( empty( $id_range ) ) {
			$response['ids']         = array();
			$response['products']    = array();
			$response['last_status'] = $api->lastStatus();
			$response['found_count'] = 0;

			return $response;
		}

		// Begin query
		$search = $api->searchRequest();

		// Get filters
		$filters = dfrapi_api_query_to_filters( array() );
		if ( isset( $filters['error'] ) ) {
			throw new DatafeedrError( $filters['error'], 0 );
		}

		// Loop thru filters.
		foreach ( $filters as $filter ) {
			$search->addFilter( $filter );
		}

		$search->addFilter( 'id IN ' . implode( ",", $id_range ) );
		$search->setLimit( $ppp );
		$products = $search->execute();

		// Keep track of IDs which were returned via the API to compare with $id_range (unreturned)
		$included_ids = array();
		if ( ! empty( $products ) ) {
			foreach ( $products as $product ) {
				$included_ids[] = $product['_id'];
			}
		}

		// Excluded product IDs.
		$excluded_ids = array_diff( $id_range, $included_ids );

		// Add "message" values to excluded IDs if there are some.
		$excluded_products = array();
		if ( ! empty( $included_ids ) && ! empty( $excluded_ids ) ) {
			foreach ( $excluded_ids as $excluded_id ) {

				$wc_url = add_query_arg(
					array(
						's'           => $excluded_id,
						'post_status' => 'trash',
						'post_type'   => 'product',
					),
					admin_url( 'edit.php' )
				);

				// Do not add a 'url' field to this array or the unavailable product WILL be imported.
				// See /datafeedr-product-sets/classes/class-dfrps-update.php:73
				$excluded_products[] = array(
					'_id'         => $excluded_id,
					'_wc_url'     => $wc_url,
					'name'        => $excluded_id . ' - ' . __( 'Unavailable', DFRAPI_DOMAIN ),
					'price'       => 0,
					'finalprice'  => 0,
					'description' => __( 'This product is either temporarily or permanently unavailable.', DFRAPI_DOMAIN ),
					'image'       => DFRAPI_URL . 'images/icons/noimage.png',
					'merchant'    => 'n/a',
					'source'      => 'n/a',
				);
			}
		}

		// Update API status
		dfrapi_api_update_status( $api );

		// Build $response array().
		$response['ids']         = $ids;
		$response['products']    = array_merge( $products, $excluded_products );
		$response['last_status'] = $api->lastStatus();
		$response['found_count'] = count( $ids );
		$response['params']      = $search->getParams();

		// Return it!
		return $response;
	
	} catch( Exception $err ) {	
		return dfrapi_api_error( $err );
	}
}

/**
 * Returns a $response array containing:
 * - query: the query passed to the function.
 * - excluded: ids of excluded products.
 * - products: array of products.
 * - last_status: value of $api->lastStatus().
 * - found_count: value of $search->getFoundCount().
 * - params: value of $search->getParams().
 * 
 * Example of $query array():
 * 
 * 
 *  $query[] = array(
 * 		'value' => 'shoes',
 *		'field' => 'any',
 *		'operator' => 'contain'	
 *  );
 *
 *  $query[] = array(
 *		'value' => 'image',
 *		'field' => 'duplicates',
 *		'operator' => 'is'	
 *  );
 *
 *  $query[] = array(
 *		'field' => 'sort',
 *		'operator' => '+saleprice'	
 *  );
 * 
 * 
 * If the API throws an exception, that will return dfrapi_api_error( $err, $params );
 * 
 * @param array $query The complete query to pass to the API.
 * @param int $ppp The number of products to return in 1 API request. Max is dictated by API, not plugin.
 * @param int $page The page number for returning products. This is used to figure the offset.
 * @param array $excluded An array of product IDs to exclude from being returned.
 */
function dfrapi_api_get_products_by_query( $query, $ppp=20, $page=1, $excluded=array() ) {

	$response = array();

	// Return false if no $query.
	if ( empty( $query ) ) { return $response; }
	
	// Make sure $page is a positive integer.
	$page = intval( abs( $page ) );
	
	// Make sure $ppp is a positive integer.
	$ppp = intval( abs( $ppp ) );
	
	// Make sure $ppp is not greater than "max_length".
	$account = (array) get_option( 'dfrapi_account' );
	if ( $ppp > $account['max_length'] ) {
		$ppp = $account['max_length'];
	}

	// The maximum number of results a request to the API can return.
	// Changing this will only break your site. It's not overridable.
	$max_total = $account['max_total'];

	// Detemine query limit (if exists).
	$query_limit = dfrapi_api_get_query_param( $query, 'limit' );
	$query_limit = ( $query_limit ) 
		? $query_limit['value']
		: false;
			
	// No query shall try to return more than 10,000 products.
	if ( $query_limit && ( $query_limit > $max_total ) ) {
		$query_limit = $max_total;
	}
			
	// Determine offset.
	$offset = ( ( $page - 1 ) * $ppp );
	
	// If offset is greater than 10,000 return empty array();
	if  ( $offset >= $max_total ) {
		return array();
	}
	
	// Factor in query limit 
	if ( $query_limit ) {
		if ( ( $ppp + $offset ) > $query_limit ) {
			$ppp = ( $query_limit - $offset );
		}
	}
	
	// Make sure $limit doesn't go over 10,000.
	if ( ( $offset + $ppp ) > $max_total ) { 
		$ppp = ( $max_total - $offset );
	}
	
	// If $ppp is negative, return empty array();
	if ( $ppp < 1 ) {
		return $response;
	}
	
	try {
				
		// Initialize API.
		$api = dfrapi_api( dfrapi_get_transport_method() );
		if ( !$api ) { return $response; }
		
		$search = $api->searchRequest();
	
		// Get filters
		$filters = dfrapi_api_query_to_filters( $query );
        if(isset($filters['error'])) {
            throw new DatafeedrError($filters['error'], 0);
        }

		// Loop thru filters.
		foreach ( $filters as $filter ) {
			$search->addFilter( $filter );
		}
				
		// Exclude duplicates.
		$duplicates = dfrapi_api_get_query_param( $query, 'duplicates' );
		if ( $duplicates ) {
			$excludes = $duplicates['value'];
			$search->excludeDuplicates( $excludes );
		}
	
		// Exclude blocked products.
		$excluded = (array) $excluded;
		if ( !empty( $excluded ) ) {
			$search->addFilter('id !IN ' . implode( ",", $excluded ) );
		}
		
		// Sort products.
		$sort = dfrapi_api_get_query_param( $query, 'sort' );
		if( $sort && strlen( $sort['operator'] ) ) {
			$search->addSort( $sort['operator'] );
		}
			
		// Set limits and offset.	
		$search->setLimit( $ppp );	
		$search->setOffset( $offset );
				
		// Execute query.
		$products = $search->execute();
			
		// Update API status
		dfrapi_api_update_status( $api );
	
		// Build $response array().
		$response['query'] 			= $query;
		$response['excluded'] 		= $excluded;
		$response['products'] 		= $products;
		$response['last_status'] 	= $api->lastStatus();
		//$response['found_count'] 	= $search->getFoundCount(); Old, returned wrong value (#8672)
		$response['found_count'] 	= $search->getResultCount();
		$response['params'] 		= $search->getParams();
	
		// Return it!
		return $response;
	
	} catch( Exception $err ) {
		$params = $search->getParams();
		return dfrapi_api_error( $err, $params );
	
	}
}
