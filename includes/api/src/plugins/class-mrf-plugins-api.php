<?php

namespace API\Plugins;

use API\Marfeel_REST_API;
use API\Mrf_API;
use Ioc\Marfeel_Press_App;
use API\Marfeel_API_Authentication_Service;
use WP_REST_Response;

class Mrf_Plugins_Api extends Mrf_API {

	public function __construct() {
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	public function register() {
		register_rest_route( $this->get_namespace(), '/plugins', $this->get_methods() );
	}

	public function get() {
		$result = Marfeel_Press_App::make( 'plugins_service' )->get();

		return new WP_REST_Response( $result, 200 );
	}

	public function authenticate() {
		return Marfeel_API_Authentication_Service::authenticate();
	}
}
