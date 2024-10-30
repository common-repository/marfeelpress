<?php

namespace API\Extract;

use API\Mrf_API;
use WP_REST_Response;
use Ioc\Marfeel_Press_App;
use API\Marfeel_REST_API;
use Base\Marfeel_Press_Plugin_Conflict_Manager;


class Marfeel_Press_Gutenberg_Content_API extends Mrf_API {

	public function __construct() {
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
			Marfeel_REST_API::METHOD_CREATABLE,
		);
	}

	protected function get_extractor() {}

	protected function set_display_errors_off() {
		if ( ! Marfeel_Press_App::make( 'request_utils' )->is_dev() ) {
			ini_set( 'display_errors', 'Off' ); // @codingStandardsIgnoreLine
		}
	}

	protected function clean_uri_schema( $uri ) {
		return preg_replace( '/https?:\/(?!\/)/', '$0/', $uri );
	}

	protected function process_request() {
		$this->set_display_errors_off();

		preg_match( '/[?&]url=([^&]+)/', $_SERVER['REQUEST_URI'], $matches );

		$url_query = $this->clean_uri_schema( $matches[1] );

		Marfeel_Press_App::make( 'wp_service' )->load_url( ltrim( wp_parse_url( $url_query, PHP_URL_PATH ), '/' ) );

		Marfeel_Press_Plugin_Conflict_Manager::disable_extraction_plugins();

		$extractor = $this->get_extractor();

		$extraction = $extractor->extract();

		$status = 200;

		if ( $extraction === null ) {
			$status = 404;
		} else {
			$extraction = Marfeel_Press_App::make( 'json_serializer' )->serialize( $extraction );
		}

		return new WP_REST_Response( $extraction, $status );
	}

	public function post() {
		return $this->process_request();
	}

	public function get() {
		return $this->process_request();
	}
}
