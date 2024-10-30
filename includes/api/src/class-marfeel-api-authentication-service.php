<?php

namespace API;

use Ioc\Marfeel_Press_App;

class Marfeel_API_Authentication_Service {

	public static function authenticate() {
		$api_token = Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.api_token' );

		if (
			strpos( $_SERVER['REQUEST_URI'], 'marfeelpress' ) === false
			|| ( isset( $_GET['token'] ) && ( $_GET['token'] == $api_token ) )
			|| ( isset( $_SERVER['HTTP_TOKEN'] ) && $_SERVER['HTTP_TOKEN'] == $api_token )
		) {
			return true;
		}
		return false;
	}

	public static function authenticate_method( $method ) {
		return ( $_SERVER['REQUEST_METHOD'] !== $method ) || self::authenticate();
	}

}
