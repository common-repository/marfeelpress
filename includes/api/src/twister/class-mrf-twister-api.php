<?php

namespace API\Twister;

use API\Mrf_API;
use Ioc\Marfeel_Press_App;
use Base\Entities\Twister\Mrf_Twister;
use API\Marfeel_REST_API;
use WP_REST_Response;

class Mrf_Twister_API extends Mrf_API {

	public function __construct() {
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	public function register() {
		register_rest_route( $this->get_namespace(), '/twister/index/config', $this->get_methods() );
	}

	public function get() {
		$result = Marfeel_Press_App::make( 'json_serializer' )->serialize( new Mrf_Twister() );

		return new WP_REST_Response( $result, 200 );
	}

}
