<?php

namespace Admin\Pages\Settings;

use Ioc\Marfeel_Press_App;

class Mrf_Look_N_Feel extends Mrf_Settings {

	public function prepare_page( $context ) {
		$context = parent::prepare_page( $context );

		$context->page = 'brandingpress/siteIdentity';

		return $context;
	}

	public function get_setting_id() {
		return 'looknfeel';
	}
}
