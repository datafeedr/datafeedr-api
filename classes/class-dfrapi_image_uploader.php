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
	 * @param boolean $featured Optional. Set to true to set this image as the post parent's featured image. Default true.
	 *
	 * @return int|WP_Error The ID of the attachment or a WP_Error on failure.
	 */
	public function upload( $featured = true ) {

		require_once( ABSPATH . 'wp-includes/pluggable.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$attachment_id = $this->sideload_image();

		// If sideload returned a WP_Error, return it.
		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		// Maybe set as featured image.
		if ( $featured ) {
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
	 * @return int|WP_Error The ID of the attachment or a WP_Error on failure.
	 */
	private function sideload_image() {

		$tmp_name = download_url( $this->image_data->get_image_url(), $this->timeout );

		if ( is_wp_error( $tmp_name ) ) {
			$this->unlink_tmp_file( $tmp_name );

			return $tmp_name;
		}

		$mime_type = wp_get_image_mime( $tmp_name );

		error_log( '$mime_type' . ': ' . print_r( $mime_type, true ) );
		error_log( 'getimagesize($tmp_name)' . ': ' . print_r( getimagesize( $tmp_name ), true ) );

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

		if ( ! in_array( $mime_type, array_keys( $this->extensions() ) ) ) {
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


		$extension = $this->extensions()[ $mime_type ];

		$this->mime_type = $mime_type;
		$this->extension = $extension;

		add_filter( 'wp_check_filetype_and_ext', [ $this, 'set_missing_extension_or_mime_type' ] );

		$attachment_id = media_handle_sideload(
			[
				'name'     => $this->image_data->get_filename() . '.' . $extension,
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
	 * Set the amount of time to process download_url() function.
	 *
	 * @param int $timeout
	 */
	public function set_timeout( $timeout = 5 ) {
		$this->timeout = absint( $timeout );
	}

	/**
	 * Sets the image's missing extension or mime type if they are missing.
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
	 * An array of image mime types and their respective extensions.
	 *
	 * @return array
	 */
	private function extensions() {

		// @todo use wp_get_ext_types() and wp_get_mime_types()

		return [
			'image/jpeg' => 'jpg',
			'image/jpg'  => 'jpg',
			'image/jpe'  => 'jpg',
			'image/png'  => 'png',
			'image/gif'  => 'gif',
			'image/bmp'  => 'bmp',
			'image/tiff' => 'tif',
			'image/webp' => 'webp',
		];
	}

	/**
	 * Safely unlink a temporary file.
	 *
	 * @param string $tmp_name
	 */
	public function unlink_tmp_file( string $tmp_name ) {
		@unlink( $tmp_name );
	}
}
