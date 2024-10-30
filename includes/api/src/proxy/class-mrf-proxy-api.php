<?php

namespace API\Proxy;

use Ioc\Marfeel_Press_App;
use API\Marfeel_REST_API;
use API\Mrf_API;
use WP_REST_Request;
use WP_REST_Response;

class Mrf_Proxy_Api extends Mrf_API {

	const DOCKER_LOCALHOST = 'docker.for.mac.host.internal';
	const LOCALHOST = 'localhost';
	const SECURE_PROTOCOL = 'https';
	const DEFAULT_TIMEOUT = 20;

	public function __construct() {
		$this->resource_name = 'proxy';
		$this->http_client = Marfeel_Press_App::make( 'http_client' );

		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
			Marfeel_REST_API::METHOD_CREATABLE,
		);

		$this->unsecure_endpoint();
		$this->proxy_utils = Marfeel_Press_App::make( 'proxy_utils' );
		$this->mrf_key = Marfeel_Press_App::make( 'insight_service' )->get_insight_key();
	}

	public function get() {
		$uri = $this->proxy_utils->build_url();

		$response = $this->http_client->request( 'GET', $uri, array(
			'timeout' => self::DEFAULT_TIMEOUT,
			'sslverify' => false,
			'headers' => array(
				'mrf-secret-key' => $this->mrf_key,
			),
		));

		if ( is_wp_error( $response ) ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'Error while proxying to: ' . $uri . ' via proxy-api' );
			return $response;
		} else {
			return new WP_REST_Response( json_decode( $response['body'] ), $response['response']['code'] );
		}
	}

	public function post( WP_REST_Request $request ) {
		$uri = $this->proxy_utils->build_url();

		$response = $this->http_client->request( 'POST', $uri, array(
			'timeout' => 20,
			'sslverify' => false,
			'headers' => array(
				'mrf-secret-key' => $this->mrf_key,
				'content-type' => 'application/json',
			),
			'body' => $request->get_body(),
		));

		if ( is_wp_error( $response ) ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'Error while proxying to: ' . $uri . ' via proxy-api' );
			return $response;
		} else {
			return new WP_REST_Response( json_decode( $response['body'] ), $response['response']['code'] );
		}
	}
}
