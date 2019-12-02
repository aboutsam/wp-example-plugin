<?php
/*
 * Plugin Name: Plugin Name
 * Description: Description for Plugin
 * Version:     1.0.0
 * Author:      Plugin Name Author
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( ! class_exists( 'Plugin_Name' ) ) :

	/**
	 * Main plugin class
	 */
	final class Plugin_Name {

		/**
		 * Init
		 */
		public static function init() {
			new self();
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->define_constants();
			$this->includes();

			add_action( 'widgets_init', array( $this, 'register_widget' ) );
		}

		/**
		 * Register Widget
		 */
		public function register_widget() {
			register_widget( 'Plugin_Name_SO_Widget' );
		}

		/**
		 * Define Success Stories constants
		 */
		private function define_constants() {
			define( 'PLUGIN_NAME_PATH', plugin_dir_path( __FILE__ ) );
		}

		/**
		 * All Success Stories classes
		 */
		private function plugin_classes() {
			$classes = array(
				'Plugin_Name_Database'  => PLUGIN_NAME_PATH . 'classes/class-plugin-name-database.php',
				'Plugin_Name_Admin'     => PLUGIN_NAME_PATH . 'classes/class-plugin-name-admin.php',
				'Plugin_Name_SO_Widget' => PLUGIN_NAME_PATH . 'classes/class-plugin-name-so-widget.php',
			);

			return $classes;
		}

		/**
		 * Load required classes
		 */
		private function includes() {
			foreach ( $this->plugin_classes() as $id => $path ) {
				if ( is_readable( $path ) && ! class_exists( $id ) ) {
					require_once $path;
				}
			}
		}
	}

	add_action( 'plugins_loaded', array( 'Plugin_Name', 'init' ), 10 );

	/**
	 * Call the activation and deactivation Hook of the Plugin.
	 */
	register_activation_hook( __FILE__, 'activate_plugin_name' );
	register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

endif;

/**
 * Define the Name of the Plugin Table.
 * This variable should be unique.
 */
define( 'PLUGIN_NAME_TABLE_NAME', 'plugin_name' );

/**
 * The code that runs during plugin activation.
 * This action is documented in classes/class-plugin-name-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'classes/class-plugin-name-activator.php';
	new Plugin_Name_Activator( PLUGIN_NAME_TABLE_NAME );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in classes/class-plugin-name-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'classes/class-plugin-name-deactivator.php';
	new Plugin_Name_Deactivator( PLUGIN_NAME_TABLE_NAME );
}
