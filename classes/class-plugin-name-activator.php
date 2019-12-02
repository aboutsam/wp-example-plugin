<?php
/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/classes
 */
class Plugin_Name_Activator {

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
	 * Initialize Plugin_Name_Activator Class.
	 *
	 * @param string $table_name   The table name that is represented.
	 * @return mixed
	 */
	public function __construct( $table_name ) {
		global $wpdb;

		$this->db         = $wpdb;
		$this->table_name = $table_name;

		$this->create_table();
	}

	/**
	 * Create Plugin Name Table
	 *
	 * @return void
	 */
	public function create_table() {
		$table = $this->db->prefix . $this->table_name;

		$charset_collate = $this->db->get_charset_collate();

		$query = 'CREATE TABLE IF NOT EXISTS  ' . $table . " (
			plugin_name_id INT(11) AUTO_INCREMENT,
			plugin_name_title VARCHAR(255),
            plugin_name_data LONGTEXT,
            PRIMARY KEY(plugin_name_id)
            )$charset_collate;";

		$this->db->query( $query );
	}

}
