<?php

class Dfrapi_Image_Post_Data {

	/**
	 * @var string $filename Name of the image file.
	 */
	private $filename;

	/**
	 * @var string $post_title Maps to Media Title field.
	 */
	private $post_title;

	/**
	 * @var string $post_content Maps to Media Description field.
	 */
	private $post_content;

	/**
	 * @var string $post_excerpt Maps to Media Caption field.
	 */
	private $post_excerpt;

	/**
	 * @var int $post_author Maps to Media Author ID.
	 */
	private $post_author;

	/**
	 * @var int THe Post ID the media is associated with.
	 */
	private $post_parent;

	/**
	 * @var string Maps to postmeta "_wp_attachment_image_alt" field.
	 */
	private $alternative_text;

	public function get_filename( string $default = '' ) {
		return ( is_string( $this->filename ) && ! empty( $this->filename ) ) ? ( $this->filename ) : $default;
	}

	public function get_title( string $default = '' ) {
		return ( is_string( $this->post_title ) && ! empty( $this->post_title ) ) ? $this->post_title : $default;
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
	 * Returns an array similar to a WP_Post array.
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
}
