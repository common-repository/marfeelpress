<?php

namespace Admin\Pages\Settings;

use Ioc\Marfeel_Press_App;
use Admin\Marfeel_Press_Admin_Translator;
use Base\Entities\Settings\Mrf_Availability_Modes_Enum;

abstract class Mrf_Settings {

	const API_TOKEN_PARAM_NAME = 'token=';
	const OPTION_AVAILABILITY = 'mrf_availability';
	const OPTION_API_TOKEN = 'marfeel_press.api_token';
	const OPTION_INSIGHT_TOKEN = 'marfeel_press.insight_token';
	const OPTION_ACTIVATED_ONCE = 'marfeel_press.activated_once';
	const WP_NO_QUERY_PARAMS_API = '/wp-json/marfeelpress/v1';
	const WP_REGULAR_API = '/?rest_route=/marfeelpress/v1';
	const DEFAULT_MODE = Mrf_Availability_Modes_Enum::OFF;
	const PAGE_ID = 'mrf-settings';

	/** @var string */
	public $id;

	/** @var string */
	public $title;

	/** @var string */
	public $base_api;

	/** @var string */
	public $api_token_param;

	/** @var string */
	public $ads_txt_api;

	/** @var string */
	public $ads_txt_api_token_param;

	public function __construct() {
		$this->settings_service = Marfeel_Press_App::make( 'settings_service' );
		$this->request_utils = Marfeel_Press_App::make( 'request_utils' );

		$this->id = $this->get_setting_id();
		$this->title = $this->get_setting_title();
		$this->api_token_press_param = self::API_TOKEN_PARAM_NAME . $this->settings_service->get( self::OPTION_API_TOKEN );
		$this->api_token_param = $this->api_token_press_param;
		$this->set_base_api();
		$this->set_ads_txt_api();
	}

	protected function get_tracking_id() {
		return $this->id;
	}

	public function set_base_api(){
		if ( $this->id != 'adstxt' ) {
			$this->base_api = Marfeel_Press_App::make( 'insight_service' )->get_base_api();
			$this->api_token_param = $this->settings_service->get( 'marfeel_press.insight_token' );
		} elseif ( $this->settings_service->get( 'marfeel_press.avoid_query_params' ) ) {
			$this->base_api = self::WP_NO_QUERY_PARAMS_API;
			$this->api_token_param = '?' . $this->api_token_param;
		} else {
			$this->base_api = self::WP_REGULAR_API;
			$this->api_token_param = '&' . $this->api_token_param;
		}
	}

	public function set_ads_txt_api(){
		if ( $this->settings_service->get( 'marfeel_press.avoid_query_params' ) ) {
			$this->ads_txt_api = self::WP_NO_QUERY_PARAMS_API;
			$this->ads_txt_api_token_param = '?' . $this->api_token_press_param;
		} else {
			$this->ads_txt_api = self::WP_REGULAR_API;
			$this->ads_txt_api_token_param = '&' . $this->api_token_press_param;
		}
	}

	abstract function get_setting_id();

	public function prepare_page( $context ) {
		$context->wp_api_structure = Marfeel_Press_App::make( 'uri_utils' )->get_api_structure();
		$context->template = 'mrf-leroy-template.php';
		$context->onboarding = Marfeel_Press_App::make( 'onboarding_setting' );
		$context->is_tenant_created = ! ! $this->settings_service->get( self::OPTION_INSIGHT_TOKEN ) || Marfeel_Press_App::make( 'request_utils' )->is_dev();
		$context->is_dev = Marfeel_Press_App::make( 'request_utils' )->is_dev();
		$context->tracking = Marfeel_Press_App::make( 'tracker' )->get_configuration();
		$context->is_local_env = Marfeel_Press_App::make( 'request_utils' )->is_local_env();
		$context->activated_once = $this->settings_service->get( self::OPTION_ACTIVATED_ONCE ) ? true : false;
		$context->selected_availability = $this->settings_service->get_option_data( self::OPTION_AVAILABILITY, Mrf_Availability_Modes_Enum::DEFAULT_MODE );
		$context->has_adstxt_file = false;
		$context->secret_press_key = $this->settings_service->get( self::OPTION_API_TOKEN );
		$context->insight_url = Marfeel_Press_App::make( 'insight_service' )->get_insight_url();
		$context->current_setting = $this;

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$this->save( $context );
		} else {
			Marfeel_Press_App::make( 'tracker' )->track( 'screen/' . $this->get_tracking_id() );
		}

		$context->back = true;

		return $context;
	}

	public function get_setting_title() {
		return Marfeel_Press_Admin_Translator::trans( 'mrf.setting.' . $this->get_setting_id() );
	}

	public function get_setting_url() {
		return get_admin_url() . 'admin.php?page=' . self::PAGE_ID . '&action=' . $this->id;
	}

	public function is_default() {
		return false;
	}

	public function save( $context ) {}
}
