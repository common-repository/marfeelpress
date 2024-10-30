<?php

namespace API;

use Ioc\WP_IOC_UnitTestCase;
use Ioc\Marfeel_Press_App;
use WP_REST_Server;
use WP_REST_Request;

class Marfeel_REST_API_Test extends WP_IOC_UnitTestCase {

	public function setUp() {
		parent::setUp();

		global $wp_rest_server;
		$this->server = $wp_rest_server = new WP_REST_Server();

		Marfeel_Press_App::make( 'rest_api' )->register();
	}

	protected function request( $method, $url ) {
		return $this->server->dispatch( new WP_REST_Request( $method, $url ) );
	}

	protected function request_with_body( $method, $url, $body ) {
		$request = new WP_REST_Request( $method, $url );
		$request->set_body( $body );
		return $this->server->dispatch( $request );
	}
}
