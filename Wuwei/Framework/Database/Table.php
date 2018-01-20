<?php namespace Datafeedr\Api\Wuwei\Database;

// @todo Don't forget to handle Multisite DB issues - look into "Network: false" in the plugin header or a filter
// that prevents the plugin from being network activated.

/**
 * Class Database_Table
 *
 * A set of utilities for interacting with a database table.
 *
 * Example Usages:
 *
 *      Include with "use" keyword
 *          use Datafeedr\Api\Wuwei\Database\Table;
 *
 *      Instantiate Class (Do NOT include $wpdb->prefix).
 *          $table = new Table( 'datafeedr_networks' );
 *
 *      Check if database table exists.
 *          return ( $table->exists() ) ? true : false;
 *
 *      Create or Update a database table.
 *          $attributes   = array();
 *          $attributes[] = 'id INT(11) NOT NULL';
 *          $attributes[] = 'test_id INT(11) NOT NULL COMMENT "Just a comment about this table."';
 *          $attributes[] = 'updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
 *          $attributes[] = 'PRIMARY KEY  (id)';
 *          $attributes[] = 'KEY test_col (test_id)';
 *          $table->create( $attributes );
 *
 *      Drop table
 *          $table->drop();
 *
 *      Truncate (Empty) Table
 *          $table->truncate();
 *
 *      Drop an index
 *          $table->drop_index( 'test_col' );
 *
 *      Add an index
 *          $table->add_index( 'test_col' );
 *
 *      Add a column
 *          $table->add_column( 'deleted_at', 'TIMESTAMP NULL DEFAULT NULL AFTER updated_at' );
 *
 *      Drop a column
 *          $table->drop_column( 'deleted_at' );
 *
 * @since 2.0.0
 */
class Table {

	/**
	 * Name of the database table WITHOUT the WordPress prefix.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var string $table_name
	 */
	public $table_name;

	/**
	 * Table constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $table_name
	 */
	public function __construct( $table_name ) {
		$this->table_name = $table_name;
	}

	/**
	 * Returns the prefixed $table_name.
	 *
	 * @since 2.0.0
	 *
	 * @global \wpdb $wpdb
	 *
	 * @return string Prefixed table name.
	 */
	public function prefixed_table_name() {
		global $wpdb;

		return $wpdb->prefix . $this->table_name;
	}

	/**
	 * Returns the Charset Collate for CREATE statements.
	 *
	 * @since 2.0.0
	 *
	 * @global \wpdb $wpdb
	 *
	 * @return string Returns the charset collate value.
	 */
	private function get_charset_collate() {
		global $wpdb;

		return $wpdb->get_charset_collate();
	}

	/**
	 * Wrapper for dbDelta().
	 *
	 * @since 2.0.0
	 *
	 * @param string|array $queries Optional. The query to run. Can be multiple queries
	 *                              in an array, or a string of queries separated by
	 *                              semicolons. Default empty.
	 * @param bool $execute Optional. Whether or not to execute the query right away.
	 *                              Default true.
	 *
	 * @return array Strings containing the results of the various update queries.
	 */
	public function db_delta( $queries = '', $execute = true ) {
		$this->include_required_files();

		return dbDelta( $queries, $execute );
	}

	/**
	 * Check if the database table exists.
	 *
	 * Majority of code is from WordPress's maybe_create_table() function.
	 *
	 * @since 2.0.0
	 *
	 * @see maybe_create_table()
	 *
	 * @global \wpdb $wpdb
	 *
	 * @return bool True if table exists, else false.
	 */
	public function exists() {

		global $wpdb;

		$query = $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $this->prefixed_table_name() ) );

		if ( $wpdb->get_var( $query ) == $this->prefixed_table_name() ) {
			return true;
		}

		return false;
	}

	/**
	 * Will create or update database table.
	 *
	 * @since 2.0.0
	 *
	 * @param array $data
	 *
	 * @return array Strings containing the results of the various update queries.
	 */
	public function create( $data = [] ) {

		$data      = array_filter( $data );
		$table     = $this->prefixed_table_name();
		$char_coll = $this->get_charset_collate();

		$query = "CREATE TABLE " . $table . " (\n  ";
		$query .= implode( ",\n  ", $data );
		$query .= "\n) $char_coll ";

		$result = $this->db_delta( $query );

		return $result;
	}

	/**
	 * Drops a table if it exists.
	 *
	 * @since 2.0.0
	 *
	 * @global \wpdb $wpdb
	 *
	 * @return int|false Number of rows affected/selected or false on error
	 */
	public function drop() {
		global $wpdb;
		$sql    = "DROP TABLE IF EXISTS " . $this->prefixed_table_name();
		$result = $wpdb->query( $sql );

		return $result;
	}

	/**
	 * Truncates a table if it exists.
	 *
	 * @since 2.0.0
	 *
	 * @global \wpdb $wpdb
	 *
	 * @return int|false Number of rows affected/selected or false on error
	 */
	public function truncate() {

		global $wpdb;

		if ( ! $this->exists() ) {
			return 0;
		}

		$sql    = 'TRUNCATE TABLE ' . $this->prefixed_table_name();
		$result = $wpdb->query( $sql );

		return $result;
	}

	/**
	 * Wrapper for add_clean_index().
	 *
	 * @since 2.0.0
	 *
	 * @param string $index Database table index column.
	 *
	 * @return true True, when done with execution.
	 */
	public function add_index( $index ) {
		$result = add_clean_index( $this->prefixed_table_name(), $index );

		return $result;
	}

	/**
	 * Wrapper for drop_index().
	 *
	 * @since 2.0.0
	 *
	 * @param string $index Index name to drop.
	 *
	 * @return true True, when finished.
	 */
	public function drop_index( $index ) {
		$result = drop_index( $this->prefixed_table_name(), $index );

		return $result;
	}

	/**
	 * Adds column to a database table if it doesn't already exist.
	 *
	 * @since 2.0.0
	 *
	 * @param string $column_name The column name to add to the table.
	 *      Example: "deleted_at"
	 *      Example: "name"
	 * @param string $column_query The SQL statement used to add the column. Should include the column name.
	 *      Example: "TIMESTAMP NULL DEFAULT NULL AFTER updated_at"
	 *      Example: "varchar(10) NOT NULL"
	 *
	 * @return bool True if already exists or on successful completion, false on error.
	 */
	public function add_column( $column_name, $column_query ) {

		$this->include_required_files();

		/**
		 * Generates SQL which looks like this:
		 *
		 * ALTER TABLE wp_table_name ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_at;
		 */
		$sql = sprintf(
			'ALTER TABLE %1$s ADD COLUMN %2$s %3$s',
			$this->prefixed_table_name(),
			$column_name,
			$column_query
		);

		$result = maybe_add_column( $this->prefixed_table_name(), $column_name, $sql );

		return $result;
	}

	/**
	 * Drops a column from a table.
	 *
	 * @since 2.0.0
	 *
	 * @param string $column_name
	 *
	 * @return bool False on failure, true on success or doesn't exist.
	 */
	public function drop_column( $column_name ) {

		$this->include_required_files();

		/**
		 * Generates SQL which looks like this:
		 *
		 * ALTER TABLE table_name DROP deleted_at;
		 */
		$sql = sprintf(
			'ALTER TABLE %1$s DROP COLUMN %2$s',
			$this->prefixed_table_name(),
			$column_name
		);

		$result = maybe_drop_column( $this->prefixed_table_name(), $column_name, $sql );

		return $result;
	}

	/**
	 * Include required files for dbDelta() style queries.
	 *
	 * @since 2.0.0
	 */
	private function include_required_files() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	}
}