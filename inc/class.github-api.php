<?php

class WP_Contributions_Github_Api {
	private static $api_url = 'https://api.github.com/';

	public static function load() {
		wp_embed_register_handler(
			'gist',
			'#https://gist.github.com/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)(\#file-(.+))?$#i',
			array( $this, 'gist_embed_handler' )
		);
	}


	public static function get_gists( $name ) {
		if( ! $name ) {
			return false;
		}

		if ( false === ( $gists = get_transient( 'wp-contributions-gists-' . $name ) ) ) {
			$url     = self::$api_url . 'users/' . $name . '/gists';
			$results = wp_remote_get( $url );

			$gists   = array();
			$data    = json_decode( wp_remote_retrieve_body( $results ) );

			if( $data ) {
				foreach( $data as $gist ) {
					$gists[] = (object) array(
						'created_at'  => $gist->created_at,
						'description' => $gist->description,
						'html_url'    => $gist->html_url
					);
				}
			}

			set_transient( 'wp-contributions-gists-' . $name, $gists, apply_filters( 'wp_contributions_gists_transient', HOUR_IN_SECONDS * 12 ) );
		}

		return $gists;
	}

	public static function get_repos( $name ) {
		if( ! $name ) {
			return false;
		}

		if ( false === ( $repos = get_transient( 'wp-contributions-grepos-' . $name ) ) ) {
			$url     = self::$api_url . 'users/' . $name . '/repos?sort=updated';
			$results = wp_remote_get( $url );

			$repos   = array();
			$data    = json_decode( wp_remote_retrieve_body( $results ) );

			if( $data ) {
				foreach( $data as $repo ) {
					$repos[] = (object) array(
						'created_at'  => $repo->created_at,
						'name'        => $repo->name,
						'description' => $repo->description,
						'html_url'    => $repo->html_url
					);
				}
			}

			set_transient( 'wp-contributions-grepos-' . $name, $repos, apply_filters( 'wp_contributions_grepos_transient', HOUR_IN_SECONDS * 12 ) );
		}

		return $repos;
	}


	public function gist_embed_handler( $matches, $attr, $url, $rawattr ) {
		$id   = $matches[2];
		$html = '<script src="https://gist.github.com/%s.js%s"></script><noscript>%s</noscript>';

		if ( preg_match( "/^[a-zA-Z0-9]+$/", $id ) ) {
			$noscript = sprintf(
				__( '<p>View the code on <a href="https://gist.github.com/%s">Gist</a>.</p>', 'wp-contributions' ),
				$id
			);

			if ( isset( $matches[4] ) ) {
				return sprintf( $html,  $id, '?file=' . $matches[4], $noscript );
			}
			else {
				return sprintf( $html,  $id, '', $noscript );
			}
		}
	}

}
