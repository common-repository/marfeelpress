<?php


namespace Base\Utils;

class Request_Utils {

	const MARFEEL_DEV_PARAM = 'marfeelDev';
	const MARFEEL_ADVANCED_PARAM = 'advanced';
	const MARFEEL_CHEROKEE_PARAM = 'marfeelcherokee';
	const MARFEEL_LOCAL_URL_PATTERN = "/localpress/";
	const MARFEEL_TEST_DEVICE_PARAM = 'mrf-test-device';

	public function get( $key, $default = null ) {
		return isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : $default;
	}

	public function is_post() {
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}

	public function is_dev() {
		return ( isset( $_GET[ self::MARFEEL_DEV_PARAM ] ) && $_GET[ self::MARFEEL_DEV_PARAM ] == true );
	}

	public function is_advanced() {
		return ( isset( $_GET[ self::MARFEEL_ADVANCED_PARAM ] ) && $_GET[ self::MARFEEL_ADVANCED_PARAM ] == true );
	}

	public function is_ripper() {
		return ( isset( $_GET[ self::MARFEEL_DEV_PARAM ] ) && $_GET[ self::MARFEEL_DEV_PARAM ] == "ripper" );
	}

	public function is_cherokee() {
		return isset( $_GET[ self::MARFEEL_CHEROKEE_PARAM ] );
	}

	public function is_local_env() {
		return preg_match( self::MARFEEL_LOCAL_URL_PATTERN, $_SERVER['HTTP_HOST'] );
	}

	public function is_test_device() {
		return isset( $_GET[ self::MARFEEL_TEST_DEVICE_PARAM ] );
	}

	public function end_connection() {
		if ( ! defined( 'PHPUNIT_TEST' ) ) {
			exit;
		}
	}

	public function set_header( $header ) {
		if ( ! defined( 'PHPUNIT_TEST' ) ) {
			header( $header );
		}
	}

	public function redirect( $url ) {
		wp_redirect( $url );
	}
}
