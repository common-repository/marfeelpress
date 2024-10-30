<?php


namespace Base\Utils;

use Base\Marfeel_Press_Plugin_Conflict_Manager;
use Ioc\Marfeel_Press_App;

class Uri_Utils {

	const OPTION_AMP_URL = 'marfeel_press.amp.url';
	const OPTION_TENANT_URI = 'uri';
	const QUERY_PARAM_AMP = '?amp=1';
	const PATH_AMP_SLASHED = '/amp/';
	const PATH_AMP_UNSLASHED = '/amp';
	const WP_NO_QUERY_PARAMS_API_STRUCTURE = '/wp-json/marfeelpress/v1/$ROUTE?';
	const WP_REGULAR_API_STRUCTURE = '/?rest_route=/marfeelpress/v1/$ROUTE&';

	public function add_params( $url, $params ) {
		if ( is_array( $params ) ) {
			foreach ( $params as $param ) {
				$url = $this->add_params( $url, $param );
			}

			return $url;
		} else {
			if ( strpos( $url, '?' ) !== false ) {
				$params = preg_replace( '/\/?\?/', '&', $params );

				if ( ! empty( $params ) && $params[0] != '&' ) {
					$params = '&' . $params;
				}
			} else {
				$url    = rtrim( $url, '/' ) . '/';
				$params = ltrim( $params, '/' );

				if ( ! empty( $params ) && $params[0] == '&' ) {
					$params = '?' . substr( $params, 1 );
				}
			}

			return $url . $params;
		}
	}

	public function clean_params( $url, $exceptions = array() ) {
		return preg_replace_callback( '/([A-Za-z0-9-_]+)(=[^&]+)?&?/ms', function( $match ) use ( $exceptions ) {
			if ( in_array( $match[1], $exceptions ) ) {
				return $match[0];
			}

			return '';
		}, $url );
	}

	public function is_valid_url( $url ) {
		return filter_var( $url, FILTER_VALIDATE_URL ) ? true : false;
	}

	public function get_home_url() {
		$protocol = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https' : 'http';
		return $protocol . '://' . $_SERVER['HTTP_HOST'];
	}

	public function get_content( $uri ) {
		$response = wp_remote_get( $uri );

		return $response['body'];
	}

	private function get_current_protocol( $uri, $separator = false ) {
		$separator_length = $separator ? 3 : 0;
		return substr( $uri, 0, strpos( $uri, '://' ) + $separator_length );
	}

	public function get_absolute_uri( $uri ) {
		if ( strpos( $uri, 'http' ) === false ) {
			$home_url = home_url();
			if ( substr( $uri, 0, 2 ) == '//' ) {
				$uri = self::get_current_protocol( $home_url ) . ':' . $uri;
			} elseif ( $uri == '/' ) {
				$uri = home_url( $uri );
			} elseif ( $uri[0] == '/' ) {
				$tenant_uri = preg_replace( '/https?:\/\//', '', Marfeel_Press_App::make( 'settings_service' )->get( self::OPTION_TENANT_URI ) );
				$trimed_uri = ltrim( $uri, '/' );
				if ( ! empty( $tenant_uri ) ) {
					$uri = self::get_current_protocol( $home_url, true ) . rtrim( $tenant_uri, '/' ) . '/' . $trimed_uri;
				} else {
					$uri = home_url( $trimed_uri );
				}
			}
		}

		return $uri;
	}

	public function get_amp_uri( $uri ) {
		$amp_url = Marfeel_Press_App::make( 'settings_service' )->get( self::OPTION_AMP_URL );

		if ( $amp_url === null ) {
			$structure   = get_option( 'permalink_structure' );
			$amp_url = empty( $structure ) ? self::QUERY_PARAM_AMP : self::PATH_AMP_SLASHED;
		}

		return $this->add_params( $uri, $this->get_normalized_slashed_amp( $amp_url, $uri ) );
	}

	public function clean_amp_uri( $uri ) {
		$amp_url = Marfeel_Press_App::make( 'settings_service' )->get( self::OPTION_AMP_URL );

		if ( strpos( $uri, '?p=' ) ) {
			return explode( '&', $uri )[0] . str_replace( '?', '&', $amp_url );
		} elseif ( strpos( $amp_url, '?' ) !== false ) {
			return explode( '?', $uri )[0] . $amp_url;
		} else {
			$uri_parts = explode( '?', $uri );

			return $uri_parts[0];
		}
	}

	private function get_normalized_slashed_amp( $amp_url, $uri ) {
		if ( $amp_url === '/amp/' ) {
			return $this->ends_with( $uri, '/' ) ? $amp_url : self::PATH_AMP_UNSLASHED;
		}

		return $amp_url;
	}

	public function get_current_uri() {
		$structure   = get_option( 'permalink_structure' );

		if ( empty( $structure ) ) {
			$current_url = $_SERVER['REQUEST_URI'];
		} else {
			$current_url = str_replace( '?' . $_SERVER['QUERY_STRING'] , '', $_SERVER['REQUEST_URI'] );

			if ( substr( $structure, strlen( $structure ) - 1, 1 ) == '/' ) {
				$current_url = trailingslashit( $current_url );
			}
		}

		$current_url = $this->get_absolute_uri( $current_url );

		$host = wp_parse_url( $current_url, PHP_URL_HOST );
		$tenant_home = Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' );

		if ( $host != $tenant_home ) {
			$current_url = str_replace( $host, $tenant_home, $current_url );
		}

		return $current_url;
	}

	public function is_local( $uri ) {
		return ( strpos( $uri, 'localhost' ) !== false );
	}

	public function is_amp_uri( $uri ) {
		return preg_match( '/\/amp\/?$/', $uri ) || get_query_var( 'amp' ) == 1;
	}

	public function is_site_secure() {
		return ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443;
	}

	public function is_feed() {
		return get_query_var( 'feed' ) == 'feed';
	}

	public function remove_protocol( $uri ) {
		return preg_replace( '/https?:\/\//', '', $uri );
	}

	public function ends_with( $base, $wanted ) {
		$length = strlen( $wanted );
		if ( $length == 0 ) {
			return true;
		}

		return ( substr( $base, -$length ) === $wanted );
	}

	public function get_image_url( $url ) {
		$url = $this->get_absolute_uri( $url );
		return Marfeel_Press_Plugin_Conflict_Manager::get_modified_image_url( $url );
	}

	public function get_api_structure(){
		if ( Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.avoid_query_params' ) ) {
			return self::WP_NO_QUERY_PARAMS_API_STRUCTURE;
		} else {
			return self::WP_REGULAR_API_STRUCTURE;
		};
	}
}
