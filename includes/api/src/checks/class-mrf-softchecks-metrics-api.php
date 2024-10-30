<?php

namespace API\Checks;

use API\Marfeel_REST_API;
use API\Mrf_API;
use Ioc\Marfeel_Press_App;

class Mrf_Softchecks_Metrics_Api extends Mrf_API {

	public function __construct() {
		$this->resource_name = 'softchecks/metrics';

		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	public function get() {
		return Marfeel_Press_App::make( 'checks_service' )->get_soft_checks();
	}
}
