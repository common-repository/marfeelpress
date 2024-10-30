<?php

namespace Base;

use Ioc\Marfeel_Press_App;
use Base\Entities\Settings\Mrf_Options_Enum;
use Base\Utils\Request_Utils;

class Marfeel_Press_Proxy {
	/** @var bool */
	protected $is_amp = false;

	protected function do_request( $proxy_url ) {
		$data = array(
			'timeout' => 5,
			'followlocation' => true,
		);

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$data['headers'] = array(
				'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
			);
		}

		return Marfeel_Press_App::make( 'http_client' )->request( 'GET', MRFP_RESOURCES_HOST . $proxy_url, $data );
	}

	public function execute() {
		$proxy_url = $this->get_proxy_uri();
		$data = $this->do_request( $proxy_url );

		if ( ! is_wp_error( $data ) ) {
			$status_code = $data['response']['code'];

			if ( $status_code == 200 || $this->is_amp ) {
				status_header( $status_code );

				$html = $this->replace_meta_generator( $data['body'] );

				$html = Marfeel_Press_App::make( 'head_service' )->add_resizer( $html );

				echo $html;

				if ( $this->is_amp && $status_code != 200 ) {
					Marfeel_Press_App::make( 'log_provider' )->write_log( 'proxyAmpError requesting url: ' . MRFP_RESOURCES_HOST . $proxy_url . ' with status code: ' . $status_code );
				}

				Marfeel_Press_App::make( 'request_utils' )->end_connection();
			} else {
				Marfeel_Press_App::make( 'log_provider' )->write_log( 'Proxy error requesting url: ' . MRFP_RESOURCES_HOST . $proxy_url . ' with status code: ' . $status_code );
			}
		} else {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'Proxy error requesting url: ' . MRFP_RESOURCES_HOST . $proxy_url );

			Marfeel_Press_App::make( 'settings_service' )->overwrite( Mrf_Options_Enum::OPTION_MRF_ROUTER, false );

			if ( $this->is_amp ) {
				Marfeel_Press_App::make( 'log_provider' )->write_log( 'proxyAmpError' );
				status_header( 503 );
				Marfeel_Press_App::make( 'request_utils' )->end_connection();
			}
		}
	}

	protected function get_proxy_uri() {
		$uri_utils = Marfeel_Press_App::make( 'uri_utils' );
		$current_uri = $uri_utils->get_current_uri();
		$current_uri = $uri_utils->remove_protocol( $current_uri );

		$params = array();
		$clean_qs = $uri_utils->clean_params( $_SERVER['QUERY_STRING'], array( 's', 'p', 'cat', 'page_id', 'marfeelContext', Request_Utils::MARFEEL_CHEROKEE_PARAM ) );

		if ( ! empty( $clean_qs ) ) {
			$params = explode( '&', $clean_qs );
		}

		if ( $uri_utils->is_amp_uri( $current_uri ) ) {
			$current_uri = preg_replace( '/\/amp(\/)?$/', '$1', $current_uri );
			$path = '/amp/';
			$this->is_amp = true;
		} else {
			$path = '/';
			$params[] = 'marfeeldt=s';

			$mrf_ct = Marfeel_Press_App::make( 'ct_service' )->get_marfeelct();
			if ( ! empty( $mrf_ct ) ) {
				$params[] = $mrf_ct;
			}
		}

		$uri = $path . $current_uri;

		if ( ! empty( $params ) ) {
			$uri .= '?' . implode( '&', $params );
		}

		return $uri;
	}

	private function replace_meta_generator( $html ) {
		return preg_replace(
			'/<meta name=["\']?generator["\']? content=["\']?MarfeelGutenberg["\']?>/',
			'<meta name="generator" content="MarfeelPress">',
			$html
		);
	}
}
