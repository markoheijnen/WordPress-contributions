<?php

class WP_Contributions_Github_Repos_Widget extends Connections_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'contributions_github_repos',
			'description' => __( 'Add a list of your GitHub repositories as a sidebar widget.', 'wp-contributions' )
		);

		parent::__construct( false, __( 'WP Contributions: GitHub Repositories', 'wp-contributions' ), $widget_ops );
	}

	public function form( $instance ) {
		if ( $instance && isset( $instance[ 'title' ] ) ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = esc_attr__( 'Gists', 'wp-contributions' );
		}

		if ( $instance && isset( $instance[ 'username' ] ) ) {
			$username = esc_attr( $instance[ 'username' ] );
		}
		else {
			$username = esc_attr__( 'GitHub Username', 'wp-contributions' );
		}

		if ( $instance && isset( $instance[ 'display-count' ] ) ) {
			$display_count = absint( $instance[ 'display-count' ] );
		}
		else {
			$display_count = 5;
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-contributions' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'GitHub Username:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'display-count' ); ?>"><?php _e( 'Display How Many Tickets?', 'wp-contributions' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'display-count' ); ?>" name="<?php echo $this->get_field_name( 'display-count' ); ?>" type="text" value="<?php echo $display_count; ?>" />
		</p>

		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['username']       = strip_tags( $new_instance['username'] );
		$instance['display-count']  = absint( $new_instance['display-count'] );

		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );

		$title    = apply_filters( 'widget_title', $instance['title'] );
		$username = $instance['username'];
		$count    = isset( $instance['display-count'] ) ? $instance['display-count'] : 5;

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// Widget content
		$theme_args = array(
			'repos' => array_slice( Connection_Github::get_repos_from_user( $instance[ 'author' ] ), 0, $count )
		);

		// Include template
		WP_Contributions::load_template( 'github-github-repos-widget.php', $theme_args );

		echo $after_widget;
	}

}