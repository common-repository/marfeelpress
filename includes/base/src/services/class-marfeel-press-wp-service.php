<?php

namespace Base\Services;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_WP_Service {

	public function force_404() {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
	}

	public function load_url( $url ) {
		error_reporting( E_WARNING ); // @codingStandardsIgnoreLine

		$_SERVER['REQUEST_URI'] = $url;

		Marfeel_Press_App::make( 'head_service' )->capture_head();

		remove_action( 'parse_request', 'rest_api_loaded' );

		global $wp;
		$wp->main();
	}
}
