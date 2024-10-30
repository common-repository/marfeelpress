<?php

namespace Admin\Pages\Settings;

use Base\Services\Marfeel_Press_Checks_Service;
use Ioc\Marfeel_Press_App;

class Mrf_Onboarding extends Mrf_Settings {

	public function __construct() {
		parent::__construct();
		$this->utils = Marfeel_Press_App::make( 'page_utils' );
	}

	protected function get_tracking_id() {
		return 'getting_started';
	}

	public function get_setting_id() {
		return 'mrf-onboarding';
	}

	public function prepare_page( $context ) {
		$context = parent::prepare_page( $context );
		$context->page = "onboarding/press";
		$context->back = false;

		if ( $context->activated_once ) {
			$context->page = 'postonboarding';
		}

		$prev_checks = Marfeel_Press_App::make( 'settings_service' )->get_option_data( Marfeel_Press_Checks_Service::OPTION_SOFTCHECKS, null );
		$new_checks = Marfeel_Press_App::make( 'checks_service' )->send_soft( true );

		$this->merge_adstxt_if_necessary( $prev_checks, $new_checks );

		return $context;
	}

	public function get_setting_url() {
		return get_admin_url() . 'admin.php?page=' . $this->id;
	}

	private function merge_adstxt_if_necessary( $prev_checks, $new_checks ) {
		if ( is_string( $prev_checks ) && is_string( $new_checks ) ) {
			$prev_checks = json_decode( $prev_checks );
			$new_checks = json_decode( $new_checks );

			if ( ! $prev_checks->canManageAdsTxt && $new_checks->canManageAdsTxt ) { // @codingStandardsIgnoreLine
				Marfeel_Press_App::make( 'marfeel_press_ads_txt_service' )->initialize();
			}
		}
	}
}
