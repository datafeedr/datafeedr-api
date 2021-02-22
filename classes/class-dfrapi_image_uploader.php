<?php

//$post_data = new Dfrapi_Image_Post_Data();
//$uploader  = new Dfrapi_Image_Uploader( 'https://www.geargrabber.net/', $post_data );
//
//$uploader->upload();

class Dfrapi_Image_Uploader {

	/**
	 * @var string $image_url URL of external image to download and import into Media Library.
	 */
	public $image_url;

	/**
	 * @var Dfrapi_Image_Post_Data $post_data
	 */
	public $post_data;

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
	 * @param string $image_url
	 * @param Dfrapi_Image_Post_Data $post_data
	 */
	public function __construct( string $image_url, Dfrapi_Image_Post_Data $post_data ) {
		$this->image_url = $image_url;
		$this->post_data = $post_data;
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

		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		// Maybe set as featured image.
		if ( $featured ) {
			set_post_thumbnail( $this->post_data->get_post_parent_id(), $attachment_id );
		}

		// Maybe update alternative text field.
		if ( $this->post_data->get_alternative_text() ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $this->post_data->get_alternative_text() ) );
		}

		return $attachment_id;
	}

	/**
	 * @return int|WP_Error The ID of the attachment or a WP_Error on failure.
	 */
	private function sideload_image() {

		// @todo be sure to handle unlinking the original file.
		$tmp_name = download_url( $this->image_url, $this->timeout );

		if ( is_wp_error( $tmp_name ) ) {
			return $tmp_name;
		}

		$mime_type = wp_get_image_mime( $tmp_name );

		if ( ! $mime_type ) {
			return new WP_Error(
				'mime_type_indeterminable',
				__( 'The true mime type cannot be determined for this image.', 'datafeedr-api' ),
				[ 'Dfrapi_Image_Uploader' => $this ]
			);
		}

		if ( ! in_array( $mime_type, array_keys( $this->extensions() ) ) ) {
			return new WP_Error(
				'mime_type_invalid',
				sprintf( __( 'The mime type "%s" is not a valid mime type for an image.', 'datafeedr-api' ), esc_html( $mime_type ) ),
				[ 'Dfrapi_Image_Uploader' => $this ]
			);
		}

		$extension = $this->extensions()[ $mime_type ];

		$this->mime_type = $mime_type;
		$this->extension = $extension;

		add_filter( 'wp_check_filetype_and_ext', [ $this, 'set_missing_extension_or_mime_type' ] );

		$filename = ( is_null( $this->post_data->get_filename() ) ) ? ( $tmp_name ) : $this->post_data->get_filename();
		$title    = ( is_null( $this->post_data->get_title() ) ) ? ( $tmp_name ) : $this->post_data->get_title();

		$attachment_id = media_handle_sideload(
			[
				'name'     => $this->post_data->get_filename( $tmp_name ) . '.' . $extension,
				'tmp_name' => $tmp_name,
			],
			$this->post_data->get_post_parent_id(),
			$this->post_data->get_title(),
			$this->post_data->get_post_array()
		);

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
}
