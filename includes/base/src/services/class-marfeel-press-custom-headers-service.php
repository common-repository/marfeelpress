<?php


namespace Base\Services;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Custom_Headers_Service {
	const VARY_HEADER = "Vary";
	const MRF_TECH = "MRF-tech";

	/** @var boolean */
	private $is_mobile;

	public function __construct() {
		$device_detection = Marfeel_Press_App::make( 'device_detection' );
		$this->is_mobile  = $device_detection->is_mobile();
		add_filter( 'wp_headers', array( $this, 'add_custom_http_headers' ) );
	}

	public function add_custom_http_headers( $headers ) {
		$headers = $this->add_vary_header( $headers );

		return $headers;
	}

	private function add_vary_header( $headers ) {
		if ( ! is_admin() ) {
			$headers['Vary'] = 'user-agent';
		}

		return $headers;
	}
}
