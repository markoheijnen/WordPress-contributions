<?php

class WP_Contributions_Codex_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'contributions_core',
			'description' => __( 'Add a list of your accepted contributions to WordPress Core as a sidebar widget.', 'wp-contributions' )
		);

		parent::__construct( false, __( 'WP Contributions: Core', 'wp-contributions' ), $widget_ops );
	}

	public function form( $instance ) {
		if ( $instance && isset( $instance[ 'title' ] ) ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = esc_attr__( 'WP Core Contributions', 'wp-contributions' );
		}

		if ( $instance && isset( $instance[ 'trac-user' ] ) ) {
			$trac_user = esc_attr( $instance[ 'trac-user' ] );
		}
		else {
			$trac_user = esc_attr__( 'Trac Username', 'wp-contributions' );
		}

		if ( $instance && isset( $instance[ 'display-count' ] ) ) {
			$trac_count = absint( $instance[ 'display-count' ] );
		}
		else {
			$trac_count = 5;
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-contributions' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'trac-user' ); ?>"><?php _e( 'Trac Username:', 'wp-contributions' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('trac-user'); ?>" name="<?php echo $this->get_field_name( 'trac-user' ); ?>" type="text" value="<?php echo $trac_user; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'display-count' ); ?>"><?php _e( 'Display How Many Tickets?', 'wp-contributions' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'display-count' ); ?>" name="<?php echo $this->get_field_name( 'display-count' ); ?>" type="text" value="<?php echo $trac_count; ?>" />
		</p>

		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['trac-user']      = strip_tags( $new_instance['trac-user'] );
		$instance['display-count']  = absint( $new_instance['display-count'] );

		return $instance;
	}

	public function widget( $args, $instance ){
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$user  = $instance['trac-user'];
		$count = isset( $instance['display-count'] ) ? $instance['display-count'] : 5;

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// Widget content
		$theme_args = array(
			'user'  => $user,
			'items' => array_slice( WP_Contributions_WordPress_Api::get_changeset_items( $user ), 0, $count ),
			'total' => WP_Contributions_WordPress_Api::get_changeset_count( $user )
		);

		// Include template
		WP_Contributions::load_template( 'core-widget.php', $theme_args );

		echo $after_widget;
	}

}
