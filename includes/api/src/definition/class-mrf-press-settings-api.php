<?php

namespace API\Definition;

use Ioc\Marfeel_Press_App;
use API\Marfeel_REST_API;
use WP_REST_Request;
use WP_REST_Response;
use API\Marfeel_API_Authentication_Service;

class Mrf_Press_Settings_API extends Mrf_Base_API {

	public function __construct() {
		$this->resource_name = 'marfeel_press';
		$this->target_class = 'Base\Entities\Settings\Mrf_Press_Setting';
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	public function register() {
		register_rest_route( $this->get_namespace(), Mrf_Base_API::BASE . '/settings', $this->get_methods() );
	}

	public function authenticate() {
		return Marfeel_API_Authentication_Service::authenticate();
	}
}
