<?php

namespace API\availability;

use API\Marfeel_REST_API;
use API\Marfeel_API_Authentication_Service;
use API\Mrf_API;
use Ioc\Marfeel_Press_App;
use WP_REST_Response;
use Base\Entities\Settings\Mrf_Availability_Modes_Enum;

class Mrf_Availability_Api extends Mrf_API {

	public function __construct() {
		$this->resource_name   = 'plugin/availability';
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
			Marfeel_REST_API::METHOD_CREATABLE,
		);

		$this->unsecure_endpoint();
	}

	public function get() {
		$availability = new Mrf_Availability();
		$availability->availability = Marfeel_Press_App::make( 'availability_service' )->get_availability();

		$result = Marfeel_Press_App::make( 'json_serializer' )->serialize( $availability );

		return new WP_REST_Response( $result, 200 );
	}

	private function is_correct_availability( $availability_candidate ) {
		switch ( $availability_candidate ) {
			case Mrf_Availability_Modes_Enum::OFF:
				return true;
			case Mrf_Availability_Modes_Enum::LOGGED:
				return true;
			case Mrf_Availability_Modes_Enum::ALL:
				return true;
			default:
				return false;
		}
	}

	public function post( $request ) {
		$availability = '';
		$is_warda_compatible = null;
		$content_type = '';

		if ( isset( $_SERVER['CONTENT_TYPE'] ) ) {
			$content_type = $_SERVER['CONTENT_TYPE'];
		}

		if ( $content_type === 'application/json' ) {
			$body = json_decode( $request->get_body() );
			$availability = $body->availability;
			$is_warda_compatible = $body->is_warda_compatible;
		} else {
			$availability = $request->get_body();
		}

		if ( $this->is_correct_availability( $availability ) ) {
			try {
				$activated_once = Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.activated_once' );
				$amp = $activated_once ? Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.amp.activate' ) : true;
				Marfeel_Press_App::make( 'availability_service' )->set_availability( $availability, $amp, $is_warda_compatible );

				return new WP_REST_Response( '', 200 );
			} catch ( \Exception $e ) {
				return new WP_REST_Response( $e, 500 );
			}
		} else {
			return new WP_REST_Response( 'Wrong availability', 400 );
		}

	}

	public function authenticate() {
		return Marfeel_API_Authentication_Service::authenticate_method( 'POST' );
	}
}
