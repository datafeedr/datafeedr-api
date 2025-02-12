<?php

defined( 'ABSPATH' ) || exit;

/**
 * Returns true if the user's Datafeedr API keys exist or false if they do not.
 *
 * @return bool
 */
function dfrapi_datafeedr_api_keys_exist(): bool {
	return dfrapi_get_datafeedr_access_id() && dfrapi_get_datafeedr_secret_key();
}

/**
 * Returns the user's Datafeedr API Access ID or false if it does not exist.
 *
 * @return false|string
 */
function dfrapi_get_datafeedr_access_id() {

	$configuration = (array) get_option( 'dfrapi_configuration', [] );

	if ( ! isset( $configuration['access_id'] ) ) {
		return false;
	}

	$access_id = trim( $configuration['access_id'] );

	return ! empty( $access_id ) ? $access_id : false;
}

/**
 * Returns the user's Datafeedr API Secret Key or false if it does not exist.
 *
 * @return false|string
 */
function dfrapi_get_datafeedr_secret_key() {

	$configuration = (array) get_option( 'dfrapi_configuration', [] );

	if ( ! isset( $configuration['secret_key'] ) ) {
		return false;
	}

	$secret_key = trim( $configuration['secret_key'] );

	return ! empty( $secret_key ) ? $secret_key : false;
}

/**
 * Returns the user's Datafeedr API Version or the default.
 *
 * @return string
 */
function dfrapi_get_datafeedr_api_version(): string {

	$configuration = (array) get_option( 'dfrapi_configuration', [] );

	if ( ! isset( $configuration['api_version'] ) ) {
		return dfrapi_get_default_api_version();
	}

	$api_version = trim( $configuration['api_version'] );

	return in_array( $api_version, dfrapi_get_valid_api_versions(), true )
		? $api_version
		: dfrapi_get_default_api_version();
}

/**
 * Returns the valid API versions.
 *
 * @return array
 */
function dfrapi_get_valid_api_versions(): array {
	return [ 'stable' ];
}

/**
 * Returns the default API version.
 *
 * @return string
 */
function dfrapi_get_default_api_version(): string {
	return 'stable';
}

/**
 * Returns the user's API requests usage as a percentage of their total requests allowed.
 *
 * @return float|int
 */
function dfrapi_get_api_usage_percentage() {
	_deprecated_function( __FUNCTION__, '1.3.0', 'dfrapi_get_api_usage_as_percentage()' );

	return dfrapi_get_api_usage_as_percentage();
}

/**
 * Returns true if the user has used more than 90% of their API requests. Otherwise returns false.
 *
 * @return bool
 */
function dfrapi_api_usage_over_90_percent(): bool {
	return dfrapi_get_api_usage_as_percentage() >= 90;
}

/**
 * Helper method for setting emails as HTML.
 *
 * @return string
 */
function dfrapi_set_html_content_type(): string {
	return 'text/html';
}

/**
 * Get Zanox Keys.
 *
 * @return array|bool Array of keys or false if they do not exist.
 */
function dfrapi_get_zanox_keys() {

	$configuration = (array) get_option( 'dfrapi_configuration' );

	$zanox_connection_key = false;
	$zanox_secret_key     = false;

	if ( isset( $configuration['zanox_connection_key'] ) && ( $configuration['zanox_connection_key'] != '' ) ) {
		$zanox_connection_key = $configuration['zanox_connection_key'];
	}

	if ( isset( $configuration['zanox_secret_key'] ) && ( $configuration['zanox_secret_key'] != '' ) ) {
		$zanox_secret_key = $configuration['zanox_secret_key'];
	}

	if ( $zanox_connection_key && $zanox_secret_key ) {
		return array(
			'connection_key' => $zanox_connection_key,
			'secret_key'     => $zanox_secret_key,
		);
	}

	return false;
}

/**
 * Get Partnerize Keys.
 *
 * @since 1.0.66
 *
 * @return array|bool Array of keys or false if they do not exist.
 */
function dfrapi_get_ph_keys() {

	$configuration = (array) get_option( 'dfrapi_configuration' );

	$ph_application_key = false;
	$ph_user_api_key    = false;
	$ph_publisher_id    = false;

	if ( isset( $configuration['ph_application_key'] ) && ( $configuration['ph_application_key'] != '' ) ) {
		$ph_application_key = $configuration['ph_application_key'];
	}

	if ( isset( $configuration['ph_user_api_key'] ) && ( $configuration['ph_user_api_key'] != '' ) ) {
		$ph_user_api_key = $configuration['ph_user_api_key'];
	}

	if ( isset( $configuration['ph_publisher_id'] ) && ( $configuration['ph_publisher_id'] != '' ) ) {
		$ph_publisher_id = $configuration['ph_publisher_id'];
	}

	if ( $ph_application_key && $ph_user_api_key && $ph_publisher_id ) {
		return array(
			'application_key' => $ph_application_key,
			'user_api_key'    => $ph_user_api_key,
			'publisher_id'    => $ph_publisher_id,
		);
	}

	return false;
}

/**
 * Get Effiliation Keys.
 *
 * @since 1.0.81
 *
 * @return array|bool Array of keys or false if they do not exist.
 */
function dfrapi_get_effiliation_keys() {

	$configuration = (array) get_option( 'dfrapi_configuration' );

	$effiliation_key = false;

	if ( isset( $configuration['effiliation_key'] ) && ( $configuration['effiliation_key'] != '' ) ) {
		$effiliation_key = $configuration['effiliation_key'];
	}

	if ( $effiliation_key ) {
		return array(
			'effiliation_key' => $effiliation_key,
		);
	}

	return false;
}

/**
 * Returns Amazon API key credentials if they exist.
 *
 * If the Amazon API keys such as the Access Key ID, Secret Access Key, Tracking ID and Locale all
 * exists, then this function returns them in array format. Otherwise it returns false.
 *
 * @since 1.0.33
 *
 * @return array|bool Returns array of values if all values exist, otherwise false.
 */
function dfrapi_get_amazon_keys() {

	$configuration = (array) get_option( 'dfrapi_configuration' );

	$amazon_access_key_id     = false;
	$amazon_secret_access_key = false;
	$amazon_tracking_id       = false;
	$amazon_locale            = false;

	if ( isset( $configuration['amazon_access_key_id'] ) && ( $configuration['amazon_access_key_id'] != '' ) ) {
		$amazon_access_key_id = $configuration['amazon_access_key_id'];
	}

	if ( isset( $configuration['amazon_secret_access_key'] ) && ( $configuration['amazon_secret_access_key'] != '' ) ) {
		$amazon_secret_access_key = $configuration['amazon_secret_access_key'];
	}

	if ( isset( $configuration['amazon_tracking_id'] ) && ( $configuration['amazon_tracking_id'] != '' ) ) {
		$amazon_tracking_id = $configuration['amazon_tracking_id'];
	}

	if ( isset( $configuration['amazon_locale'] ) && ( $configuration['amazon_locale'] != '' ) ) {
		$amazon_locale = $configuration['amazon_locale'];
	}

	if ( $amazon_access_key_id && $amazon_secret_access_key && $amazon_tracking_id && $amazon_locale ) {
		return array(
			'amazon_access_key_id'     => $amazon_access_key_id,
			'amazon_secret_access_key' => $amazon_secret_access_key,
			'amazon_tracking_id'       => $amazon_tracking_id,
			'amazon_locale'            => $amazon_locale,
		);
	}

	return false;
}

/**
 * Returns a link to a user page on v4.datafeedr.com.
 */
function dfrapi_user_pages( $page ) {

	$pages = array(
		'edit'     => 'https://datafeedr.me/dashboard',
		'invoices' => 'https://datafeedr.me/dashboard',
		'billing'  => 'https://datafeedr.me/dashboard',
		'cancel'   => 'https://datafeedr.me/dashboard',
		'change'   => 'https://datafeedr.me/dashboard',
		'signup'   => 'https://datafeedr.me/dashboard',
		'summary'  => 'https://datafeedr.me/dashboard',
		'api'      => 'https://datafeedr.me/dashboard',
		'factory'  => 'https://datafeedr.me/dashboard',
	);

	return $pages[ $page ];
}

/**
 * Adds option name to transient whitelist. This is so we know
 * all transient options that can be deleted when deleting the
 * API cache on Tools page.
 */
function dfrapi_update_transient_whitelist( $option_name ) {
	$whitelist   = (array) get_option( 'dfrapi_transient_whitelist', [] );
	$whitelist[] = $option_name;
	update_option( 'dfrapi_transient_whitelist', array_unique( $whitelist ) );
}

/**
 * Add affiliate ID and tracking ID to an affiliate link.
 *
 * @param array $product An array of a single Datafeedr $product.
 *
 * @return string A URL with affiliate ID inserted or an empty string if the affiliate ID is missing.
 */
function dfrapi_url( $product ) {

	// Get all the user's selected networks.
	$networks = (array) get_option( 'dfrapi_networks' );

	// Support added for Amazon in version 1.0.60 (2017-10-18) Ticket #15201
	if ( substr( $product['source'], 0, 6 ) === "Amazon" ) {
		// Get the user's Amazon Associate Tag
		$affiliate_id = dfrapi_get_amazon_associate_tag();
	} else {
		// Extract the affiliate ID from the $networks array.
		$affiliate_id = isset( $networks['ids'][ $product['source_id'] ]['aid'] ) ? $networks['ids'][ $product['source_id'] ]['aid'] : '';
	}

	$affiliate_id = apply_filters( 'dfrapi_affiliate_id', $affiliate_id, $product, $networks );
	$affiliate_id = trim( $affiliate_id );

	// Extract the Tracking ID from the $networks array.
	$tracking_id = ( isset( $networks['ids'][ $product['source_id'] ]['tid'] ) ) ? $networks['ids'][ $product['source_id'] ]['tid'] : '';
	$tracking_id = apply_filters( 'dfrapi_tracking_id', $tracking_id, $product, $networks );
	$tracking_id = trim( $tracking_id );

	// Affiliate ID is missing.  Do action and return empty string.
	if ( $affiliate_id == '' ) {
		do_action( 'dfrapi_affiliate_id_is_missing', $product );

		return '';
	}

	// Determine which URL field to get: 'url' OR 'ref_url'. Return 'url' if $tracking_id is empty, otherwise, use 'ref_url'.
	$url = ( $tracking_id !== '' && isset( $product['ref_url'] ) ) ? $product['ref_url'] : $product['url'];

	// Apply filters to URL before affiliate & tracking ID insertion.
	$url = apply_filters( 'dfrapi_before_affiliate_id_insertion', $url, $product, $affiliate_id );
	$url = apply_filters( 'dfrapi_before_tracking_id_insertion', $url, $product, $tracking_id );

	// Replace placeholders in URL.
	$placeholders = array( "@@@", "###" );
	$replacements = array( $affiliate_id, $tracking_id );
	$url          = str_replace( $placeholders, $replacements, $url );

	// Apply filters to URL after affiliate & tracking ID insertion.
	$url = apply_filters( 'dfrapi_after_affiliate_id_insertion', $url, $product, $affiliate_id );
	$url = apply_filters( 'dfrapi_after_tracking_id_insertion', $url, $product, $tracking_id );

	// Return URL
	return $url;
}

/**
 * Return Amazon Associate Tag (ie. Tracking ID).
 *
 * @since 1.0.60
 *
 * @return string Associate Tag or empty string if it does not exist.
 */
function dfrapi_get_amazon_associate_tag() {
	$config = get_option( 'dfrapi_configuration' );

	return ( isset( $config['amazon_tracking_id'] ) ) ? $config['amazon_tracking_id'] : '';
}

/**
 * Add affiliate ID to impression URL.
 *
 * Since 1.0.39
 *
 * @param $product - An array of a single's product's information.
 */
function dfrapi_impression_url( $product ) {

	$impression_url = ( isset( $product['impressionurl'] ) ) ? trim( $product['impressionurl'] ) : false;

	if ( ! $impression_url ) {
		return '';
	}

	// Get all the user's selected networks.
	$networks = (array) get_option( 'dfrapi_networks' );

	// Extract the affiliate ID from the $networks array.
	$affiliate_id = $networks['ids'][ $product['source_id'] ]['aid'];
	$affiliate_id = apply_filters( 'dfrapi_affiliate_id', $affiliate_id, $product, $networks );
	$affiliate_id = trim( $affiliate_id );

	// Affiliate ID is missing.  Do action and return empty string.
	if ( $affiliate_id == '' ) {
		do_action( 'dfrapi_affiliate_id_is_missing_impression', $product );

		return '';
	}

	// Apply filters to URL before affiliate & tracking ID insertion.
	$impression_url = apply_filters( 'dfrapi_before_affiliate_id_insertion_impression', $impression_url, $product, $affiliate_id );

	// Replace placeholders in URL.
	$placeholders   = array( "@@@" );
	$replacements   = array( $affiliate_id );
	$impression_url = str_replace( $placeholders, $replacements, $impression_url );

	// Apply filters to URL after affiliate & tracking ID insertion.
	$impression_url = apply_filters( 'dfrapi_after_affiliate_id_insertion_impression', $impression_url, $product, $affiliate_id );

	// Return URL
	return $impression_url;
}

/**
 * Output an error message generated by the API.
 */
function dfrapi_output_api_error( $data ) {
	$error  = @$data['dfrapi_api_error'];
	$params = @$data['dfrapi_api_error']['params'];
	?>
	<div class="dfrapi_api_error">
		<div class="dfrapi_head"><?php _e( 'Datafeedr API Error', 'datafeedr-api' ); ?></div>
		<div class="dfrapi_msg">
			<strong><?php _e( 'Message:', 'datafeedr-api' ); ?></strong> <?php echo $error['msg']; ?>
		</div>
		<div class="dfrapi_code"><strong><?php _e( 'Code:', 'datafeedr-api' ); ?></strong> <?php echo $error['code']; ?>
		</div>
		<div class="dfrapi_class">
			<strong><?php _e( 'Class:', 'datafeedr-api' ); ?></strong> <?php echo $error['class']; ?></div>
		<?php if ( is_array( $params ) ) : ?>
			<div class="dfrps_query"><strong><?php _e( 'Query:', 'datafeedr-api' ); ?></strong>
				<span><?php echo dfrapi_display_api_request( $params ); ?></span></div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Convert a currency code to sign. USD => $
 *
 * @https://github.com/pelle/bux/blob/master/src/bux/currencies.clj
 *
 * Currently supported currencies:
 *
 * AUD    Australia    &#36;
 * BRL    Brazil    R$
 * CAD    Canada    &#36;
 * CHF    Switzerland    Fr
 * DKK    Denmark    kr
 * EUR    Belgium    &euro;
 * EUR    Finland    &euro;
 * EUR    France    &euro;
 * EUR    Germany    &euro;
 * EUR    Ireland    &euro;
 * EUR    Italy    &euro;
 * EUR    Netherlands    &euro;
 * EUR    Spain    &euro;
 * GBP    United Kingdom    &pound;
 * HUF    Hungary    Fr
 * INR    India    &#8377;
 * MYR    Malaysia    RM
 * NOK    Norway    kr
 * NZD    New Zealand    &#36;
 * PHP    Philippines  &#8369;
 * PLN    Poland    zł
 * RON    Romania L
 * RUB    Russia    ₽
 * SEK    Sweden    kr
 * TRY    Turkey    &#8356;
 * USD    United States    &#36;
 *
 * @param string $code 3-character ISO 4217 currency code.
 *
 * @return mixed|string
 */
function dfrapi_currency_code_to_sign( $code ) {
	return dfrapi_currency( $code )->get_currency_symbol();
}

/**
 * This displays the API request in PHP format.
 */
function dfrapi_display_api_request( $params = array() ) {

	$html = '';

	if ( empty( $params ) ) {
		return $html;
	}

	$html .= '$search = $api->searchRequest();<br />';
	foreach ( $params as $k => $v ) {

		// Handle query.
		if ( $k === 'query' ) {
			foreach ( $v as $query ) {
				if ( substr( $query, 0, 9 ) !== 'source_id' || substr( $query, 0, 11 ) !== 'merchant_id' ) {
					$query = str_replace( ",", ", ", $query );
				}
				$html .= '$search->addFilter( \'' . ( $query ) . '\' );<br />';
			}
		}

		// Handle sort.
		if ( $k === 'sort' ) {
			foreach ( $v as $sort ) {
				$html .= '$search->addSort( \'' . stripslashes( $sort ) . '\' );<br />';
			}
		}

		// Handle limit.
		if ( $k === 'limit' ) {
			$html .= '$search->setLimit( \'' . stripslashes( $v ) . '\' );<br />';
		}

		// Handle merchant_limit.
		if ( $k === 'merchant_limit' ) {
			$html .= '$search->setMerchantLimit( \'' . stripslashes( absint( $v ) ) . '\' );<br />';
		}

		// Handle Offset.
		if ( $k === 'offset' ) {
			$html .= '$search->setOffset( \'' . stripslashes( $v ) . '\' );<br />';
		}

		// Handle Exclude duplicates.
		if ( $k === 'exclude_duplicates' ) {
			$html .= '$search->excludeDuplicates( \'' . $v . '\' );<br />';
		}
	}

	$html .= '$products = $search->execute();';

	return $html;

}

function dfrapi_get_query_param( $query, $param ) {
	if ( is_array( $query ) && ! empty( $query ) ) {
		foreach ( $query as $k => $v ) {
			if ( $v['field'] == $param ) {
				return array(
					'field'    => @$v['field'],
					'operator' => @$v['operator'],
					'value'    => @$v['value'],
				);
			}
		}
	}

	return false;
}

/**
 * Converts a value in cents into a value with proper
 * decimal placement.
 *
 * Example: 14999 => 149.99
 */
function dfrapi_int_to_price( $price ) {
	return number_format( ( $price / 100 ), 2 );
}

/**
 * Converts decimal or none decimal prices into values in cents.
 *
 * assert(dfrapi_price_to_int('123')           ==12300);
 * assert(dfrapi_price_to_int('123.4')         ==12340);
 * assert(dfrapi_price_to_int('1234.56')       ==123456);
 * assert(dfrapi_price_to_int('123,4')         ==12340);
 * assert(dfrapi_price_to_int('1234,56')       ==123456);
 * assert(dfrapi_price_to_int('1,234,567')     ==123456700);
 * assert(dfrapi_price_to_int('1,234,567.8')   ==123456780);
 * assert(dfrapi_price_to_int('1,234,567.89')  ==123456789);
 * assert(dfrapi_price_to_int('1.234.567')     ==123456700);
 * assert(dfrapi_price_to_int('1.234.567,8')   ==123456780);
 * assert(dfrapi_price_to_int('1.234.567,89')  ==123456789);
 * assert(dfrapi_price_to_int('FOO 123 BAR')   ==12300);
 */
function dfrapi_price_to_int( $price ) {
	$d = $price;
	$d = preg_replace( '~^[^\d.,]+~', '', $d );
	$d = preg_replace( '~[^\d.,]+$~', '', $d );

	// 123 => 12300
	if ( preg_match( '~^(\d+)$~', $d, $m ) ) {
		return intval( $m[1] . '00' );
	}

	// 123.4 => 12340, 123,45 => 12345
	if ( preg_match( '~^(\d+)[.,](\d{1,2})$~', $d, $m ) ) {
		return intval( $m[1] . substr( $m[2] . '0000', 0, 2 ) );
	}

	// 1,234,567.89 => 123456789
	if ( preg_match( '~^((?:\d{1,3})(?:,\d{3})*)(\.\d{1,2})?$~', $d, $m ) ) {
		$f = isset( $m[2] ) ? $m[2] : '.';

		return intval( str_replace( ',', '', $m[1] ) . substr( $f . '0000', 1, 2 ) );
	}

	// 1.234.567,89 => 123456789
	if ( preg_match( '~^((?:\d{1,3})(?:\.\d{3})*)(,\d{1,2})?$~', $d, $m ) ) {
		$f = isset( $m[2] ) ? $m[2] : '.';

		return intval( str_replace( '.', '', $m[1] ) . substr( $f . '0000', 1, 2 ) );
	}

	return null;
}

function dfrapi_html_output_api_error( $data ) {
	$error  = $data['dfrapi_api_error'];
	$params = @$data['dfrapi_api_error']['params'];
	?>
	<div class="dfrapi_api_error">
		<div class="dfrapi_head"><?php _e( 'Datafeedr API Error', 'datafeedr-api' ); ?></div>
		<div class="dfrapi_msg">
			<strong><?php _e( 'Message:', 'datafeedr-api' ); ?></strong> <?php echo $error['msg']; ?>
		</div>
		<div class="dfrapi_code"><strong><?php _e( 'Code:', 'datafeedr-api' ); ?></strong> <?php echo $error['code']; ?>
		</div>
		<div class="dfrapi_class">
			<strong><?php _e( 'Class:', 'datafeedr-api' ); ?></strong> <?php echo $error['class']; ?></div>
		<?php if ( is_array( $params ) ) : ?>
			<div class="dfrapi_query"><strong><?php _e( 'Query:', 'datafeedr-api' ); ?></strong>
				<span><?php echo dfrapi_helper_display_api_request( $params ); ?></span></div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Returns the total number of products in the Datafeedr database (pulled from dfrapi_account option).
 *
 * @param bool $formatted
 * @param mixed $default
 *
 * @return int|string
 */
function dfrapi_get_total_products_in_db( $formatted = true, $default = 0 ) {
	$account = (array) get_option( 'dfrapi_account', [] );
	$count   = absint( $account['product_count'] ?? $default );

	return $formatted ? number_format_i18n( $count ) : $count;
}

/**
 * Imports an image from a URL into the WordPress Media Library.
 *
 * @since 1.2.2 Will return either the Attachment ID or WP_Error if there was an error importing the image.
 *
 * @since 1.0.71
 *
 * @param string $url Image URL.
 * @param array $args Optional. An array of options.
 *
 * $args = array(
 *
 *      This is the ID of the post we want to attach this image to. If we do not
 *      want this image to be attached to a post, leave this set to 0.
 *      'post_id' => 0,
 *
 *      This is name of the file name the image will have once it is stored on
 *      on the server in the WordPress uploads directory.
 *      'file_name' => '',
 *
 *      This is the ID of the User this image will be associated with.
 *      'user_id' => 0,
 *
 *      This is the title of the image (which is different than the file name).
 *      'title' => '',
 *
 *      The description of the image.
 *      'description' => '',
 *
 *      The caption for the image.
 *      'caption' => '',
 *
 *      The alt text. Text to display if image cannot be loaded.
 *      'alt_text' => '',
 *
 *      Whether this image should be set as the post's thumbnail. If the post_id is 0, this setting will be ignored.
 *      'is_post_thumbnail' => false,
 *
 *      The number of seconds to spend attempting to download the image.
 *      'timeout' => 5
 *
 *      Sets the image's owner and source. _owner_datafeedr : dfrapi
 *      '_source_plugin' => 'dfrapi'
 * );
 *
 * @return Datafeedr_Image_Importer|int|WP_Error
 */
function datafeedr_import_image( $url, $args = [] ) {

	if ( dfrapi_use_legacy_image_importer() ) {
		return ( new Datafeedr_Image_Importer( $url, $args ) )->import();
	}

	$default_args = [
		'title'             => '',
		'file_name'         => '',
		'description'       => '',
		'caption'           => '',
		'alt_text'          => '',
		'user_id'           => 0,
		'post_id'           => 0,
		'is_post_thumbnail' => true,
		'timeout'           => 5,
		'_source_plugin'    => 'dfrapi',
	];

	$args = array_merge( $default_args, $args );

	$image_data = dfrapi_image_data( $url );

	$image_data->set_title( $args['title'] );
	$image_data->set_filename( $args['file_name'] );
	$image_data->set_description( $args['description'] );
	$image_data->set_caption( $args['caption'] );
	$image_data->set_alternative_text( $args['alt_text'] );
	$image_data->set_author_id( absint( $args['user_id'] ) );
	$image_data->set_post_parent_id( absint( $args['post_id'] ) );
	$image_data->set_post_thumbnail( (bool) $args['is_post_thumbnail'] );

	$image_data = apply_filters( 'datafeedr_import_image_image_data', $image_data, $url, $args );

	$uploader = dfrapi_image_uploader( $image_data );

	$uploader->set_timeout( absint( $args['timeout'] ) );

	$attachment_id = $uploader->upload();

	if ( ! is_wp_error( $attachment_id ) ) {
		update_post_meta( $attachment_id, '_owner_datafeedr', sanitize_text_field( $args['_source_plugin'] ) );
	}

	do_action( 'datafeedr_import_image_attachment_id', $attachment_id, $image_data, $url, $args );

	return $attachment_id;
}

/**
 * Returns true if the $string starts with one of the $patterns. Otherwise returns false.
 *
 * @since 1.0.78
 *
 * @param string|array $patterns The patterns to search for in the beginning of the $string.
 *
 * @param string $string The haystack.
 *
 * @return bool True if string starts with the pattern(s) else returns false.
 */
function dfrapi_string_starts_with( $string, $patterns ) {
	$patterns = ( is_string( $patterns ) ) ? array( $patterns ) : $patterns;
	foreach ( $patterns as $pattern ) {
		$length = mb_strlen( $pattern );
		if ( mb_substr( $string, 0, $length ) === $pattern ) {
			return true;
		}
	}

	return false;
}

/**
 * Returns true if we are viewing a Datafeedr-specific page in the WordPress Admin Area.
 *
 * @since 1.0.84
 *
 * @return bool
 * @global $pagenow
 *
 */
function dfrapi_is_datafeedr_admin_page() {

	/**
	 * For post edit pages (ie. post.php?post=1&action=edit).
	 */
	$post_types = [
		'datafeedr-productset',
	];

	/**
	 * For $_GET params (ie. admin.php?page=dfrps_configuration).
	 */
	$params = [
		'page'      => [
			'dfrapi',
			'dfrapi_networks',
			'dfrapi_merchants',
			'dfrapi_tools',
			'dfrapi_export',
			'dfrapi_import',
			'dfrapi_account',
			'dfrcs_options',
			'dfrps_configuration',
			'dfrps_tools',
			'dfrpswc_options',
		],
		'post_type' => [
			'datafeedr-productset'
		]
	];

	foreach ( $params as $k => $v ) {
		if ( isset( $_GET[ $k ] ) && in_array( $_GET[ $k ], $v ) ) {
			return true;
		}
	}

	global $pagenow;

	if ( 'post.php' === $pagenow && in_array( get_post_type(), $post_types ) ) {
		return true;
	}

	return false;
}

/**
 * @param integer $network_id
 * @param string $id_type
 *
 * @return WP_Error|string
 */
function dfrapi_get_affiliate_and_tracking_id( $network_id, $id_type = 'aid' ) {

	static $networks = null;

	$key  = 'ids';
	$type = ( 'tid' === $id_type ) ? 'tid' : 'aid';

	if ( null === $networks ) {
		$networks = get_option( 'dfrapi_networks', [] );
	}

	if ( empty( $networks ) ) {
		return new WP_Error(
			'dfrapi_get_affiliate_id_no_networks',
			__( 'No networks selected.', 'datafeedr-api' )
		);
	}

	if ( ! isset( $networks[ $key ] ) ) {
		return new WP_Error(
			'dfrapi_get_affiliate_id_no_network_ids',
			__( 'No network IDs selected.', 'datafeedr-api' )
		);
	}

	if ( ! isset( $networks[ $key ][ $network_id ] ) ) {
		return new WP_Error(
			'dfrapi_get_affiliate_id_no_network_ids',
			__( 'No data for network with ID of ' . intval( $network_id ), 'datafeedr-api' )
		);
	}

	if ( ! isset( $networks[ $key ][ $network_id ][ $type ] ) || empty( $networks[ $key ][ $network_id ][ $type ] ) ) {
		return new WP_Error(
			'dfrapi_get_affiliate_id_empty_type',
			__( 'No affiliate or tracking ID entered for network with ID of ' . intval( $network_id ), 'datafeedr-api' )
		);
	}

	return $networks[ $key ][ $network_id ][ $type ];
}

/**
 * Get The Affiliate Gateway SID from this page WordPress Admin Area > Datafeedr API > Configuration
 *
 * @since 1.0.102
 * @return string|WP_Error
 */
function dfrapi_get_affiliate_gateway_sid() {

	static $sid = null;

	if ( null === $sid ) {

		$config = get_option( 'dfrapi_configuration', [] );

		$sid = ( isset( $config['affiliate_gateway_sid'] ) && ! empty( $config['affiliate_gateway_sid'] ) ) ?
			trim( $config['affiliate_gateway_sid'] ) :
			new WP_Error(
				'missing_affiliate_gateway_sid',
				'Please enter your The Affiliate Gateway SID <a href="' . admin_url( 'admin.php?page=dfrapi' ) . '" target="_blank">here</a>.'
			);
	}

	return $sid;
}

/**
 * Get Adservice Media ID from this page WordPress Admin Area > Datafeedr API > Configuration
 *
 * @since 1.0.102
 * @return string|WP_Error
 */
function dfrapi_get_adservice_mid() {

	static $sid = null;

	if ( null === $sid ) {

		$config = get_option( 'dfrapi_configuration', [] );

		$sid = ( isset( $config['adservice_mid'] ) && ! empty( $config['adservice_mid'] ) ) ?
			trim( $config['adservice_mid'] ) :
			new WP_Error(
				'missing_adservice_mid',
				'Please enter your Adservice Media ID <a href="' . admin_url( 'admin.php?page=dfrapi' ) . '" target="_blank">here</a>.'
			);
	}

	return $sid;
}

/**
 * Get Belboon Adspace ID from this page WordPress Admin Area > Datafeedr API > Configuration
 *
 * @since 1.0.124
 * @return string|WP_Error
 */
function dfrapi_get_belboon_adspace_id() {

	static $aid = null;

	if ( null === $aid ) {

		$config = get_option( 'dfrapi_configuration', [] );

		$aid = ( isset( $config['belboon_aid'] ) && ! empty( $config['belboon_aid'] ) ) ?
			trim( $config['belboon_aid'] ) :
			new WP_Error(
				'missing_belboon_aid',
				'Please enter your Belboon Adspace ID <a href="' . admin_url( 'admin.php?page=dfrapi' ) . '" target="_blank">here</a>.'
			);
	}

	return $aid;
}

/**
 * @param string $url
 * @param string $method
 * @param array $args
 *
 * @return SimpleXMLElement|WP_Error
 */
function dfrapi_get_xml_response( $url, $method = 'GET', array $args = [] ) {

	$response = $method === 'GET' ? wp_remote_get( $url, $args ) : wp_remote_post( $url, $args );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = wp_remote_retrieve_body( $response );

	if ( $code < 200 || $code >= 300 ) {
		return new WP_Error( $code, strip_tags( $body ) );
	}

	if ( ! strlen( $body ) ) {
		return new WP_Error( 'connection_error', esc_html__( 'Empty response', 'datafeedr' ) );
	}

	$xml = simplexml_load_string( $body, null, LIBXML_NOCDATA );

	if ( $xml->getName() === 'error' ) {
		return new WP_Error( $code, esc_html( strval( $xml->message ) ) );
	}

	return $xml;
}

/**
 * Determine if a given string ends with a given substring.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $haystack
 * @param string|string[] $needles
 *
 * @return bool
 */
function dfrapi_ends_with( $haystack, $needles ) {
	foreach ( (array) $needles as $needle ) {
		if ( $needle !== '' && substr( $haystack, - strlen( $needle ) ) === (string) $needle ) {
			return true;
		}
	}

	return false;
}

/**
 * Determine if a given string starts with a given substring.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $haystack
 * @param string|string[] $needles
 *
 * @return bool
 */
function dfrapi_starts_with( $haystack, $needles ) {
	foreach ( (array) $needles as $needle ) {
		if ( (string) $needle !== '' && strncmp( $haystack, $needle, strlen( $needle ) ) === 0 ) {
			return true;
		}
	}

	return false;
}

/**
 * Returns the portion of string specified by the start and length parameters.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $string
 * @param int $start
 * @param int|null $length
 *
 * @return string
 */
function dfrapi_substr( $string, $start, $length = null ) {
	return mb_substr( $string, $start, $length, 'UTF-8' );
}

/**
 * Get the portion of a string before the first occurrence of a given value.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $subject
 * @param string $search
 *
 * @return string
 */
function dfrapi_str_before( $subject, $search ) {
	return $search === '' ? $subject : explode( $search, $subject )[0];
}

/**
 * Get the portion of a string before the last occurrence of a given value.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $subject
 * @param string $search
 *
 * @return string
 */
function dfrapi_str_before_last( $subject, $search ) {

	if ( $search === '' ) {
		return $subject;
	}

	$pos = mb_strrpos( $subject, $search );

	if ( $pos === false ) {
		return $subject;
	}

	return dfrapi_substr( $subject, 0, $pos );
}

/**
 * Get the portion of a string between two given values.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $subject
 * @param string $from
 * @param string $to
 *
 * @return string
 */
function dfrapi_str_between( $subject, $from, $to ) {
	if ( $from === '' || $to === '' ) {
		return $subject;
	}

	return dfrapi_str_before_last( dfrapi_str_after( $subject, $from ), $to );
}

/**
 * Return the remainder of a string after the first occurrence of a given value.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $subject
 * @param string $search
 *
 * @return string
 */
function dfrapi_str_after( $subject, $search ): string {
	return $search === '' ? $subject : array_reverse( explode( $search, $subject, 2 ) )[0];
}

/**
 * Return the remainder of a string after the last occurrence of a given value.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $subject
 * @param string $search
 *
 * @return string
 */
function dfrapi_str_after_last( $subject, $search ) {

	if ( $search === '' ) {
		return $subject;
	}

	$position = strrpos( $subject, (string) $search );

	if ( $position === false ) {
		return $subject;
	}

	return substr( $subject, $position + strlen( $search ) );
}

/**
 * Determine if a given string contains a given substring.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $haystack
 * @param string|string[] $needles
 *
 * @return bool
 */
function dfrapi_str_contains( $haystack, $needles ) {
	foreach ( (array) $needles as $needle ) {
		if ( $needle !== '' && mb_strpos( $haystack, $needle ) !== false ) {
			return true;
		}
	}

	return false;
}

/**
 * Determine if a given string contains all array values.
 *
 * @link https://github.com/illuminate/support/blob/7.x/Str.php
 *
 * @param string $haystack
 * @param string[] $needles
 *
 * @return bool
 */
function dfrapi_str_contains_all( $haystack, array $needles ) {
	foreach ( $needles as $needle ) {
		if ( ! dfrapi_str_contains( $haystack, $needle ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Returns an integer value of string by:
 *  - removing all non-numeric characters
 *  - retaining any negative sign
 *  - running through intval to clean up any other weirdness.
 *
 * This function is pretty forgiving.
 *
 * Examples:
 *
 * '123.45'      => 12345
 * '-123.45'     => -12345
 * 'foo'         => 0
 * '-foo'        => 0
 * '-123-56'     => -123
 * '123-45'      => 123
 * '-123,56'     => -12356
 * '$-1.111,45'  => -111145
 * '00012.34'    => 1234
 *
 * @param string|int|numeric $int
 *
 * @return int
 */
function dfrapi_intify( $int ): int {
	return (int) preg_replace( '/[^0-9-]/', '', $int );
}

/**
 * Returns an instance of the Dfrapi_Price class.
 *
 * @param mixed $value The value to use as the price.
 * @param string $currency_code 3-character ISO 4217 currency code.
 * @param mixed $context Optional.
 *
 * @return Dfrapi_Price
 */
function dfrapi_price( $value, $currency_code, $context = null ): Dfrapi_Price {
	return new Dfrapi_Price( $value, dfrapi_currency( $currency_code, $context ), $context );
}

/**
 * Returns an instance of the Dfrapi_Currency class.
 *
 * @param string $currency_code 3-character ISO 4217 currency code.
 * @param mixed $context Optional.
 *
 * @return Dfrapi_Currency
 */
function dfrapi_currency( $currency_code, $context = null ): Dfrapi_Currency {
	return new Dfrapi_Currency( $currency_code, $context );
}

/**
 * Returns the fully formatted price.
 *
 * @param mixed $value The value to use as the price.
 * @param string $currency_code 3-character ISO 4217 currency code.
 * @param mixed $context Optional.
 *
 * @return string
 */
function dfrapi_get_price( $value, $currency_code, $context = null ): string {
	return dfrapi_price( $value, $currency_code, $context )->get_price();
}

/**
 * Returns an instance of the Dfrapi_Image_Data class.
 *
 * @param string $url The URL of the image we will be uploading.
 *
 * @return Dfrapi_Image_Data
 */
function dfrapi_image_data( string $url ): Dfrapi_Image_Data {
	return new Dfrapi_Image_Data( $url );
}

/**
 * Returns an instance of the Dfrapi_Image_Uploader class.
 *
 * @param Dfrapi_Image_Data $image_data
 *
 * @return Dfrapi_Image_Uploader
 */
function dfrapi_image_uploader( Dfrapi_Image_Data $image_data ): Dfrapi_Image_Uploader {
	return new Dfrapi_Image_Uploader( $image_data );
}

/**
 * Returns the string to use as the prefix for the ActionScheduler hook name.
 *
 * @return string
 */
function dfrapi_as_hook_prefix(): string {
	return 'dfrapi_as_';
}

/**
 * Formats and returns the hook name.
 *
 * @param string $hook
 *
 * @return string
 */
function dfrapi_as_hook_name( string $hook ): string {
	return dfrapi_as_hook_prefix() . trim( $hook );
}

/**
 * Returns true is the ActionScheduler library exists otherwise returns WP_Error.
 *
 * The ActionScheduler ships with WooCommerce but can also be installed independently
 * here: https://wordpress.org/plugins/action-scheduler/
 *
 * @return true|WP_Error
 */
function dfrapi_action_scheduler_exists() {
	return function_exists( 'as_schedule_recurring_action' )
		? true
		: new WP_Error( 'dfrapi_action_scheduler_does_not_exist.', __( 'The ActionScheduler library does not exist.', 'datafeedr-api' ) );
}

/**
 * Enqueue an action to run one time, as soon as possible
 *
 * @param string $hook
 * @param array $args
 * @param string $group
 *
 * @return string The action ID.
 */
function dfrapi_schedule_async_action( string $hook, array $args = [], string $group = 'datafeedr' ) {
	return ( dfrapi_action_scheduler_exists() === true )
		? as_enqueue_async_action( dfrapi_as_hook_name( $hook ), $args, $group )
		: dfrapi_action_scheduler_exists();
}

/**
 * Schedule an action to run one time
 *
 * @param int $timestamp
 * @param string $hook
 * @param array $args
 * @param string $group
 *
 * @return string The action ID
 */
function dfrapi_schedule_single_action( int $timestamp, string $hook, array $args = [], string $group = 'datafeedr' ) {
	return ( dfrapi_action_scheduler_exists() === true )
		? as_schedule_single_action( $timestamp, dfrapi_as_hook_name( $hook ), $args, $group )
		: dfrapi_action_scheduler_exists();
}

/**
 * Schedule a recurring action using ActionScheduler.
 *
 * @param int $timestamp
 * @param int $interval_in_seconds
 * @param string $hook
 * @param array $args
 * @param string $group
 *
 * @return int|WP_Error The action ID or WP_Error if as_schedule_recurring_action() function does not exist.
 */
function dfrapi_schedule_recurring_action( int $timestamp, int $interval_in_seconds, string $hook, array $args = [], string $group = 'datafeedr' ) {
	return ( dfrapi_action_scheduler_exists() === true )
		? as_schedule_recurring_action( $timestamp, $interval_in_seconds, dfrapi_as_hook_name( $hook ), $args, $group )
		: dfrapi_action_scheduler_exists();
}

/**
 * Schedule an action that recurs on a cron-like schedule.
 *
 * @param int $timestamp The first instance of the action will be scheduled to run at a time calculated after this timestamp matching the cron expression. This can be used to delay the first instance of the action.
 * @param string $schedule A cron-like schedule string (See: http://en.wikipedia.org/wiki/Cron)
 *   *    *    *    *    *    *
 *   ┬    ┬    ┬    ┬    ┬    ┬
 *   |    |    |    |    |    |
 *   |    |    |    |    |    + year [optional]
 *   |    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
 *   |    |    |    +---------- month (1 - 12)
 *   |    |    +--------------- day of month (1 - 31)
 *   |    +-------------------- hour (0 - 23)
 *   +------------------------- min (0 - 59)
 * @param string $hook
 * @param array $args
 * @param string $group
 *
 * @return int|WP_Error The action ID or WP_Error if as_schedule_cron_action() function does not exist.
 */
function dfrapi_schedule_cron_action( int $timestamp, string $schedule, string $hook, array $args = [], string $group = 'datafeedr' ) {
	return ( dfrapi_action_scheduler_exists() === true )
		? as_schedule_cron_action( $timestamp, $schedule, dfrapi_as_hook_name( $hook ), $args, $group )
		: dfrapi_action_scheduler_exists();
}

/**
 * Unschedule a scheduled action.
 *
 * @param string $hook
 * @param array $args
 * @param string $group
 *
 * @return string|null|WP_Error The scheduled action ID if a scheduled action was found, or null if no matching action found. WP_Error if as_ function doesn't exist.
 */
function dfrapi_unschedule_action( string $hook, array $args = [], string $group = 'datafeedr' ) {
	return ( dfrapi_action_scheduler_exists() === true )
		? as_unschedule_action( dfrapi_as_hook_name( $hook ), $args, $group )
		: dfrapi_action_scheduler_exists();
}

/**
 * Returns the timestamp for the next occurrence of a pending scheduled action,
 * returns true for an async or in-progress action or false if there is no matching action.
 *
 * @param string $hook
 * @param array $args
 * @param string $group
 *
 * @return int|bool|WP_Error The timestamp for the next occurrence of a pending scheduled action, true for an async or in-progress action or false if there is no matching action. WP_Error if as_ function doesn't exist.
 */
function dfrapi_next_scheduled_action( string $hook, array $args = [], string $group = 'datafeedr' ) {
	return ( dfrapi_action_scheduler_exists() === true )
		? as_next_scheduled_action( dfrapi_as_hook_name( $hook ), $args, $group )
		: dfrapi_action_scheduler_exists();
}

/**
 * Returns true if the Jetpack::class exists.
 *
 * @return bool
 */
function dfrapi_jetpack_exists(): bool {
	return class_exists( Jetpack::class, false );
}

/**
 * Returns true is Jetpack is active, otherwise returns false.
 *
 * @return bool
 */
function dfrapi_jetpack_is_active(): bool {
	return dfrapi_jetpack_exists() ? Jetpack::is_active() : false;
}

/**
 * Returns true if Jetpack is in Dev/Debug mode.
 *
 * @return bool
 */
function dfrapi_jetpack_is_in_dev_mode(): bool {
	return dfrapi_jetpack_exists() ? defined( 'JETPACK_DEV_DEBUG' ) && JETPACK_DEV_DEBUG === true : false;
}

/**
 * Returns true if "Speed up image load times" is ON here:
 * WordPress Admin Area > Jetpack > Settings > Performance > Performance & speed
 *
 * @return bool
 */
function dfrapi_jetpack_photon_module_is_active(): bool {
	return dfrapi_jetpack_exists() ? in_array( 'photon', Jetpack::get_active_modules() ) : false;
}

/**
 * Returns true if "Speed up static file load times" is ON here:
 * WordPress Admin Area > Jetpack > Settings > Performance > Performance & speed
 *
 * @return bool
 */
function dfrapi_jetpack_photon_cdn_module_is_active(): bool {
	return dfrapi_jetpack_exists() ? in_array( 'photon-cdn', Jetpack::get_active_modules() ) : false;
}

/**
 * @param string $image_url URL to the publicly accessible image you want to manipulate.
 * @param array|string $args An array of arguments, i.e. array( 'w' => '300', 'resize' => array( 123, 456 ) ), or in string form (w=123&h=456).
 * @param string|null $scheme URL protocol.
 *
 * @return false|string
 */
function dfrapi_jetpack_photon_url( $image_url, $args = [], $scheme = null ) {

	if ( ! dfrapi_jetpack_is_active() && ! dfrapi_jetpack_is_in_dev_mode() ) {
		return $image_url;
	}

	return jetpack_photon_url( $image_url, $args, $scheme );
}

/**
 * Whether to use the legacy image importer. Default false.
 *
 * @return bool
 */
function dfrapi_use_legacy_image_importer(): bool {
	return (bool) apply_filters( 'dfrapi_use_legacy_image_importer', false );
}

/**
 * Formats an Admin Notice and echos it.
 *
 * You must send a fully escaped $message to dfrapi_admin_notice() because this
 * function will NOT escape the $message variable.
 *
 * Also, $message is already wrapped in <p></p> tags. Therefore, it cannot contain
 * additional <p> tags or any other HTML element not allowed as a child to <p> tags.
 *
 * @param string $message Notice message. Will NOT be escaped.
 * @param string $status Either error, warning, success or info.
 * @param string|null $heading Optional. The notice heading or title.
 * @param string|null $plugin Optional. The name of the plugin responsible for generating this notice.
 *
 * @return void
 */
function dfrapi_admin_notice( string $message, string $status, string $heading = null, string $plugin = null ) {
	$plugin    = $plugin ? esc_html( trim( $plugin ) ) : '';
	$heading   = $heading ? esc_html( trim( $heading ) ) : '';
	$separator = $plugin && $heading ? ' &mdash; ' : '';
	$label     = $plugin || $heading ? sprintf( '<strong>%1$s%2$s%3$s</strong><br>', $plugin, $separator, $heading ) : '';

	$status = in_array( $status, [ 'error', 'warning', 'success', 'info' ] ) ? $status : 'info';
	$class  = esc_attr( 'notice notice-' . $status );

	printf( '<div class="%1$s"><p>%2$s%3$s</p></div>', $class, $label, $message );
}

/**
 * Get selected networks. Format is like this:
 *
 *  Array (
 *      [ids] => Array (
 *          [18] => Array (
 *              [nid] => 18
 *              [aid] => abc123
 *              [tid] =>
 *          )
 *          [1200] => Array (
 *              [nid] => 1200
 *              [aid] => qwerty
 *              [tid] =>
 *          )
 *          [126] => Array (
 *              [nid] => 126
 *              [aid] => 15759
 *              [tid] =>
 *          )
 *      )
 *  )
 *
 * @return array
 */
function dfrapi_get_selected_networks(): array {
	return (array) get_option( 'dfrapi_networks', [] );
}

/**
 * Get the list of selected Network IDs.
 *
 * @return array
 */
function dfrapi_get_selected_network_ids(): array {

	static $ids = null;

	if ( $ids === null ) {

		$ids      = [];
		$networks = dfrapi_get_selected_networks();

		if ( ! isset( $networks['ids'] ) ) {
			return $ids;
		}

		if ( empty( $networks['ids'] ) ) {
			return $ids;
		}

		if ( ! is_array( $networks['ids'] ) ) {
			return $ids;
		}

		foreach ( $networks['ids'] as $k => $v ) {
			$nid = absint( $v['nid'] ?? 0 );
			if ( $nid > 0 ) {
				$ids[] = absint( $nid );
			}
		}

		$ids = array_filter( array_unique( $ids ) );
	}

	return $ids;
}

/**
 * Get the total number of selected networks.
 *
 * @return int
 */
function dfrapi_selected_network_count(): int {
	return count( dfrapi_get_selected_network_ids() );
}

/**
 * Returns true if the user has selected at least 1 network. Otherwise, returns false.
 *
 * @return bool
 */
function dfrapi_user_has_selected_networks(): bool {
	return dfrapi_selected_network_count() > 0;
}

/**
 * Get selected merchants. Format is like this:
 *
 *  Array (
 *      [ids] => Array (
 *          [0] => 1258
 *          [1] => 12927
 *          [2] => 1312
 *          [3] => 14342
 *      )
 *  )
 *
 * @return array
 */
function dfrapi_get_selected_merchants(): array {
	return (array) get_option( 'dfrapi_merchants', [] );
}

/**
 * Get the list of selected Merchant IDs.
 *
 * @return array
 */
function dfrapi_get_selected_merchant_ids(): array {

	static $ids = null;

	if ( $ids === null ) {

		$ids       = [];
		$merchants = dfrapi_get_selected_merchants();

		if ( ! isset( $merchants['ids'] ) ) {
			return $ids;
		}

		if ( empty( $merchants['ids'] ) ) {
			return $ids;
		}

		if ( ! is_array( $merchants['ids'] ) ) {
			return $ids;
		}

		foreach ( $merchants['ids'] as $id ) {
			$ids[] = absint( $id );
		}

		$ids = array_filter( array_unique( $ids ) );
	}

	return $ids;
}

/**
 * Get the total number of selected merchants.
 *
 * @return int
 */
function dfrapi_selected_merchant_count(): int {
	return count( dfrapi_get_selected_merchant_ids() );
}

/**
 * Returns true if the user has selected at least 1 merchant. Otherwise, returns false.
 *
 * @return bool
 */
function dfrapi_user_has_selected_merchants(): bool {
	return dfrapi_selected_merchant_count() > 0;
}

/**
 * Returns the absolute URL for the Datafeedr API > Networks page.
 *
 * @return string
 */
function dfrapi_networks_page_url(): string {
	return add_query_arg( [ 'page' => 'dfrapi_networks' ], admin_url( 'admin.php' ) );
}

/**
 * Returns the absolute URL for the Datafeedr API > Merchants page.
 *
 * @return string
 */
function dfrapi_merchants_page_url(): string {
	return add_query_arg( [ 'page' => 'dfrapi_merchants' ], admin_url( 'admin.php' ) );
}

/**
 * Returns the absolute URL for the Datafeedr API > Configuration page.
 *
 * @return string
 */
function dfrapi_configuration_page_url(): string {
	return add_query_arg( [ 'page' => 'dfrapi' ], admin_url( 'admin.php' ) );
}

/**
 * Returns the absolute URL for the Datafeedr API > Tools page.
 *
 * @return string
 */
function dfrapi_tools_page_url(): string {
	return add_query_arg( [ 'page' => 'dfrapi_tools' ], admin_url( 'admin.php' ) );
}

/**
 * Returns an array of Network IDs of those networks who require an affiliate ID but the
 * user has not yet entered an affiliate ID.
 *
 * @return array
 */
function dfrapi_get_network_ids_missing_affiliate_id(): array {

	static $ids = null;

	if ( $ids === null ) {

		$ids      = [];
		$networks = dfrapi_get_selected_networks();

		if ( ! isset( $networks['ids'] ) ) {
			return $ids;
		}

		if ( ! is_array( $networks['ids'] ) ) {
			return $ids;
		}

		$no_affiliate_id_required = dfrapi_get_ids_of_networks_which_dont_require_affiliate_ids();

		foreach ( $networks['ids'] as $k => $v ) {

			$nid = absint( $v['nid'] ?? 0 );

			if ( in_array( $nid, $no_affiliate_id_required, true ) ) {
				continue;
			}

			$aid = trim( $v['aid'] ?? '' );

			if ( $nid > 0 && empty( $aid ) ) {
				$ids[] = $nid;
			}
		}
	}

	return array_filter( array_unique( $ids ) );
}

/**
 * Returns an array of Network IDs which don't require the user to enter an affiliate ID.
 *
 * @return array
 */
function dfrapi_get_ids_of_networks_which_dont_require_affiliate_ids(): array {
	return array_merge(
		dfrapi_get_partnerize_network_ids(),
		dfrapi_get_effiliation_network_ids()
	);
}

/**
 * Returns true if user is missing at least one affiliate ID. Otherwise, returns false.
 *
 * @return bool
 */
function dfrapi_user_is_missing_affiliate_ids(): bool {
	return count( dfrapi_get_network_ids_missing_affiliate_id() ) > 0;
}

// Functions from functions/api.php 2022-02-14 14:02:41 from here to effiliation_ids function
function dfrapi_api_get_status() {
	$api = dfrapi_api( dfrapi_get_transport_method() );
	try {
		$status = $api->getStatus();
		dfrapi_api_update_status( $api );

		return $status;
	} catch ( Exception $err ) {
		return dfrapi_api_error( $err );
	}
}

/**
 * Removed configuration. Always returns 'wordpress'. 2017-02-21 10:23:10
 */
function dfrapi_get_transport_method(): string {
	return 'wordpress';
}

/**
 * This instantiates the Datafeedr API Library and returns the $api object.
 */
function dfrapi_api( $transport = 'curl', $timeout = 0, $returnObjects = false ) {

	$configuration = (array) get_option( 'dfrapi_configuration' );

	if ( isset( $configuration['disable_api'] ) && ( $configuration['disable_api'] === 'yes' ) ) {
		$configuration['disable_api'] = 'no';
		update_option( 'dfrapi_configuration', $configuration );
	}

	$access_id  = false;
	$secret_key = false;
	$transport  = dfrapi_get_transport_method();

	if ( isset( $configuration['access_id'] ) && ( $configuration['access_id'] != '' ) ) {
		$access_id = $configuration['access_id'];
	}

	if ( isset( $configuration['secret_key'] ) && ( $configuration['secret_key'] != '' ) ) {
		$secret_key = $configuration['secret_key'];
	}

	if ( $access_id && $secret_key ) {

		$options = [
			'transport'     => 'wordpress',
			'timeout'       => 60,
			'returnObjects' => false,
			'retry'         => 3, // The number of retries if an API request times-out.
			'retryTimeout'  => 5, // The number of seconds to wait between retries.
		];

		$options = apply_filters( 'dfrapi_api_options', $options );

		$options['domain'] = parse_url( get_site_url(), PHP_URL_HOST );

		return new DatafeedrApi( $access_id, $secret_key, $options );

	} else {
		return false;
	}
}

/**
 * Creates an associate array with the API's error details.
 */
function dfrapi_api_error( $error, $params = false ) {

	// Change "request_count" to "max_requests" because sometimes there's
	// not even enough API requests left to update the Account info with
	// the most update to date information.
	if ( $error->getCode() == 301 ) {
		$account                  = get_option( 'dfrapi_account', array() );
		$account['request_count'] = $account['max_requests'];
		update_option( 'dfrapi_account', $account );
	}

	return array(
		'dfrapi_api_error' => array(
			'class'  => get_class( $error ),
			'code'   => $error->getCode(),
			'msg'    => $error->getMessage(),
			'params' => $params,
		)
	);
}

/**
 * Creates the proper API request from the $query.
 */
function dfrapi_api_query_to_filters( $query, $useSelected = true ) {
	$sform = new Dfrapi_SearchForm();

	return $sform->makeFilters( $query, $useSelected );
}

/**
 * Returns a parameter value from the $query array.
 */
function dfrapi_api_get_query_param( $query, $param ) {
	if ( is_array( $query ) && ! empty( $query ) ) {
		foreach ( $query as $k => $v ) {
			if ( $v['field'] == $param ) {
				return array(
					'field'    => $v['field'] ?? '',
					'operator' => $v['operator'] ?? '',
					'value'    => $v['value'] ?? '',
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
		$account                   = get_option( 'dfrapi_account', array() );
		$account['user_id']        = $status['user_id'];
		$account['plan_id']        = $status['plan_id'];
		$account['bill_day']       = $status['bill_day'];
		$account['max_total']      = $status['max_total'];
		$account['max_length']     = $status['max_length'];
		$account['max_requests']   = $status['max_requests'];
		$account['request_count']  = $status['request_count'];
		$account['network_count']  = $status['network_count'];
		$account['product_count']  = $status['product_count'];
		$account['merchant_count'] = $status['merchant_count'];
		update_option( 'dfrapi_account', $account );
	}
}

/**
 * This returns all affiliate networks' information.
 * This accepts an array of source_ids (network ids)
 * to return a subset of networks.
 */
function dfrapi_api_get_all_networks( $nids = array() ) {
	$option_name = 'dfrapi_all_networks';
	$use_cache   = wp_using_ext_object_cache( false );
	$networks    = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );
	if ( false === $networks || empty ( $networks ) ) {
		$api = dfrapi_api( dfrapi_get_transport_method() );
		try {
			$networks = $api->getNetworks( $nids, true );
			dfrapi_api_set_network_types( $networks );
			dfrapi_api_update_status( $api );
		} catch ( Exception $err ) {
			return dfrapi_api_error( $err );
		}
		$use_cache = wp_using_ext_object_cache( false );
		set_transient( $option_name, $networks, MONTH_IN_SECONDS );
		wp_using_ext_object_cache( $use_cache );
	}
	dfrapi_update_transient_whitelist( $option_name );

	usort( $networks, function ( $a, $b ) {
		return strnatcasecmp( $a['name'], $b['name'] );
	} );

	return array_filter( $networks, static function ( $network ) {
		return ! in_array( absint( $network['_id'] ), dfrapi_inactive_networks(), true );
	} );
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
 * Returns a Partnerize camref value.
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
 * Returns a Effiliation affiliate ID.
 *
 * @since 1.0.81
 */
function dfrapi_api_get_effiliation_affiliate_id( $merchant_id ) {

	$option_name  = 'effiliation_' . $merchant_id;
	$use_cache    = wp_using_ext_object_cache( false );
	$affiliate_id = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );

	if ( $affiliate_id ) {
		return $affiliate_id;
	}

	try {
		$affiliate_id = dfrapi_get_affiliate_id_for_effiliation_merchant( $merchant_id );
	} catch ( Exception $err ) {
		$affiliate_id = 'dfrapi_unapproved_effiliation_merchant';
	}

	$use_cache = wp_using_ext_object_cache( false );
	set_transient( $option_name, $affiliate_id, WEEK_IN_SECONDS );
	wp_using_ext_object_cache( $use_cache );

	dfrapi_update_transient_whitelist( $option_name );

	return $affiliate_id;
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
	$coupon_networks  = array();
	foreach ( $networks as $network ) {
		if ( $network['type'] === 'products' ) {
			$product_networks[ $network['_id'] ] = $network;
		} elseif ( $network['type'] === 'coupons' ) {
			$coupon_networks[ $network['_id'] ] = $network;
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
	$use_cache   = wp_using_ext_object_cache( false );
	$merchants   = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );
	if ( false === $merchants || empty ( $merchants ) ) {
		$api = dfrapi_api( dfrapi_get_transport_method() );
		try {
			$merchants = $api->getMerchants( array( intval( $nid ) ), true );
			dfrapi_api_update_status( $api );
		} catch ( Exception $err ) {
			return dfrapi_api_error( $err );
		}
		$use_cache = wp_using_ext_object_cache( false );
		set_transient( $option_name, $merchants, MONTH_IN_SECONDS );
		wp_using_ext_object_cache( $use_cache );
	}
	dfrapi_update_transient_whitelist( $option_name );

	return $merchants;
}

/**
 * This returns merchant or merchants' information by merchant_id or
 * an array of merchant IDs.
 */
function dfrapi_api_get_merchants_by_id( $ids, $includeEmpty = false ) {
	$name = false;
	if ( is_array( $ids ) ) {
		sort( $ids, SORT_NUMERIC );
		$id_string = implode( ",", $ids );
		$name      = md5( $id_string );
	} elseif ( $ids != '' ) {
		$name = trim( $ids );
	}
	if ( ! $name ) {
		return;
	}
	$name        = substr( $name, 0, 20 );
	$option_name = 'dfrapi_merchants_byid_' . $name;
	$use_cache   = wp_using_ext_object_cache( false );
	$merchants   = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );
	if ( false === $merchants || empty ( $merchants ) ) {
		$api = dfrapi_api( dfrapi_get_transport_method() );
		try {
			$merchants = $api->getMerchantsById( $ids, $includeEmpty );
			dfrapi_api_update_status( $api );
		} catch ( Exception $err ) {
			return dfrapi_api_error( $err );
		}
		$use_cache = wp_using_ext_object_cache( false );
		set_transient( $option_name, $merchants, MONTH_IN_SECONDS );
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
function dfrapi_api_get_products_by_id( $ids, $ppp = 20, $page = 1 ) {

	$response = array();

	// Return false if no $ids or no $postid
	if ( empty( $ids ) ) {
		return $response;
	}

	// Make sure $page is a positive integer.
	$page = absint( $page );

	// Make sure $ppp is a positive integer.
	$ppp = absint( $ppp );

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
	if ( $offset >= ( $max_total - $ppp ) ) {
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
					'name'        => $excluded_id . ' - ' . __( 'Unavailable', 'datafeedr-api' ),
					'price'       => 0,
					'finalprice'  => 0,
					'description' => __( 'This product is either temporarily or permanently unavailable.', 'datafeedr-api' ),
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
		$response['score']       = $search->getQueryScore();

		// Return it!
		return $response;

	} catch ( Exception $err ) {
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
 *        'value' => 'shoes',
 *        'field' => 'any',
 *        'operator' => 'contain'
 *  );
 *
 *  $query[] = array(
 *        'value' => 'image',
 *        'field' => 'duplicates',
 *        'operator' => 'is'
 *  );
 *
 *  $query[] = array(
 *        'field' => 'sort',
 *        'operator' => '+saleprice'
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
function dfrapi_api_get_products_by_query( $query, $ppp = 20, $page = 1, $excluded = array() ) {

	$response = array();

	// Return false if no $query.
	if ( empty( $query ) ) {
		return $response;
	}

	// Make sure $page is a positive integer.
	$page = absint( $page );

	// Make sure $ppp is a positive integer.
	$ppp = absint( $ppp );

	// Make sure $ppp is not greater than "max_length".
	$account = (array) get_option( 'dfrapi_account' );
	if ( $ppp > $account['max_length'] ) {
		$ppp = $account['max_length'];
	}

	// The maximum number of results a request to the API can return.
	// Changing this will only break your site. It's not overridable.
	$max_total = $account['max_total'];

	// Determine query limit (if exists).
	$query_limit = dfrapi_api_get_query_param( $query, 'limit' );
	$query_limit = ( $query_limit )
		? $query_limit['value']
		: false;

	// No query shall try to return more than 10,000 products.
	if ( $query_limit && ( $query_limit > $max_total ) ) {
		$query_limit = $max_total;
	}

	// Determine merchant limit (if exists).
	$merchant_limit = dfrapi_api_get_query_param( $query, 'merchant_limit' );
	$merchant_limit = ( $merchant_limit )
		? absint( $merchant_limit['value'] )
		: 0;

	// Determine offset.
	$offset = ( ( $page - 1 ) * $ppp );

	// If offset is greater than 10,000 return empty array();
	if ( $offset >= $max_total ) {
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
		if ( ! $api ) {
			return $response;
		}

		$search = $api->searchRequest();

		// Get filters
		$filters = dfrapi_api_query_to_filters( $query );
		if ( isset( $filters['error'] ) ) {
			throw new DatafeedrError( $filters['error'], 0 );
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
		if ( ! empty( $excluded ) ) {
			$search->addFilter( 'id !IN ' . implode( ",", $excluded ) );
		}

		// Sort products.
		$sort = dfrapi_api_get_query_param( $query, 'sort' );
		if ( $sort && strlen( $sort['operator'] ) ) {
			$search->addSort( $sort['operator'] );
		}

		// Set Merchant Limit
		$search->setMerchantLimit( $merchant_limit );

		// Set limits and offset.
		$search->setLimit( $ppp );
		$search->setOffset( $offset );

		// Execute query.
		$products = $search->execute();

		// Update API status
		dfrapi_api_update_status( $api );

		// Build $response array().
		$response['query']       = $query;
		$response['excluded']    = $excluded;
		$response['products']    = $products;
		$response['last_status'] = $api->lastStatus();
		$response['found_count'] = $search->getResultCount();
		$response['params']      = $search->getParams();
		$response['score']       = $search->getQueryScore();

		// Return it!
		return $response;

	} catch ( Exception $err ) {
		$params = $search->getParams();

		return dfrapi_api_error( $err, $params );

	}
}

/**
 * Returns the URL to get the Effiliation product feeds URL with user's API injected into URL.
 *
 * @param string $api_key
 *
 * @return string
 */
function dfrapi_get_effiliation_product_feeds_url( string $api_key ): string {
	return sprintf( 'http://apiv2.effiliation.com/apiv2/productfeeds.xml?key=%s&filter=mines&type=33&fields=0001010000110001', $api_key );
}

/**
 * Get affiliate IDs from Effiliation.
 *
 * @param $api_key
 *
 * @return array|mixed|SimpleXMLElement|WP_Error
 */
function dfrapi_request_effiliation_affiliate_ids( $api_key = null ) {

	$option_name   = 'effiliation_affiliate_ids';
	$use_cache     = wp_using_ext_object_cache( false );
	$affiliate_ids = get_transient( $option_name );
	wp_using_ext_object_cache( $use_cache );

	if ( $affiliate_ids ) {
		return $affiliate_ids;
	}

	$keys    = dfrapi_get_effiliation_keys();
	$api_key = $api_key ?: $keys['effiliation_key'];
	$method  = 'GET';
	$url     = dfrapi_get_effiliation_product_feeds_url( $api_key );

	$xml = dfrapi_get_xml_response( $url, $method, [ 'timeout' => 30 ] );

	if ( is_wp_error( $xml ) ) {
		return $xml;
	}

	$affiliate_ids = [];

	foreach ( $xml->feed as $e ) {
		$item = json_decode( json_encode( $e ), true );
		$suid = sanitize_text_field( $item['id_affilieur'] );

		$affiliate_ids[ $suid ]['suid']         = ( $suid );
		$affiliate_ids[ $suid ]['affiliate_id'] = sanitize_text_field( $item['id_compteur'] );
	}

	$use_cache = wp_using_ext_object_cache( false );
	set_transient( $option_name, $affiliate_ids, ( MINUTE_IN_SECONDS * 20 ) );
	wp_using_ext_object_cache( $use_cache );
	dfrapi_update_transient_whitelist( $option_name );

	return $affiliate_ids;
}

/**
 * @param $merchant_id
 *
 * @return mixed|string
 * @throws Exception
 */
function dfrapi_get_affiliate_id_for_effiliation_merchant( $merchant_id ) {
	$merchants     = dfrapi_api_get_merchants_by_id( $merchant_id );
	$merchant      = $merchants[0] ?? [ 'suids' => '' ];
	$affiliate_ids = dfrapi_request_effiliation_affiliate_ids();

	if ( is_wp_error( $affiliate_ids ) ) {
		throw new Exception( 'Unable to query Effiliation at this time. Please try again in 15 minutes.' );
	}

	if ( ! isset( $affiliate_ids[ $merchant['suids'] ]['affiliate_id'] ) ) {
		throw new Exception( 'Suid does not exist for affiliate ID.' );
	}

	return $affiliate_ids[ $merchant['suids'] ]['affiliate_id'];
}

/**
 * An array of data about the user's Datafeedr account. Formatted like:
 *
 *  Array (
 *      [network_count] => 227
 *      [plan_id] => 30600000
 *      [user_id] => 70123
 *      [max_total] => 10000
 *      [merchant_count] => 84031
 *      [max_requests] => 100000
 *      [bill_day] => 25
 *      [request_count] => 11061
 *      [product_count] => 797373259
 *      [max_length] => 100
 *  )
 *
 * @return array
 */
function dfrapi_get_user_account_data(): array {
	return (array) get_option( 'dfrapi_account', [] );
}

/**
 * Returns the total number of networks in the Datafeedr API.
 *
 * @return int
 */
function dfrapi_get_network_count(): int {
	$data = dfrapi_get_user_account_data();

	return absint( $data['network_count'] ?? 0 );
}

/**
 * Returns the total number of merchants in the Datafeedr API.
 *
 * @return int
 */
function dfrapi_get_merchant_count(): int {
	$data = dfrapi_get_user_account_data();

	return absint( $data['merchant_count'] ?? 0 );
}

/**
 * Returns the total number of products in the Datafeedr API.
 *
 * @return int
 */
function dfrapi_get_product_count(): int {
	$data = dfrapi_get_user_account_data();

	return absint( $data['product_count'] ?? 0 );
}

/**
 * The maximum number of API requests the user is allowed to make during a single subscription period (i.e. 30 days).
 *
 * @return int
 */
function dfrapi_get_max_requests(): int {
	$data = dfrapi_get_user_account_data();

	return absint( $data['max_requests'] ?? 0 );
}

/**
 * The current number of API requests the user has made during the current subscription period (i.e. 30 days).
 *
 * @return int
 */
function dfrapi_get_request_count(): int {
	$data = dfrapi_get_user_account_data();

	return absint( $data['request_count'] ?? 0 );
}

/**
 * Returns the user's API requests usage as a percentage of their total requests allowed.
 *
 * @param int $precision Default: 2
 *
 * @return float|int
 */
function dfrapi_get_api_usage_as_percentage( int $precision = 2 ) {
	$max_requests  = dfrapi_get_max_requests();
	$request_count = dfrapi_get_request_count();

	return $max_requests > 0 ? round( ( $request_count / $max_requests * 100 ), $precision ) : 0;
}

/**
 * Returns an array of network IDs for the Partnerize affiliate network.
 *
 * @return int[]
 */
function dfrapi_get_partnerize_network_ids(): array {
	return [ 801, 811, 812, 813, 814, 815, 816, 817, 818, 819, 820, 821, 822, 823 ];
}

/**
 * Returns the Group ID for Partnerize.
 *
 * @return int
 */
function dfrapi_get_partnerize_group_id(): int {
	return 10027;
}

/**
 * Returns an array of network IDs for the Effiliation affiliate network.
 *
 * @return int[]
 */
function dfrapi_get_effiliation_network_ids(): array {
	return [ 805, 806, 807 ];
}

/**
 * Returns the Group ID for Effiliation.
 *
 * @return int
 */
function dfrapi_get_effiliation_group_id(): int {
	return 10017;
}

/**
 * Returns the Group ID for Belboon.
 *
 * @return int
 */
function dfrapi_get_belboon_group_id(): int {
	return 10007;
}

/**
 * Get the affiliate ID for a specific network.
 *
 * @param int $network_id
 * @param mixed $default
 *
 * @return mixed|string Returns the affiliate ID if found otherwise it returns the value of $default.
 */
function dfrapi_get_affiliate_id_by_network_id( int $network_id, $default = false ) {

	static $network_ids = null;

	if ( $network_ids === null ) {

		$network_ids = [];

		$networks = dfrapi_get_selected_networks();

		if ( isset( $networks['ids'] ) && is_array( $networks['ids'] ) && ! empty( $networks['ids'] ) ) {
			$network_ids = $networks['ids'];
		}
	}

	foreach ( $network_ids as $k => $v ) {
		$nid = absint( $v['nid'] ?? 0 );
		if ( $nid === $network_id ) {
			$aid = trim( $v['aid'] ?? '' );

			return ! empty( $aid ) ? $aid : $default;
		}
	}

	return $default;
}

/**
 * Get one or more fields from a Datafeedr Product array.
 *
 * @since 1.3.1
 *
 * @param array $product A Datafeedr Product array (as returned from Datafeedr API).
 * @param string|array $fields A single field or an array of fields to return. Examples:
 *      - 'barcode'
 *      - ['barcode']
 *      - ['barcode', 'ean']
 * @param mixed $default Value to return if no fields are found in $product array. Default: null
 * @param false|string $concatenate False to return the first field found or a separator to concatenate all found fields.
 *
 * @return mixed
 */
function dfrapi_get_fields_from_product( array $product, $fields, $default = null, $concatenate = false ) {

	if ( ! is_string( $fields ) && ! is_array( $fields ) ) {
		return $default;
	}

	if ( is_string( $fields ) ) {
		$fields = [ (string) $fields ];
	}

	$fields = array_filter( $fields );

	if ( empty( $fields ) ) {
		return $default;
	}

	if ( count( $fields ) === 1 ) {
		return $product[ $fields[0] ] ?? $default;
	}

	$values = [];

	foreach ( $fields as $field ) {
		if ( isset( $product[ $field ] ) ) {
			$values[] = $product[ $field ];
		}
	}

	if ( empty( $values ) ) {
		return $default;
	}

	return is_string( $concatenate ) ? implode( $concatenate, $values ) : $values[0];
}

/**
 * Returns a URL to install a plugin from the WordPress.org repo.
 *
 * @since 1.3.1
 *
 * @param string $plugin Path to the plugin file relative to the plugin's directory. Ex: datafeedr-api/datafeedr-api.php
 *
 * @return string
 */
function dfrapi_get_install_plugin_url( string $plugin ): string {

	if ( ! current_user_can( 'install_plugins' ) ) {
		return admin_url( 'plugins.php' );
	}

	return add_query_arg(
		[ 'action' => 'install-plugin', 'plugin' => dfrapi_parse_plugin_path( $plugin, 'dirname' ) ],
		wp_nonce_url( admin_url( 'update.php' ), 'install-plugin_' . dfrapi_parse_plugin_path( $plugin, 'dirname' ) )
	);
}

/**
 * Returns a URL to activate a plugin.
 *
 * @since 1.3.1
 *
 * @param string $plugin Path to the plugin file relative to the plugin's directory. Ex: datafeedr-api/datafeedr-api.php
 *
 * @return string
 */
function dfrapi_get_activate_plugin_url( string $plugin ): string {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return admin_url( 'plugins.php' );
	}

	return add_query_arg(
		[ 'action' => 'activate', 'plugin' => dfrapi_parse_plugin_path( $plugin ), 'paged' => '1', 's' => '' ],
		wp_nonce_url( network_admin_url( 'plugins.php' ), 'activate-plugin_' . dfrapi_parse_plugin_path( $plugin ) )
	);
}

/**
 * Returns true if plugin is installed. Otherwise, returns false.
 *
 * @since 1.3.1
 *
 * @param string $plugin Path to the plugin file relative to the plugin's directory. Ex: datafeedr-api/datafeedr-api.php
 *
 * @return bool
 */
function dfrapi_plugin_is_installed( string $plugin ): bool {
	return file_exists( dfrapi_parse_plugin_path( $plugin, 'absolute' ) );
}

/**
 * This function parses and sanitizes a plugin path and returns it in the desired format.
 *
 * @since 1.3.1
 *
 * @param string $plugin Path to the plugin file relative to the plugin's directory. Ex: datafeedr-api/datafeedr-api.php
 * @param string $format The format in which to return the plugin info.
 *
 * @return string
 */
function dfrapi_parse_plugin_path( string $plugin, string $format = 'relative' ): string {

	// For examples below, if $plugin equals = "hello-dolly/hello.php"...
	$valid_formats = [
		'absolute',  // /home/public_html/user/wp-content/plugins/hello-dolly/hello.php
		'relative',  // hello-dolly/hello.php
		'dirname',   // hello-dolly
		'basename',  // hello.php
		'filename',  // hello
		'extension', // php
	];

	$path = pathinfo( $plugin );

	$dirname   = sanitize_file_name( $path['dirname'] ?? '' );
	$basename  = sanitize_file_name( $path['basename'] ?? '' );
	$extension = sanitize_file_name( $path['extension'] ?? '' );
	$filename  = sanitize_file_name( $path['filename'] ?? '' );

	$format = in_array( $format, $valid_formats, true ) ? $format : 'relative';

	if ( $format === 'absolute' ) {
		return trailingslashit( WP_PLUGIN_DIR ) . trailingslashit( $dirname ) . $basename;
	}

	if ( $format === 'dirname' ) {
		return $dirname;
	}

	if ( $format === 'basename' ) {
		return $basename;
	}

	if ( $format === 'filename' ) {
		return $filename;
	}

	if ( $format === 'extension' ) {
		return $extension;
	}

	return empty( $dirname ) ? $basename : trailingslashit( $dirname ) . $basename;
}

/**
 * Returns an array of Network IDs which should be considered inactive.
 *
 * @since 1.3.8
 *
 * @return array
 */
function dfrapi_inactive_networks(): array {

	$inactive_network_ids = [
		14, // Prophetably
	];

	return array_map( 'absint', apply_filters( 'dfrapi_inactive_networks', $inactive_network_ids ) );
}
