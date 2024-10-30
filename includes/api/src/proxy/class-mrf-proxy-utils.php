<?php

namespace API\Proxy;

use Ioc\Marfeel_Press_App;

class Mrf_Proxy_Utils {

	const DOCKER_LOCALHOST = 'docker.for.mac.host.internal';
	const LOCALHOST = 'localhost';
	const SECURE_PROTOCOL = 'https';


	public function build_url() {
		return self::SECURE_PROTOCOL . '://' . str_replace( self::LOCALHOST, self::DOCKER_LOCALHOST, $this->normalize_query_params() );
	}

	private function normalize_query_params() {
		$result_url = $_GET['url'];
		$has_query_param = preg_match( "/\?/", $result_url );

		$input_array = $_GET;
		unset( $input_array['url'] );

		foreach ( $input_array as $key => $value ) {
			$connector = $has_query_param ? '&' : '?';
			$result_url = $result_url . $connector . $key . '=' . $value;

			$has_query_param = true;
		}

		return $result_url;
	}

}
