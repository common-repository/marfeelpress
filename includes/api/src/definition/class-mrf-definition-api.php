<?php

namespace API\Definition;

use API\Marfeel_API_Authentication_Service;
use API\Marfeel_REST_API;

class Mrf_Definition_API extends Mrf_Base_API {

	public function __construct() {
		$this->allowed_methods = array( Marfeel_REST_API::METHOD_READABLE );
	}

	public function read() {
		$settings = parent::read();

		return $settings;
	}

	public function authenticate() {
		return Marfeel_API_Authentication_Service::authenticate();
	}

}
