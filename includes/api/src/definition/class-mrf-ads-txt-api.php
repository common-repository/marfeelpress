<?php

namespace API\Definition;

use Ads_Txt\Managers\Marfeel_Ads_Txt_File_Manager;
use API\Definition\Mrf_Base_API;
use Ioc\Marfeel_Press_App;
use API\Marfeel_REST_API;

class Mrf_Ads_Txt_API extends Mrf_Base_API {

	/** @var Marfeel_Press_Settings_Service */
	private $settings_service;
	/** @var Marfeel_Press_Ads_Txt_Service */
	private $ads_txt_service;
	/** @var Error_Utils */
	private $error_utils;

	public function __construct() {
		$this->resource_name = 'ads/ads_txt';
		$this->target_class = 'Base\Entities\Advertisement\Mrf_Ads_Txt';
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
			Marfeel_REST_API::METHOD_CREATABLE,
			Marfeel_REST_API::METHOD_EDITABLE,
		);

		$this->settings_service = Marfeel_Press_App::make( 'settings_service' );
		$this->ads_txt_service = Marfeel_Press_App::make( 'marfeel_press_ads_txt_service' );
		$this->error_utils = Marfeel_Press_App::make( 'error_utils' );
	}

	public function get() {
		$this->update_status();
		return parent::get();
	}

	public function validate( $body ) {
		return null;
	}

	public function save( $body ) {
		parent::save( $body );
		$mrf_lines = $this->settings_service->get( 'ads.ads_txt.mrf_lines' );
		Marfeel_Press_App::make( 'ads_txt_manager' )->save( $body->content . PHP_EOL . $mrf_lines, true );
		Marfeel_Press_App::make( 'ads_txt_manager' )->update();
	}

	public function update_status() {
		$ads_txt = $this->settings_service->get( 'ads.ads_txt' );
		$response = $this->ads_txt_service->get_ads_txt_status_from_insight();

		if ( $this->error_utils->is_response_ok( $response ) ) {
			$result = json_decode( $response['body'] );
			$this->update_ads_txt( $ads_txt, $result );
		}
	}

	public function update_ads_txt( $ads_txt, $result ) {
		$ads_txt->status = $result->status;

		if ( ! $ads_txt->merged && empty( $ads_txt->content ) ) {
			$ads_txt = Marfeel_Press_App::make( 'marfeel_ads_txt_loader' )->load_unmerged( $ads_txt );
			$ads_txt->merged = true;
		} else {
			$ads_txt->mrf_lines = Marfeel_Press_App::make( 'marfeel_ads_txt_loader' )->load_mrf_lines();
		}

		$this->settings_service->set( 'ads.ads_txt', $ads_txt );
	}

	public function put() {
		Marfeel_Press_App::make( 'marfeel_press_ads_txt_service' )->initialize();
	}
}
