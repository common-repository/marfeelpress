<?php

namespace API\Plugins;

use API\Marfeel_REST_API;
use API\Mrf_API;
use Ioc\Marfeel_Press_App;
use API\Marfeel_API_Authentication_Service;
use WP_REST_Response;
use Base\Marfeel_Press_Plugin_Conflict_Manager;

class Mrf_Compatible_Plugins_Api extends Mrf_API {

	public function __construct() {
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	public function register() {
		register_rest_route( $this->get_namespace(), '/plugins/enable-device-detection', $this->get_methods() );
	}

	public function get() {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );

		try {
			Marfeel_Press_Plugin_Conflict_Manager::enable_cache_mobile_detection();

			$log_provider->write_log( 'Call to enable cache mobile detection for supported plugins' );

			return new WP_REST_Response( '', 200 );
		} catch ( \Exception $e ) {
			$log_provider->write_log( 'Enable cache mobile detection failed ' . $e );

			return new WP_REST_Response( $e, 500 );
		}
	}

	public function authenticate() {
		return Marfeel_API_Authentication_Service::authenticate();
	}
}
