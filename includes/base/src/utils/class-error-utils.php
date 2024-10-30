<?php

namespace Base\Utils;

class Error_Utils {
	public function is_error_response( $response ) {
		return is_array( $response ) && array_key_exists( 'response', $response ) && $response['response']['code'] >= 400;
	}

	public function is_response_ok( $response ) {
		return ! is_wp_error( $response ) && $response['response']['code'] === 200;
	}
}
