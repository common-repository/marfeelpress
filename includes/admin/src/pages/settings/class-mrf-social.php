<?php

namespace Admin\Pages\Settings;

use Ioc\Marfeel_Press_App;

class Mrf_Social extends Mrf_Settings {

	public function prepare_page( $context ) {
		$context = parent::prepare_page( $context );

		$context->page = 'social';

		return $context;
	}

	public function get_setting_id() {
		return 'socialNetworks';
	}
}
