<?php

class Dfrapi_Image_Data {

	/**
	 * @var string $image_url The URL of the image to import into the Media Library.
	 */
	private $image_url;

	/**
	 * @var string $filename Name of the image file.
	 */
	private $filename = '';

	/**
	 * @var string $post_title Maps to Media Title field.
	 */
	private $post_title = '';

	/**
	 * @var string $post_content Maps to Media Description field.
	 */
	private $post_content = '';

	/**
	 * @var string $alternative_text Maps to postmeta "_wp_attachment_image_alt" field.
	 */
	private $alternative_text = '';

	/**
	 * @var string $post_excerpt Maps to Media Caption field.
	 */
	private $post_excerpt = '';

	/**
	 * @var int $post_author Maps to Media Author ID.
	 */
	private $post_author = 0;

	/**
	 * @var int $post_parent The Post ID the media is associated with.
	 */
	private $post_parent = 0;

	/**
	 * Dfrapi_Image_Data constructor.
	 *
	 * @param string $image_url
	 */
	public function __construct( string $image_url ) {
		$this->set_image_url( $image_url );
	}

	public function get_image_url() {
		return $this->image_url;
	}

	public function get_filename() {
		return ! empty( $this->filename ) ? $this->filename : $this->get_original_filename();
	}

	public function get_title() {
		return ! empty( $this->post_title ) ? $this->post_title : $this->get_original_filename();
	}

	public function get_description() {
		return ! empty( $this->post_content ) ? $this->post_content : '';
	}

	public function get_caption() {
		return ! empty( $this->post_excerpt ) ? $this->post_excerpt : '';
	}

	public function get_author_id() {
		return absint( $this->post_author );
	}

	public function get_post_parent_id() {
		return absint( $this->post_parent );
	}

	public function get_alternative_text() {
		return ! empty( $this->alternative_text ) ? $this->alternative_text : '';
	}

	public function set_image_url( string $image_url ) {
		$this->image_url = trim( $image_url );
	}

	public function set_filename( string $filename ) {
		$this->filename = trim( $filename );
	}

	public function set_title( string $title ) {
		$this->post_title = trim( $title );
	}

	public function set_description( string $content ) {
		$this->post_content = trim( $content );
	}

	public function set_caption( string $excerpt ) {
		$this->post_excerpt = trim( $excerpt );
	}

	public function set_author_id( int $author_id ) {
		$this->post_author = absint( $author_id );
	}

	public function set_post_parent_id( int $post_parent_id ) {
		$this->post_parent = absint( $post_parent_id );
	}

	public function set_alternative_text( string $alternative_text ) {
		$this->alternative_text = trim( $alternative_text );
	}

	/**
	 * Return an array structured similar to a WP_Post array.
	 *
	 * @return array
	 */
	public function get_post_array() {
		return [
			'post_title'   => $this->get_title(),
			'post_content' => $this->get_description(),
			'post_excerpt' => $this->get_caption(),
			'post_author'  => $this->get_author_id(),
			'post_parent'  => $this->get_post_parent_id(),
		];
	}

	/**
	 * Returns just the name of the image without an extension, paths, domain name, etc...
	 *
	 * Examples:
	 *
	 * Original: https://images.asos-media.com/products/new-look-snake-print-v-shaped-bikini-bottoms-in-bright-yellow/11880433-1-brightyellow?$XXLrmbnrbtm$
	 * Filename: 11880433-1-brightyellow
	 *
	 * Original: https://www.rei.com/media/3c8c2c5f-5c2c-4319-b536-1a9caefb8514
	 * Filename: 3c8c2c5f-5c2c-4319-b536-1a9caefb8514
	 *
	 * Original: https://www.patagonia.com/dw/image/v2/BDJB_PRD/on/demandware.static/-/Sites-patagonia-master/default/dwa72917ec/images/hi-res/11193_950.jpg?sw=1000&sh=1000&sfrm=png&q=95&bgcolor=f6f6f6
	 * Filename: 11193_950
	 *
	 * @return string
	 */
	public function get_original_filename() {
		$path = parse_url( $this->get_image_url(), PHP_URL_PATH );

		return pathinfo( $path, PATHINFO_FILENAME );
	}
}
