<?php

class WP_Contributions_Core_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'contributions_codex',
			'description' => __( 'Add a list of your contributions to the WordPress Codex as a sidebar widget.', 'wp-contributions' )
		);

		parent::__construct( false, __( 'WP Contributions: Codex', 'wp-contributions' ), $widget_ops );
	}
	
	function form( $instance ) {
		if ( $instance && isset( $instance[ 'title' ] ) ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = esc_attr__( 'WP Codex Contributions', 'wp-contributions' );
		}
		
		if ( $instance && isset( $instance[ 'codex-user' ] ) ) {
			$codex_user = esc_attr( $instance[ 'codex-user' ] );
		}
		else {
			$codex_user = esc_attr__( 'Codex Username', 'wp-contributions' );
		}
		
		if ( $instance && isset( $instance[ 'display-count' ] ) ) {
			$codex_count = absint( $instance[ 'display-count' ] );
		}
		else {
			$codex_count = 5;
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-contributions' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'codex-user' ); ?>"><?php _e( 'Codex Username:', 'wp-contributions' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'codex-user' ); ?>" name="<?php echo $this->get_field_name( 'codex-user' ); ?>" type="text" value="<?php echo $codex_user; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'display-count' ); ?>"><?php _e( 'Display How Many Changes?', 'wp-contributions' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'display-count' ); ?>" name="<?php echo $this->get_field_name( 'display-count' ); ?>" type="text" value="<?php echo $codex_count; ?>" />
		</p>

		<?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['codex-user']     = strip_tags( $new_instance['codex-user'] );
		$instance['display-count']  = absint( $new_instance['display-count'] );

		return $instance;
	}
	
	function widget( $args, $instance ){
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );
		// Mediawiki usernames uppercase on 1st letter & case-specific
		$user  = $instance['codex-user'];
		$count = isset( $instance['display-count'] ) ? $instance['display-count'] : 5;

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// Widget content
		$theme_args = array(
			'items' => array_slice( WP_Contributions_WordPress_Api::get_codex_items( $user, $count ), 0, $count ),
			'total' => WP_Contributions_WordPress_Api::get_codex_count( $user )
		);

		// Include template
		WP_Contributions::load_template( 'codex-widget.php', $theme_args );

		echo $after_widget;
	}

}
