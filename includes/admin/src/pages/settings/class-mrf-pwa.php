<?php

namespace Admin\Pages\Settings;

use Ioc\Marfeel_Press_App;

class Mrf_Pwa extends Mrf_Settings {

	public function get_setting_id() {
		return 'pwa_press';
	}

	public function prepare_page( $context ) {
		$context = parent::prepare_page( $context );
		$context->page = 'pwa/press';

		return $context;
	}
}
