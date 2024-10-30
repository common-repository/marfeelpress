<?php

namespace API\Test;

use WP_REST_Response;
use Ioc\Marfeel_Press_App;
use API\Mrf_API;
use API\Marfeel_REST_API;
use API\Test\Mrf_Api_Status;

class Mrf_Test_Api extends Mrf_API {

	public function __construct() {
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
			Marfeel_REST_API::METHOD_CREATABLE,
		);
	}

	public function register() {
		register_rest_route( $this->get_namespace(), '/test', $this->get_methods() );
	}

	public function get() {
		Marfeel_Press_App::make( 'wp_service' )->load_url( home_url() );
		return new Mrf_Api_Status( "OK" );
	}

	public function post() {
		return $this->get();
	}
}
