<?php
/**
 * Enable Plugin Name to use in SiteOrigin.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/classes
 * @author     Your Name <email@example.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}


if ( ! class_exists( 'Plugin_Name_SO_Widget' ) ) :

	/**
	 * Outputs a registered widget
	 */
	class Plugin_Name_SO_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				__CLASS__,
				'Create Plugin in Site Origin Bundle',
				array( 'description' => __( 'Description for Plugin Name', 'plugin-name' ) )
			);
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance Previously saved values from database.
		 *
		 * @see WP_Widget::form()
		 */
		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance );
			$data     = new Plugin_Name_Database();
			$data->get_all_results();
			$list = $data->db->last_result;
			?>

			<p>Select Plugin Name Widget</p>
			<select name="<?php echo esc_attr( $this->get_field_name( 'plugin_name_list_id' ) ); ?>" id="plugin_name_list_id">
				<?php foreach ( $list as $item ) : ?>
					<?php $selected = (int) $instance['plugin_name_list_id'] === (int) $item->plugin_name_id ? 'selected' : ''; ?>	
					<option value="<?php echo esc_attr( $item->plugin_name_id ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_attr( $item->plugin_name_title ); ?></option>
				<?php endforeach ?>
			</select>

			<?php

			$posts          = get_posts(
				array(
					'posts_per_page' => 20,
					'offset'         => 0,
				)
			);
			$selected_posts = ! empty( $instance['selected_posts'] ) ? $instance['selected_posts'] : array();

			?>
			<div style="max-height: 120px; overflow: auto;">
			<ul>
				<?php foreach ( $posts as $post ) { ?>
		
				<li><input 
					type="checkbox" 
					name="<?php echo esc_attr( $this->get_field_name( 'selected_posts' ) ); ?>[]" 
					value="<?php echo $post->ID; ?>" 
					<?php checked( ( in_array( $post->ID, $selected_posts ) ) ? $post->ID : '', $post->ID ); ?> />
					<?php echo get_the_title( $post->ID ); ?></li>
		
			<?php } ?>
			</ul>
			</div>
			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 */
		public function update( $new_instance, $old_instance ) {
			return $new_instance;
		}

		/**
		 * Front-end display of widget.
		 */
		public function widget( $args, $instance ) {

			$data   = new Plugin_Name_Database();
			$id     = $instance['plugin_name_list_id'];
			$output = $data->get( $id );

			for ( $i = 1; $i <= $output['count']; $i++ ) {
				$this->output( $output, $i );
			}
		}

		/**
		 * Output of Widget.
		 *
		 * @param array $output
		 * @param int   $i
		 * @return void
		 */
		public function output( $output, $i ) {
			$image = wp_get_attachment_url( $output[ 'file_image_' . $i ] );

			/* Hide output if name and description is empty */
			if ( empty( $output[ 'name_' . $i ] ) && empty( $output[ 'description_' . $i ] ) ) {
				return;
			}

			$output = '<div class="item wis-output border">
				<div class="wis-name">' . esc_attr( $output[ 'name_' . $i ] ) . '</div>
				<div class="wis-title">' . esc_attr( $output[ 'title_' . $i ] ) . '</div>
				<div class="wis-description">' . esc_attr( $output[ 'description_' . $i ] ) . '</div>
				<div class="wis-image">
				<img src="' . esc_attr( $image ) . '" alt="">
				</div>
			</div>';

			echo $output;
		}
	}

endif;
