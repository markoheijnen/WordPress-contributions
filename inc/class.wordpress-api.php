<?php

class WP_Contributions_WordPress_Api {

	public static function get_plugins( $username, $args = array() ) {
		if( ! $username ) {
			return false;
		}

		if ( false === ( $data = get_transient( 'wp-contributions-plugissns-' . $username ) ) ) {
			$defaults = array(
				'author'   => $username,
				'per_page' => 30,
				'fields'   => array( 'description' => false, 'compatibility' => false )
			);

			$payload = array(
				'action'  => 'query_plugins',
				'request' => serialize(
					(object) wp_parse_args( $args, $defaults )
				)
			);
			$response = wp_remote_post( 'https://api.wordpress.org/plugins/info/1.0/', array( 'body' => $payload ) );

			if( 200 == wp_remote_retrieve_response_code( $response ) ) {
				$data = maybe_unserialize( wp_remote_retrieve_body( $response ) );
				$data = $data->plugins;

				set_transient( 'wp-contributions-plugins-' . $username, $data, apply_filters( 'wp_contributions_plugins_transient', HOUR_IN_SECONDS * 12 ) );
			}
		}

		return $data;
	}

	public static function get_changeset_items( $username ) {
		if ( null == $username ) {
			return array();
		}

		if ( false === ( $formatted = get_transient( 'wp-contributions-core-' . $username ) ) ) {
			$results_url = add_query_arg( array(
				'q'             => 'props+' . $username,
				'noquickjump'   => '1',
				'changeset'     => 'on'
			), 'https://core.trac.wordpress.org/search' );
			$response = wp_remote_get( $results_url, array( 'sslverify' => false ) );

			if( 200 == wp_remote_retrieve_response_code( $response ) ) {
				$results  = wp_remote_retrieve_body( $response );

				$results  = preg_replace( '/\s+/', ' ', $results );
				$results  = str_replace( PHP_EOL, '', $results );
				$pattern  = '/<dt><a href="(.*?)" class="searchable">\[(.*?)\]: ((?s).*?)<\/a><\/dt>\s*(<dd class="searchable">.*?. #(.*?) .*?.<\/dd>)/';

				preg_match_all( $pattern, $results, $matches, PREG_SET_ORDER );

				$formatted = array();

				foreach ( $matches as $match ) {
					array_shift( $match );

					$new_match = array(
						'link'          => 'https://core.trac.wordpress.org' . $match[0],
						'changeset'     => intval($match[1]),
						'description'   => $match[2],
						'ticket'        => isset( $match[3] ) ? intval($match[4]) : '',
					);

					array_push( $formatted, $new_match );
				}

				set_transient( 'wp-contributions-core-' . $username, $formatted, apply_filters( 'wp_contributions_core_transient', HOUR_IN_SECONDS * 12 ) );
			}
		}

		return $formatted;
	}

	public static function get_changeset_count( $username ) {
		if ( null == $username ) {
			return array();
		}

		if ( false == ( $count = get_transient( 'wp-contributions-core-count-' . $username ) ) ) {
			$results_url = add_query_arg( array(
				'q'             => 'props+' . $username,
				'noquickjump'   => '1',
				'changeset'     => 'on'
			), 'https://core.trac.wordpress.org/search' );
			$response = wp_remote_get( $results_url, array( 'sslverify' => false ) );

			if( 200 == wp_remote_retrieve_response_code( $response ) ) {
				$results = wp_remote_retrieve_body( $response );
				$pattern = '/<meta name="totalResults" content="(\d*)" \/>/';

				preg_match( $pattern, $results, $matches );

				$count = intval( $matches[1] );

				set_transient( 'wp-contributions-core-count-' . $username, $count, apply_filters( 'wp_contributions_core_count_transient', HOUR_IN_SECONDS * 12 ) );
			}
		}

		return $count;
	}

	public static function get_codex_items( $username, $limit = 10 ) {
		if ( null == $username ) {
			return array();
		}

		if ( false == ( $formatted = get_transient( 'wp-contributions-codex-' . $username ) ) ) {
			$results_url = add_query_arg( array(
				'action'    => 'query',
				'list'      => 'usercontribs',
				'ucuser'    => $username,
				'uclimit'   => $limit,
				'ucdir'     => 'older',
				'format'    => 'json'
			), 'http://codex.wordpress.org/api.php' );
			$response = wp_remote_get( $results_url, array( 'sslverify' => false ) );

			if( 200 == wp_remote_retrieve_response_code( $response ) ) {
				$results   = wp_remote_retrieve_body( $response );
				$raw       = json_decode( $results );
				$formatted = array();

				foreach( $raw->query->usercontribs as $item ) {
					$count = 0;
					$clean_title = preg_replace( '/^Function Reference\//', '', (string) $item->title, 1, $count );

					$new_item = array(
						'title'         => $clean_title,
						'description'   => (string) $item->comment,
						'revision'      => (int) $item->revid,
						'function_ref'  => (bool) $count
					);

					array_push( $formatted, $new_item );
				}

				set_transient( 'wp-contributions-codex-' . $username, $formatted, apply_filters( 'wp_contributions_codex_transient', HOUR_IN_SECONDS * 12 ) );
			}
		}

		return $formatted;
	}

	public static function get_codex_count( $username ) {
		if ( null == $username ) {
			return array();
		}

		if ( false == ( $count = get_transient( 'wp-contributions-codex-count-' . $username ) ) ) {
			$results_url = add_query_arg( array(
				'action'    =>  'query',
				'list'      =>  'users',
				'ususers'   =>  $username,
				'usprop'    =>  'editcount',
				'format'    =>  'json'
			), 'http://codex.wordpress.org/api.php' );
			$response = wp_remote_get( $results_url, array( 'sslverify' => false ) );

			if( 200 == wp_remote_retrieve_response_code( $response ) ) {
				$results  = wp_remote_retrieve_body( $response );

				$raw   = json_decode( $results );
				$count = (int) $raw->query->users[0]->editcount;

				set_transient( 'wp-contributions-codex-count-' . $username, $count, apply_filters( 'wp_contributions_codex_count_transient', HOUR_IN_SECONDS * 12 ) );
			}
		}

		return $count;
	}

}
