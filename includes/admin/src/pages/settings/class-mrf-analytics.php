<?php


namespace Admin\Pages\Settings;

use Ioc\Marfeel_Press_App;

class Mrf_Analytics extends Mrf_Settings {

	public function get_setting_id() {
		return 'metrics';
	}

	protected function get_tracking_id() {
		return 'metrics';
	}
}
