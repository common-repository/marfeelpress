<?php

namespace API;

use WP_Error;
use Base\Marfeel_Press_Plugin_Conflict_Manager;
use Ioc\Marfeel_Press_App;

class Marfeel_REST_API {

	const METHOD_CREATABLE = 'POST';
	const METHOD_EDITABLE = 'PUT';
	const METHOD_READABLE = 'GET';

	public function __construct() {
		if ( ! defined( 'PHPUNIT_TEST' ) ) {
			Marfeel_Press_Plugin_Conflict_Manager::start_api();
		}
		add_filter( 'rest_authentication_errors', array( $this, 'authenticate' ) );
	}

	public static function authenticate() {
		$api_token = Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.api_token' );

		if (
			strpos( $_SERVER['REQUEST_URI'], 'marfeelpress' ) === false
			|| ( isset( $_GET['token'] ) && ( $_GET['token'] == $api_token ) )
			|| ( isset( $_SERVER['HTTP_TOKEN'] ) && $_SERVER['HTTP_TOKEN'] == $api_token )
		) {
			return true;
		}
		return new WP_Error( 403 );
	}

	public function register() {
		Marfeel_Press_App::make( 'definition_api' )->register();
		Marfeel_Press_App::make( 'ads_txt_api' )->register();
		Marfeel_Press_App::make( 'ads_txt_update_api' )->register();
		Marfeel_Press_App::make( 'user_api' )->register();
		Marfeel_Press_App::make( 'twister_api' )->register();

		Marfeel_Press_App::make( 'ripper_api' )->register();
		Marfeel_Press_App::make( 'extractor_api' )->register();
		Marfeel_Press_App::make( 'availability_api' )->register();
		Marfeel_Press_App::make( 'menu_api' )->register();
		Marfeel_Press_App::make( 'menu_categories_api' )->register();
		Marfeel_Press_App::make( 'press_settings_api' )->register();
		Marfeel_Press_App::make( 'proxy_api' )->register();
		Marfeel_Press_App::make( 'widgets_api' )->register();
		Marfeel_Press_App::make( 'plugins_api' )->register();
		Marfeel_Press_App::make( 'compatible_plugins_api' )->register();
		Marfeel_Press_App::make( 'log_api' )->register();
		Marfeel_Press_App::make( 'logo_api' )->register();

		Marfeel_Press_App::make( 'test_api' )->register();
		Marfeel_Press_App::make( 'softchecks_results_api' )->register();
		Marfeel_Press_App::make( 'softchecks_metrics_api' )->register();
	}
}
