<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/classes
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Deactivator {

	/**
	 * Holds the WordPress database object.
	 *
	 * @var wpdb
	 */
	protected $db;

	/**
	 * Holds the table name.
	 *
	 * @var String
	 */
	protected $table_name;

	/**
	 * Initialize Plugin_Name_Deactivator Class.
	 *
	 * @param string $table_name   The table name that is represented.
	 */
	public function __construct( $table_name ) {
		global $wpdb;

		$this->db         = $wpdb;
		$this->table_name = $table_name;

		$this->drop_table();
	}

	/**
	 * Drop table from MySQL.
	 *
	 * @return void
	 */
	public function drop_table() {
		$this->db->prefix . $this->table_name;

		$sql = "DROP TABLE IF EXISTS $table";
		$this->db->query( $sql );
		$this->drop_from_postmeta( false );
	}

	/**
	 * Delete all custom post types from wp_posts.
	 *
	 * @return void
	 */
	public function drop_from_postmeta( $delete_all = true ) {
		if ( false === $delete_all ) {
			return;
		}
		$delete = new Plugin_Name_Database();
		foreach ( $delete->get_all_data_from_postmeta( 'plugin_post_type' ) as $single_data ) {
			wp_delete_post( $single_data );
		}
		return;
	}

}
