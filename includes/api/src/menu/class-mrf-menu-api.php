<?php

namespace API\Menu;

use API\Marfeel_REST_API;
use API\Mrf_API;
use Ioc\Marfeel_Press_App;
use WP_REST_Response;
use API\Menu\Mrf_Menu_Input_Converter;

class Mrf_Menu_Api extends Mrf_API {

	public function __construct() {
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	public function register() {
		register_rest_route( $this->get_namespace(), '/menus/default', $this->get_methods() );
	}

	public function get() {
		$menu   = Marfeel_Press_App::make( 'default_menu_service' )->get_default_menu();

		if ( isset( $_GET['format'] ) && $_GET['format'] === 'form' ) {
			$request_utils = Marfeel_Press_App::make( 'request_utils' );

			$request_utils->set_header( 'Content-Type: text/html' );

			echo Mrf_Menu_Input_Converter::convert( $menu );

			$request_utils->end_connection();
		} else {
			$result = Marfeel_Press_App::make( 'json_serializer' )->serialize( $menu );

			return new WP_REST_Response( $result, 200 );
		}
	}
}
