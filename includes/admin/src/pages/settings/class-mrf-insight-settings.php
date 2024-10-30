<?php


namespace Admin\Pages\Settings;

use Ioc\Marfeel_Press_App;

abstract class Mrf_Insight_Settings extends Mrf_Settings {

	public function __construct() {
		parent::__construct();

		$this->base_api = Marfeel_Press_App::make( 'insight_service' )->get_base_api();
		$this->api_token_param = $this->settings_service->get( 'marfeel_press.insight_token' );
	}
}
