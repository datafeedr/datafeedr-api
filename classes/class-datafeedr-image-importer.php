<?php

/**
 * Class Datafeedr_Image_Importer.
 *
 * Handles importing images from URLs and assigning image data.
 *
 * Usage:
 *
 * $url = 'http://via.placeholder.com/350x150';
 *
 * $args = array(
 *      'title'             => 'My Image Name',
 *      'file_name'         => 'my-image-name',
 *      'post_id'           => 123,
 *      'description'       => 'Description of this image.',
 *      'caption'           => 'Caption for this image.',
 *      'alt_text'          => 'Alt text for this image.',
 *      'user_id'           => 1,
 *      'is_post_thumbnail' => true,
 *      'user_agent'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0',
 *      'timeout'           => 30,
 * );
 *
 * $image = new Datafeedr_Image_Importer( $url, $args );
 *
 * $attachment_id = $image->import();
 *
 * if ( is_wp_error( $attachment_id ) ) {
 *      // Handle error.
 * } else {
 *      // Success! Do something with $attachment_id.
 * }
 *
 * @since 1.0.71
 */
class Datafeedr_Image_Importer {

	use Datafeedr_Timer;

	/**
	 * Image URL.
	 *
	 * @since 1.0.71
	 * @access protected
	 * @var string $url
	 */
	protected $url;

	/**
	 * An array of arguments for storing image in database.
	 *
	 * @see $this->default_args()
	 * @since 1.0.71
	 * @access protected
	 * @var array $args
	 */
	protected $args;

	/**
	 *  The response of wp_remote_get( $this->url ) or WP_Error on failure.
	 *
	 * @since 1.0.71
	 * @access protected
	 * @var WP_Error|array $response
	 */
	protected $response;

	/**
	 * The ID created for the new attachment.
	 *
	 * @since 1.0.71
	 * @access protected
	 * @var integer $attachment_id
	 */
	protected $attachment_id;

	/**
	 * A copy of the WP_Error encountered during import.
	 *
	 * @since 1.0.74
	 * @access protected
	 * @var WP_Error $wp_error
	 */
	protected $wp_error = null;

	/**
	 * Datafeedr_Image_Importer constructor.
	 *
	 * Sets the $this->url and $this->args properties.
	 *
	 * @param string $url Image URL.
	 * @param array $args Optional. See $this->default_args().
	 *
	 * @since 1.0.71
	 */
	public function __construct( $url, $args = array() ) {
		$this->set_url( $url );
		$this->set_args( $args );
	}

	/**
	 * Sets the $this->url property.
	 *
	 * @param string $url Image URL.
	 *
	 * @since 1.0.71
	 */
	protected function set_url( $url ) {
		$url       = ( "//" === substr( $url, 0, 2 ) ) ? 'http:' . $url : $url;
		$this->url = apply_filters( 'datafeedr_image_importer/url', $url, $this );
	}

	/**
	 * Sets the $this->args property.
	 *
	 * This merges the $defaults with the values passed into the constructor.
	 *
	 * @param array $args
	 *
	 * @since 1.0.71
	 */
	protected function set_args( $args ) {
		$args       = wp_parse_args( $args, $this->default_args() );
		$this->args = apply_filters( 'datafeedr_image_importer/args', $args, $this );
	}

	/**
	 * Default args to use for importing image.
	 *
	 * @return array
	 * @since 1.0.71
	 */
	protected function default_args() {

		$args = array(

			/**
			 * This is the ID of the post we want to attach this image to. If we do not
			 * want this image to be attached to a post, leave this set to 0.
			 */
			'post_id'           => 0,

			/**
			 * This is name of the file name the image will have once it is stored on
			 * on the server in the WordPress uploads directory.
			 */
			'file_name'         => preg_replace( '/\.[^.]+$/', '', basename( $this->url ) ),

			/**
			 * This is the ID of the User this image will be associated with.
			 */
			'user_id'           => $this->get_admin_user_id(),

			/**
			 * This is the title of the image (which is different than the file name).
			 */
			'title'             => preg_replace( '/\.[^.]+$/', '', basename( $this->url ) ),

			/**
			 * The description of the image.
			 */
			'description'       => '',

			/**
			 * The caption for the image.
			 */
			'caption'           => '',

			/**
			 * The alt text. Text to display if image cannot be loaded.
			 */
			'alt_text'          => '',

			/**
			 * Whether this image should be set as the post's thumbail. If the post_id
			 * is 0, this setting will be ignored.
			 */
			'is_post_thumbnail' => false,

			/**
			 * The user-agent to use when making requests to the external URL.
			 */
			'user_agent'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0',

			/**
			 * The number of seconds to spend attempting to download the image.
			 */
			'timeout'           => 10,
		);

		return $args;
	}

	/**
	 * Returns the image URL.
	 *
	 * @return string
	 * @since 1.0.71
	 */
	public function url() {
		return $this->url;
	}

	/**
	 * Returns the image's attachment ID.
	 *
	 * @return integer
	 * @since 1.0.71
	 */
	public function attachment_id() {
		return absint( $this->attachment_id );
	}

	/**
	 * Returns the response of wp_remote_get($this->url) or WP_Error on failure.
	 *
	 * @return array|WP_Error
	 * @since 1.0.71
	 */
	public function response() {
		return $this->response;
	}

	/**
	 * Returns a WP_Error encountered during the import() process.
	 *
	 * @return WP_Error|null
	 * @since 1.0.74
	 */
	public function wp_error() {
		return $this->wp_error;
	}

	/**
	 * Returns all args or a value of a specific arg.
	 *
	 * @param bool|string $key
	 * @param string $cast Possible values: boolean, integer, float, string, array, object or null. See settype().
	 *
	 * @return array|mixed Return all $args if $key doesn't exists or a single arg if $key isset.
	 * @since 1.0.74
	 */
	public function args( $key = false, $cast = 'string' ) {

		$args = $this->args;

		if ( $key && isset( $args[ $key ] ) ) {
			$value = $args[ $key ];
			settype( $value, $cast );

			return $value;
		}

		return $args;
	}

	/**
	 * Returns true if errors were encountered. Otherwise returns false.
	 *
	 * @return bool
	 * @since 1.0.74
	 */
	public function has_error() {
		return ( is_wp_error( $this->wp_error() ) ) ? true : false;
	}

	/**
	 * This handles the importing of an image into WordPress.
	 *
	 * This performs the following actions:
	 *  - Requires necessary WordPress media.php, $file.php and image.php files
	 *  - Performs a wp_remote_get() request to the image URL.
	 *  - Extracts the image extension (.jpg, .png, etc...) for the image.
	 *  - Downloads the image file and stores a temporary copy.
	 *  - Adds the image to the WordPress uploads directory and saves a record to the posts table.
	 *  - Sets the $this->attachment_id property
	 *  - Maybe sets the image as the post's featured thumbnail.
	 *  - Maybe sets the image's alt text.
	 *
	 * If the import encounters an error, a WP_Error will be added to the $this->errors array.
	 *
	 * @return Datafeedr_Image_Importer
	 * @since 1.0.71
	 */
	public function import() {

		$this->start_timer();

		$this->require_files();

		$tmp_name = $this->tmp_name();

		if ( is_wp_error( $tmp_name ) ) {

			$this->wp_error = $tmp_name;

			/**
			 * Do something when image import fails.
			 *
			 * @param WP_Error $tmp_name
			 * @param Datafeedr_Image_Importer $this
			 *
			 * @since 1.0.72
			 *
			 */
			do_action( 'datafeedr_image_importer/import/fail', $tmp_name, $this );

			$this->stop_timer();

			return $this;
		}

		$content_type = $this->response_header( $this->response(), 'content-type' );

		$extension = $this->content_type_to_extension( $content_type );

		$file_array             = array();
		$file_array['name']     = $this->args( 'file_name' ) . '.' . $extension;
		$file_array['tmp_name'] = $tmp_name;

		if ( is_wp_error( $file_array['tmp_name'] ) ) {

			$this->wp_error = $file_array['tmp_name'];

			/**
			 * Do something when image import fails.
			 *
			 * @param WP_Error $file_array ['tmp_name']
			 * @param Datafeedr_Image_Importer $this
			 *
			 * @since 1.0.72
			 *
			 */
			do_action( 'datafeedr_image_importer/import/fail', $file_array['tmp_name'], $this );

			$this->stop_timer();

			return $this;
		}

		$result = $this->media_sideload_image(
			$file_array,
			$this->args( 'post_id', 'integer' ),
			$this->args( 'title' ),
			$this->post_data()
		);

		if ( is_wp_error( $result ) ) {

			$this->wp_error = $result;

			$this->unlink_tmp_file( $file_array['tmp_name'] );

			/**
			 * Do something when image import fails.
			 *
			 * @param WP_Error $result
			 * @param Datafeedr_Image_Importer $this
			 *
			 * @since 1.0.72
			 *
			 */
			do_action( 'datafeedr_image_importer/import/fail', $result, $this );

			$this->stop_timer();

			return $this;
		}

		$this->set_attachment_id( $result );
		$this->set_post_thumbnail();
		$this->set_alt_text();

		/**
		 * Do something when image import succeeds.
		 *
		 * @param int $result Attachment ID.
		 * @param Datafeedr_Image_Importer $this
		 *
		 * @since 1.0.72
		 */
		do_action( 'datafeedr_image_importer/import/success', $result, $this );

		$this->stop_timer();

		return $this;
	}

	/**
	 * Returns an array of Post data to use in the call to media_sideload_image().
	 *
	 * This sets the image description, caption and user_id.
	 *
	 * @return array Post data.
	 * @since 1.0.71
	 */
	protected function post_data() {

		$post_data = array(
			'post_content' => $this->args( 'description' ),
			'post_excerpt' => $this->args( 'caption' ),
			'post_author'  => $this->args( 'user_id', 'integer' ),
		);

		/**
		 * Allow user to override post data.
		 *
		 * @param array $post_data
		 * @param Datafeedr_Image_Importer $this
		 *
		 * @since 1.0.72
		 *
		 */
		return apply_filters( 'datafeedr_image_importer/post_data', $post_data, $this );
	}

	/**
	 * Sets the image's alt text if alt text was provided.
	 *
	 * @since 1.0.71
	 */
	protected function set_alt_text() {
		$text = $this->args( 'alt_text' );
		if ( ! empty( $text ) ) {
			update_post_meta( $this->attachment_id(), '_wp_attachment_image_alt', $text );
		}
	}

	/**
	 * Sets the image as the post thumbnail if the is_post_thumbnail argument is set to
	 * true and the post_id is greater than 0.
	 *
	 * @since 1.0.71
	 */
	protected function set_post_thumbnail() {

		if ( ! $this->args( 'is_post_thumbnail', 'boolean' ) ) {
			return;
		}

		if ( $this->args( 'post_id', 'integer' ) < 1 ) {
			return;
		}

		set_post_thumbnail( $this->args( 'post_id', 'integer' ), $this->attachment_id() );
	}

	/**
	 * Requires the necessary files for importing an image into WordPress.
	 *
	 * @since 1.0.71
	 */
	protected function require_files() {
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	}

	/**
	 * A wrapper for the media_handle_sideload() function.
	 *
	 * @param array $file_array
	 * @param int $post_id
	 * @param string $title
	 * @param array $post_data
	 *
	 * @return int|WP_Error The ID of the attachment or a WP_Error on failure.
	 * @see media_handle_sideload()
	 *
	 * @since 1.0.71
	 */
	protected function media_sideload_image( $file_array, $post_id = 0, $title = '', $post_data = array() ) {

		$attachment_id = media_handle_sideload( $file_array, $post_id, $title, $post_data );

		// If there was an error sideloading the image, return the returned WP_Error.
		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		// If the image does not have a width or height, return WP_Error.
		$meta = wp_get_attachment_metadata( $attachment_id );
		if ( ! isset( $meta['width'], $meta['height'] ) ) {
			return new WP_Error( 'http_no_image_width_or_height', __( 'Image data does not exist.', 'datafeedr' ) );
		}

		// If we made it this far, the image is valid so return the image's attachment ID.
		return $attachment_id;
	}

	/**
	 * This is a modified version of download_url() from /wp-admin/includes/file.php
	 *
	 * We had to modify so we could change the user-agent.
	 *
	 * Downloads a URL to a local temporary file using the WordPress HTTP Class.
	 * Please note, That the calling function must unlink() the file.
	 *
	 * This also sets the $this->response property.
	 *
	 * @return WP_Error|string WP_Error on failure, string temporary filename on success.
	 * @since 1.0.72
	 *
	 * @see /wp-admin/includes/file.php:965 function download_url( $url, $timeout = 300 )
	 */
	protected function tmp_name() {

		$url = $this->url();

		if ( ! $url ) {
			return new WP_Error( 'http_no_url', __( 'Invalid URL Provided.' ) );
		}

		$url_filename = basename( parse_url( $url, PHP_URL_PATH ) );

		$url_filename = substr( $url_filename, 0, 100 );

		$tmp_name = wp_tempnam( $url_filename );

		if ( ! $tmp_name ) {
			return new WP_Error( 'http_no_file', __( 'Could not create Temporary file.' ) );
		}

		$this->set_response( $tmp_name );

		if ( is_wp_error( $this->response() ) ) {
			$this->unlink_tmp_file( $tmp_name );

			return $this->response();
		}

		$response_code = $this->response_code( $this->response() );

		if ( 200 != $response_code ) {
			$this->unlink_tmp_file( $tmp_name );

			return new WP_Error(
				'http_' . $response_code,
				__( 'Response code was something other than 200.', 'datafeedr' )
			);
		}

		$content_md5 = $this->response_header( $this->response(), 'content-md5' );

		if ( $content_md5 ) {
			$md5_check = verify_file_md5( $tmp_name, $content_md5 );
			if ( is_wp_error( $md5_check ) ) {
				$this->unlink_tmp_file( $tmp_name );

				return $md5_check;
			}
		}

		return $tmp_name;
	}

	/**
	 * Sets the $this->attachment_id property.
	 *
	 * @param integer $id The ID to set the attachment_id to.
	 *
	 * @since 1.0.71
	 */
	protected function set_attachment_id( $id ) {
		$this->attachment_id = absint( $id );
	}

	/**
	 * Safely unlink a temporary file.
	 *
	 * @param string $file_name Name of temporary file to unlink.
	 *
	 * @since 1.0.72
	 */
	protected function unlink_tmp_file( $file_name ) {
		@unlink( $file_name );
	}

	/**
	 * Sets the $this->response property.
	 *
	 * Makes a call to wp_remote_get() and sets the $this->response property to the
	 * returned value.
	 *
	 * @param string $tmp_name Temporary file name generated by wp_tempnam().
	 *
	 * @since 1.0.71
	 */
	protected function set_response( $tmp_name ) {

		$args = array(
			'user-agent' => $this->args( 'user_agent' ),
			'timeout'    => $this->args( 'timeout', 'integer' ),
			'filename'   => $tmp_name,
			'stream'     => true,
		);

		/**
		 * Allow user to override $args.
		 *
		 * @param array $args
		 * @param Datafeedr_Image_Importer $this
		 *
		 * @since 1.0.72
		 *
		 */
		$args = apply_filters( 'datafeedr_image_importer/set_response/args', $args, $this );

		$this->response = wp_safe_remote_get( $this->url, $args );
	}

	/**
	 * Retrieve a single header by name from the raw response.
	 *
	 * @param $response WP_Error|array The response or WP_Error on failure.
	 * @param string $header Header name to retrieve value from.
	 *
	 * @return string The header value. Empty string on if incorrect parameter given, or if the header doesn't exist.
	 * @since 1.0.72
	 */
	protected function response_header( $response, $header ) {
		return wp_remote_retrieve_header( $response, $header );
	}

	/**
	 * Retrieve only the response message from the raw response.
	 *
	 * Will return an empty array if incorrect parameter value is given.
	 *
	 * @param $response WP_Error|array The response or WP_Error on failure.
	 *
	 * @return string The response message. Empty string on incorrect parameter given.
	 * @since 1.0.72
	 */
	protected function response_message( $response ) {
		return wp_remote_retrieve_response_message( $response );
	}

	/**
	 * Retrieve only the response code from the raw response.
	 *
	 * Will return an empty array if incorrect parameter value is given.
	 *
	 * @param $response WP_Error|array The response or WP_Error on failure.
	 *
	 * @return int|string The response code as an integer. Empty string on incorrect parameter given.
	 * @since 1.0.72
	 */
	protected function response_code( $response ) {
		return wp_remote_retrieve_response_code( $response );
	}

	/**
	 * This returns the proper image extension given the $content_type requested.
	 *
	 * Handle content types of values like these:
	 *      "image/png"
	 *      "image/jpeg;charset=UTF-8"
	 *      "JPG"
	 *
	 * @param string $content_type Content type received from HTTP request.
	 *
	 * @return string Extension (ex. jpg, png, gif, etc...).
	 * @since 1.0.71
	 */
	protected function content_type_to_extension( $content_type ) {

		$type = explode( ";", $content_type );
		$type = trim( strtolower( $type[0] ) );

		$extensions = $this->extensions();

		foreach ( $extensions as $mime => $ext ) {
			if ( $mime == $type ) {
				return $ext;
			}
		}

		return 'jpg';
	}

	/**
	 * Returns an associative array of valid content types (or mime types) and
	 * their respective extensions.
	 *
	 * @return array
	 * @since 1.0.71
	 */
	protected function extensions() {

		$extensions = array(
			'image/jpeg' => 'jpg',
			'image/jpg'  => 'jpg',
			'image/jpe'  => 'jpg',
			'image/png'  => 'png',
			'image/gif'  => 'gif',
			'image/bmp'  => 'bmp',
			'image/tiff' => 'tif',
			'image/webp' => 'webp',
			'jpg'        => 'jpg',
			'png'        => 'png',
			'gif'        => 'gif',
		);

		/**
		 * Allow user to override $extensions.
		 *
		 * @param array $extensions
		 * @param Datafeedr_Image_Importer $this
		 *
		 * @since 1.0.72
		 *
		 */
		return apply_filters( 'datafeedr_image_importer/extensions', $extensions, $this );
	}

	/**
	 * Returns the first found ID of an admin user.
	 *
	 * @return int
	 * @since 1.0.71
	 */
	protected function get_admin_user_id() {

		$args = array(
			'blog_id'  => $GLOBALS['blog_id'],
			'role__in' => array( 'administrator' ),
			'orderby'  => 'ID',
			'order'    => 'ASC',
			'number'   => 1,
		);

		$users = get_users( $args );
		$user  = $users[0];

		return absint( $user->ID );
	}
}
