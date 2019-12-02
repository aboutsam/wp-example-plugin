<?php
/**
 * Database functions for Plugin_Name.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/classes
 */

class Plugin_Name_Database {

	/**
	 * Holds the WordPress Database Variable
	 *
	 * @var wpdb
	 */
	public $db;

	/**
	 * Initialize Plugin_Name_Database Class.
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	 * Insert or update data in custom database table.
	 *
	 * @param string $plugin_name_id   Post ID.
	 * @param array  $values    Metadata of Post.
	 * @return mixed
	 */
	public function insert( string $plugin_name_id, array $values ) {
		if ( $this->db->insert( $this->db->prefix . PLUGIN_NAME_TABLE_NAME, $values ) !== false ) {
			return;
		}

		$this->db->update(
			$this->db->prefix . PLUGIN_NAME_TABLE_NAME,
			$values,
			array( 'plugin_name_id' => $plugin_name_id )
		);

		return false;
	}

	/**
	 * Return single value from custom post type Plugin Name.
	 */
	public function get_results( $plugin_name_id ) {
		$query = 'SELECT plugin_name_data FROM ' . $this->db->prefix . PLUGIN_NAME_TABLE_NAME . ' WHERE plugin_name_id=' . $plugin_name_id;
		return $this->db->get_var( $query );
	}

	/**
	 * Return all data from custom post type Plugin Name.
	 *
	 * @return array
	 */
	public function get_all_results() {
		$query = 'SELECT * FROM ' . $this->db->prefix . PLUGIN_NAME_TABLE_NAME;
		return $this->db->get_results( $query );
	}

	/**
	 * Convert serialized string into array.
	 *
	 * @param int $plugin_name_id    Post ID.
	 * @return array
	 */
	public function get( $plugin_name_id ) {
		$data = $this->get_results( $plugin_name_id );
		return maybe_unserialize( $data );
	}

	/**
	 * Get all data from wp_postmeta table.
	 *
	 * @param string $post_type
	 * @return array
	 */
	public function get_all_data_from_postmeta( $post_type ) {
		$query = array(
			'post_type'      => $post_type, // 'plugin_post_type',
			'posts_per_page' => 100,
		);

		$get_all_data = new WP_Query( $query );

		$all_post_ids = array();
		foreach ( $get_all_data->posts as $data ) {
			array_push( $all_post_ids, $data->ID );
		}

		return $all_post_ids;
	}
}
