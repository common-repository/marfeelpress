<?php

namespace API\Checks;

use API\Marfeel_REST_API;
use API\Mrf_API;
use Ioc\Marfeel_Press_App;

class Mrf_Softchecks_Results_Api extends Mrf_API {

	public function __construct() {
		$this->resource_name = 'softchecks';

		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	public function get() {
		$response = Marfeel_Press_App::make( 'checks_service' )->send_soft( isset( $_REQUEST['force'] ) && $_REQUEST['force'] );

		if ( is_string( $response ) ) {
			return $response;
		}

		return array(
			'error' => true,
			'code' => is_wp_error( $response ) ? 0 : $response['response']['code'],
		);
	}
}
