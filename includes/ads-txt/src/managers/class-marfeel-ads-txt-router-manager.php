<?php

namespace Ads_Txt\Managers;

use Ioc\Marfeel_Press_App;

class Marfeel_Ads_Txt_Router_Manager extends Marfeel_Ads_Txt_Manager {

	public function is_valid() {
		return true;
	}

	public function save( $lines ) {
		$lines = Marfeel_Press_App::make( 'marfeel_press_ads_txt_service' )->start_with_new_line( $lines );
		Marfeel_Press_App::make( 'settings_service' )->set( 'ads.ads_txt.content_merged', $lines );
	}

	public function plugin_init() {
		Marfeel_Press_App::make( 'ads_txt_router' );
	}
}
