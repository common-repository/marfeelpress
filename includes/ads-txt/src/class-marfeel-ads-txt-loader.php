<?php

namespace Ads_Txt;

use Ioc\Marfeel_Press_App;

class Marfeel_Ads_Txt_Loader {

	public function load_merged( $ads_txt ) {
		$host = Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' ) ?: $_SERVER['HTTP_HOST'];
		$url = MRFP_INSIGHT_API . '/adstxt/' . $host . '/?action=simpleMerge';

		$response = Marfeel_Press_App::make( 'http_client' )->request( 'POST', $url, array(
			'headers' => array(
				'Accept' => 'application/json; charset=utf-8',
				'Content-Type' => 'text/html',
			),
			'body' => $ads_txt->content,
		) );

		if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 ) {
			$ads_txt->content_merged = str_replace( 'action=simpleMerge', '', $response['body'] );
		}

		return $ads_txt;
	}

	public function load_unmerged( $ads_txt ) {
		$host = Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' ) ?: $_SERVER['HTTP_HOST'];
		$url = MRFP_INSIGHT_API . '/adstxt/' . $host . '/unmergedadstxt';

		$response = Marfeel_Press_App::make( 'http_client' )->request( 'GET', $url, array(
			'headers' => array(
				'Accept' => 'application/json; charset=utf-8',
			),
		) );

		if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 ) {
			$body = json_decode( $response['body'] );

			$ads_txt->content = $this->get_lines_as_text( $body->customerLines ); // @codingStandardsIgnoreLine
			$ads_txt->mrf_lines = $this->get_lines_as_text( $body->mrfLines ); // @codingStandardsIgnoreLine
		}

		return $ads_txt;
	}

	public function load_mrf_lines() {
		$uri = MRFP_INSIGHT_API . '/adstxt/' . Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' ) . '/mrflines';

		$provider = Marfeel_Press_App::make( 'log_provider' );

		$response = Marfeel_Press_App::make( 'http_client' )->request( 'GET', $uri, array(
			'headers' => array(
				'Accept' => 'application/json; charset=utf-8',
			),
		) );

		if ( is_wp_error( $response ) ) {
			$provider->write_log( 'Marfeel_Ads_Txt_Loader: wp error for: ' . $uri . ' | ' . $response->get_error_message() );
			return null;
		} elseif ( $response['response']['code'] == 200 ) {
			return $response['body'];
		}

		$provider->write_log( 'Marfeel_Ads_Txt_Loader: error for: ' . $uri . ' | error code: ' . $response['response']['code'] );
		return null;
	}

	private function get_lines_as_text( $lines ) {
		if ( is_array( $lines ) ) {
			return implode( "\n", $lines );
		}

		return $lines;
	}
}
