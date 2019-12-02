<?php
/**
 * Admin Configuration for Plugin_Name.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Plugin_Name_Admin' ) ) :

	class Plugin_Name_Admin {

		/**
		 * @var string
		 */
		private $slug = 'plugin_slug';

		/**
		 * @var string
		 */
		private $post_type = 'plugin_post_type';

		/**
		 * @var string
		 */
		private $title = 'Plugin Name';

		/**
		 * @var string
		 */
		private $dashicon = 'dashicons-thumbs-up';

		/**
		 * @var string
		 */
		private $rand;

		/**
		 * Initialize Plugin_Name_Admin Class.
		 */
		public function init() {
			add_action( 'init', array( $this, 'register_post_types' ), 5 );
			add_action( 'init', array( $this, 'remove_wysiwyg_editor' ), 10 );
			add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ), 5 );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_frontend' ) );

			add_action( 'save_post', array( $this, 'save_options' ) );

			$this->db   = new Plugin_Name_Database();
			$this->rand = md5( uniqid() . mt_rand() );
		}

		/**
		 * Remove WYSIWYG Editor.
		 */
		public function remove_wysiwyg_editor() {
			remove_post_type_support( $this->post_type, 'editor' );
		}

		/**
		 * Register Custom Post Type.
		 */
		public function register_post_types() {
			$labels = array(
				'name'               => $this->title,
				'singular_name'      => $this->title,
				'menu_name'          => $this->title,
				'name_admin_bar'     => $this->title,
				'add_new'            => 'Neue hinzufügen',
				'add_new_item'       => 'Neue ' . $this->title . ' hinzufügen',
				'new_item'           => 'Neue ' . $this->title,
				'edit_item'          => $this->title . ' bearbeiten',
				'view_item'          => $this->title . ' anzeigen',
				'all_items'          => 'Alle ' . $this->title,
				'search_items'       => $this->title . ' suchen',
				'not_found'          => $this->title . ' nicht gefunden',
				'not_found_in_trash' => $this->title . ' im Papierkorb nicht gefunden',
			);

			$args = array(
				'labels'              => $labels,
				'description'         => 'Zum Erstellen von ' . $this->title,
				'public'              => false,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'query_var'           => true,
				'exclude_from_search' => true,
				'rewrite'             => array( 'slug' => $this->post_type ),
				'has_archive'         => false,
				'hierarchical'        => false,
				'menu_icon'           => $this->dashicon,
				'menu_position'       => 5,
				'map_meta_cap'        => true,
			);

			register_post_type( $this->post_type, $args );
		}

		/**
		 * Add Metabox for Custom Post Type.
		 *
		 * @return void
		 */
		public function create_meta_box() {
			add_meta_box(
				$this->slug . '_meta_box',
				$this->title,
				array( $this, 'add_meta_box_content' ),
				$this->post_type,
				'normal',
				'default'
			);
		}

		/**
		 * Load Backend Scripts.
		 */
		public function enqueue_scripts() {

			wp_register_script(
				'admin_' . $this->rand,
				plugins_url( '../assets/main.js', __FILE__ ),
				array( 'jquery' ),
				false,
				true
			);
			wp_enqueue_script( 'admin_' . $this->rand );

			wp_register_style(
				'style_' . $this->rand,
				plugins_url( '../assets/main.css', __FILE__ )
			);
			wp_enqueue_style( 'style_' . $this->rand );

			if ( is_user_logged_in() ) {
				wp_enqueue_media();
			}
		}

		/**
		 * Load Frontend Scripts.
		 */
		public function enqueue_scripts_frontend() {
			wp_register_style(
				'style_fe_' . $this->rand,
				plugins_url( '../assets/frontend.css', __FILE__ )
			);

			wp_enqueue_style( 'style_fe_' . $this->rand );
		}

		/**
		 * Content of Metabox.
		 *
		 * @param int $id    Post ID.
		 */
		public function add_meta_box_content( $post ) {
			$id     = $post->ID;
			$fields = $this->db->get( $id );

			$count = isset( $fields['count'] ) ? $fields['count'] : 1;
			$nonce = 'metabox_' . $this->slug . '_nonce';
			?>

			<p>Click or drag to reorder the boxes.</p>
			<input type="hidden" name="<?php echo esc_attr( $nonce ); ?>">
			<input type="hidden" id="count" name="count" value="">
			<div class="sortable-container">

				<?php for ( $i = 1; $i <= $count; $i++ ) : ?>
					<?php $image_id = $fields[ 'file_image_' . $i ]; ?>
					<div class="box">
						<div class="form-group">
							<label for="name">Name</label>
							<input type="text" class="item name" data-id="name" value="<?php echo esc_attr( $fields[ 'name_' . $i ] ); ?>">
							<label for=" title">Title</label>
							<input type="text" class="item title" data-id="title" value="<?php echo esc_attr( $fields[ 'title_' . $i ] ); ?>">
							<label for="name">Beschreibung</label>
							<textarea name="description" class="item description" data-id="description" cols="3" rows="3"><?php echo esc_attr( $fields[ 'description_' . $i ] ); ?></textarea>
							<label for="image">Bild</label>
							<input type="hidden" class="item file_image" data-id="file_image" value="<?php echo esc_attr( $image_id ); ?>">
							<img src="<?php echo wp_get_attachment_url( $image_id ); ?>" class="item image preview-img" data-id="image" alt="">
							<button value="addImage" class="wis item btn" data-id="upload">Upload Image</button>
						</div>
						<div class="order">
							<span class="item count">Count</span>
							<button class="wis" value="up"><i class="dashicons dashicons-arrow-up-alt2"></i></button>
							<button class="wis" value="down"><i class="dashicons dashicons-arrow-down-alt2"></i></button>
							<button class="wis" value="remove"><i class="dashicons dashicons-no"></i></button>
							<button class="wis" value="create"><i class="dashicons dashicons-plus"></i></button>
						</div>
					</div>

				<?php endfor; ?>

			</div>
			<?php
		}

		/**
		 * Save Meta Box Fields.
		 *
		 * @param string $post_id   Post ID.
		 * @return void
		 */
		public function save_options( $post_id ) {

			// Check if our nonce is set.
			if ( ! isset( $_POST[ 'metabox_' . $this->slug . '_nonce' ] ) ) {
				return $post_id;
			}

			/*
			* If this is an autosave, our form has not been submitted,
			* so we don't want to do anything.
			*/
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			$post_data = array();
			if ( isset( $_POST ) && ! empty( $_POST ) ) {
				$post_data = wp_unslash( $_POST );
			}

			$fields = array();
			array_push( $fields, 'count', );
			for ( $i = 1; $i <= $post_data['count']; $i++ ) {
				array_push( $fields, 'name_' . $i, 'title_' . $i, 'description_' . $i, 'file_image_' . $i );
			}

			$store_data = array_fill_keys( $fields, '' );

			foreach ( $store_data as $key => $value ) {
				if ( isset( $post_data[ $key ] ) ) {
					$store_data[ $key ] = $post_data[ $key ];
				}
			}

			$serialized_data = serialize( $store_data );

			$insert_data = array(
				'plugin_name_id'    => $post_id,
				'plugin_name_title' => $post_data['post_title'],
				'plugin_name_data'  => $serialized_data,
			);

			$this->db->insert( $post_id, $insert_data );
		}
	}

	/**
	 * Load Plugin_Name_Admin Class.
	 *
	 * @return void
	 */
	function load_plugin_name_admin() {
		global $plugin_name_admin;

		if ( ! isset( $plugin_name_admin ) ) {
			$plugin_name_admin = new Plugin_Name_Admin();
			$plugin_name_admin->init();
		}

		return $plugin_name_admin;
	}

	/**
	 * Initialize Plugin_Name_Admin Class.
	 */
	load_plugin_name_admin();

endif;
