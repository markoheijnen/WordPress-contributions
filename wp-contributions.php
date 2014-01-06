<?php
/*
Plugin Name: WP Contributions
Plugin URI: http://codekitchen.eu
Description: Add 
Version: 1.0
Author: CodeKitchen
Author URI: http://codekitchen.eu
License: GPLv2+
*/

/* Copyright 2014  Marko Heijnen, CodeKitchen B.V.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class WP_Contributions {
	private static $path;

	public function __construct() {
		self::$path = dirname( __FILE__ ) . '/';

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		$this->load_wordpress();
		$this->load_github();
	}


	public function load_textdomain() {
		load_plugin_textdomain( 'wp-contributions', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}


	public static function load_template( $name, $args = array() ) {
		extract( $args );

		// Include template from the theme
		$template_name = 'wp-contributions/' . $name;
		$path          = locate_template( $template_name );

		// If theme doesn't have the template then include our own
		if ( empty( $path ) ) {
			$path = self::$path . 'templates/' . $name;
		}

		// Load the template file
		include( $path );
	}


	public function load_wordpress() {
		require_once( 'inc/class.wordpress-api.php' );

		require_once( 'inc/widget.wp-codex-contributions.php' );
		require_once( 'inc/widget.wp-core-contributions.php' );

		add_action( 'widgets_init', array( $this, 'register_wordpress_widgets' ) );
	}

	public static function register_wordpress_widgets() {
		register_widget( 'WP_Contributions_Core_Widget' );
		register_widget( 'WP_Contributions_Codex_Widget' );
	}


	public function load_github() {
		require_once( 'inc/class.github-api.php' );

		require_once( 'inc/widget.github-gists.php' );
		require_once( 'inc/widget.github-repos.php' );

		add_action( 'widgets_init', array( $this, 'register_github_widgets' ) );
	}

	public static function register_github_widgets() {
		register_widget( 'WP_Contributions_Gists_Widget' );
		register_widget( 'WP_Contributions_Github_Repos_Widget' );
	}

}

$wp_contributions = new WP_Contributions;
