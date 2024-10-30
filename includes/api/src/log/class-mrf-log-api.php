<?php

namespace API\Log;

use API\Marfeel_REST_API;
use API\Mrf_API;
use Ioc\Marfeel_Press_App;

class Mrf_Log_Api extends Mrf_API {

	public function __construct() {
		$this->resource_name = 'log';

		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	public function get() {
		$log_service = Marfeel_Press_App::make( 'log_service' );

		$content = $log_service->get_content();

		if ( isset( $_GET['delete'] ) && $_GET['delete'] ) {
			$log_service->clean();
		}

		echo $content;

		Marfeel_Press_App::make( 'request_utils' )->end_connection();
	}
}
