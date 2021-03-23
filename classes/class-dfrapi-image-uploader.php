<?php

class Dfrapi_Image_Uploader {

	/**
	 * @var Dfrapi_Image_Data $image_data
	 */
	public $image_data;

	/**
	 * @var int $timeout The amount of time in seconds to wait for an image to be downloaded.
	 */
	public $timeout = 5;

	/**
	 * @var string $mime_type The image's mime type.
	 */
	public $mime_type;

	/**
	 * @var string $mime_type The image's extension.
	 */
	public $extension;

	/**
	 * Dfrapi_Image_Uploader constructor.
	 *
	 * @param Dfrapi_Image_Data $image_data
	 */
	public function __construct( Dfrapi_Image_Data $image_data ) {
		$this->image_data = $image_data;
	}

	/**
	 * Uploads an image from the given $this->image_url and return its Attachment ID on success
	 * or a WP_Error on failure.
	 *
	 * @return int|WP_Error The ID of the attachment or a WP_Error on failure.
	 */
	public function upload() {

		require_once( ABSPATH . 'wp-includes/pluggable.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$attachment_id = $this->sideload_image();

		// If sideload returned a WP_Error, add some extra data to the error object and return it.
		if ( is_wp_error( $attachment_id ) ) {
			$attachment_id->add_data( $this->image_data->get_image_url(), 'image_url' );
			$attachment_id->add_data( $this->image_data->get_post_parent_id(), 'post_parent' );
			$attachment_id->add_data( $this->image_data->get_title(), 'post_title' );

			return $attachment_id;
		}

		// Maybe set as post thumbnail.
		if ( $this->image_data->is_post_thumbnail() ) {
			set_post_thumbnail( $this->image_data->get_post_parent_id(), $attachment_id );
		}

		// Maybe update alternative text field.
		if ( $this->image_data->get_alternative_text() ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $this->image_data->get_alternative_text() ) );
		}

		return $attachment_id;
	}

	/**
	 * Upload the image and if successful, import into Media Library.
	 *
	 * This method will make 2 or 3 attempts to download the image.
	 *
	 * First Attempt: Try to download the image with the standard HTTP requests args.
	 * Second Attempt: Try to download the image with the modified HTTP requests args.
	 * Third Attempt: If Jetpack is activated, try to download the image using a Jetpack Photon URL.
	 *
	 * If all 3 attempts fail, return a WP_Error.
	 *
	 * @return int|WP_Error The ID of the attachment or a WP_Error on failure.
	 */
	private function sideload_image() {

		// This is our first attempt to download the image (without modified HTTP request args).
		$tmp_name = $this->download_url( $this->image_data->get_image_url() );

		// If our first attempt to download the image URL fails, try again with $with_request_filters set to true.
		if ( is_wp_error( $tmp_name ) ) {
			$tmp_name = $this->download_url( $this->image_data->get_image_url(), true );
		}

		// This is our last attempt to download image using a Jetpack Photon URL (if Jetpack is active).
		if ( is_wp_error( $tmp_name ) ) {
			$photon_image_url = dfrapi_jetpack_photon_url( $this->image_data->get_image_url() );

			// Make sure the photon image URL is different than the URL of the image we are attempting to download.
			if ( $photon_image_url !== $this->image_data->get_image_url() ) {
				$tmp_name = $this->download_url( $photon_image_url );
			}
		}

		// If $tmp_name is still a WP_Error then bail, for some reason we can't download this image.
		if ( is_wp_error( $tmp_name ) ) {
			return $tmp_name;
		}

		$mime_type = wp_get_image_mime( $tmp_name );

		if ( ! $mime_type ) {
			$this->unlink_tmp_file( $tmp_name );

			return new WP_Error(
				'mime_type_indeterminable',
				__( 'The true mime type cannot be determined for this image.', 'datafeedr-api' ),
				[
					'$image_url'   => $this->image_data->get_image_url(),
					'$post_parent' => $this->image_data->get_post_parent_id(),
				]
			);
		}

		if ( ! array_key_exists( $mime_type, $this->mime_types_and_extensions() ) ) {
			$this->unlink_tmp_file( $tmp_name );

			return new WP_Error(
				'mime_type_invalid',
				sprintf( __( 'The mime type "%s" is not a valid mime type for an image.', 'datafeedr-api' ), esc_html( $mime_type ) ),
				[
					'$image_url'   => $this->image_data->get_image_url(),
					'$post_parent' => $this->image_data->get_post_parent_id(),
				]
			);
		}

		$this->mime_type = $mime_type;
		$this->extension = $this->mime_types_and_extensions()[ $mime_type ];

		add_filter( 'wp_check_filetype_and_ext', [ $this, 'set_missing_extension_or_mime_type' ] );

		$attachment_id = media_handle_sideload(
			[
				'name'     => $this->image_data->get_filename() . '.' . $this->extension,
				'tmp_name' => $tmp_name,
			],
			$this->image_data->get_post_parent_id(),
			$this->image_data->get_title(),
			$this->image_data->get_post_array()
		);

		$this->unlink_tmp_file( $tmp_name );

		remove_filter( 'wp_check_filetype_and_ext', [ $this, 'set_missing_extension_or_mime_type' ] );

		return $attachment_id;
	}

	/**
	 * This is a wrapper for the download_url() function. If $with_request_filters is enabled, that
	 * means we will add filters to the "http_request_args" in order to change the request
	 * with the hopes of bypassing any access controls (403) permission issues.
	 *
	 * @param string $image_url The image URL to download.
	 * @param false $with_request_filters Set to true to modify HTTP request args before request.
	 *
	 * @return string|WP_Error The name of the temporary file or WP_Error on failure.
	 */
	public function download_url( string $image_url, $with_request_filters = false ) {

		add_filter( 'wp_unique_filename', [ $this, 'force_filename_length_limit' ] );

		if ( $with_request_filters ) {
			add_filter( 'http_request_args', [ $this, 'modify_http_request_args' ], 10, 2 );
		}

		$tmp_name = download_url( $image_url, $this->timeout );

		if ( $with_request_filters ) {
			remove_filter( 'http_request_args', [ $this, 'modify_http_request_args' ] );
		}

		remove_filter( 'wp_unique_filename', [ $this, 'force_filename_length_limit' ] );

		return $tmp_name;
	}

	/**
	 * Modify the arguments sent in the HTTP request to download the image.
	 *
	 * These are used in the WP_Http::request() method which is called by the download_url() function.
	 *
	 * @param array $default_args
	 * @param string $url
	 *
	 * @return array
	 */
	public function modify_http_request_args( array $default_args, string $url ) {

		$custom_args = [
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0',
			'stream'     => true,
			'sslverify'  => false,
		];

		$custom_args = apply_filters(
			'dfrapi_image_uploader_custom_http_request_args',
			$custom_args,
			$default_args,
			$url,
			$this
		);

		return array_merge( $default_args, $custom_args );
	}

	/**
	 * Sets the image's extension or mime type if either are missing.
	 *
	 * This is required for processing image URLs which aren't formatted with a traditional
	 * extension such as .jpg or .png. For example, if the image URL uses a query string or is
	 * just a slug. In these cases, WordPress's wp_check_filetype() function will fail to
	 * get the uploaded image's extension and mime type which forces the _wp_handle_upload()
	 * (which is ultimately called by media_handle_sideload()) function to fail and return
	 * a 'Sorry, this file type is not permitted for security reasons.' WP_Error.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function set_missing_extension_or_mime_type( $params ) {

		if ( ! empty( $params['ext'] ) && ! empty( $params['type'] ) ) {
			return $params;
		}

		$params['ext']  = empty( $params['ext'] ) ? $this->extension : $params['ext'];
		$params['type'] = empty( $params['type'] ) ? $this->mime_type : $params['type'];

		return $params;
	}

	/**
	 * Set the amount of time to process download_url() function.
	 *
	 * @param int $timeout
	 */
	public function set_timeout( $timeout = 5 ) {
		$this->timeout = absint( $timeout );
	}

	/**
	 * An array of image mime types and their respective extensions.
	 *
	 * @return array
	 */
	private function mime_types_and_extensions() {
		return apply_filters( 'dfrapi_image_uploader_mime_types_and_extensions', [
			'image/jpeg'   => 'jpg',
			'image/jpg'    => 'jpg',
			'image/jpe'    => 'jpg',
			'image/png'    => 'png',
			'image/gif'    => 'gif',
			'image/bmp'    => 'bmp',
			'image/tiff'   => 'tif',
			'image/webp'   => 'webp',
			'image/x-icon' => 'ico',
			'image/heic'   => 'heic',
		], $this );
	}

	/**
	 * Safely unlink a temporary file.
	 *
	 * @param string $tmp_name
	 */
	public function unlink_tmp_file( string $tmp_name ) {
		@unlink( $tmp_name );
	}

	/**
	 * When filenames are longer than 255 characters, this causes a "failed to open stream: File name too long" error.
	 * This filter function shortens the unique name generated by WordPress before the file is stored in the
	 * temporary directory.
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public function force_filename_length_limit( string $filename ) {
		$length = absint( apply_filters( 'dfrapi_image_uploader_filename_max_length', 200, $this ) );

		return substr( $filename, 0, $length );
	}
}
