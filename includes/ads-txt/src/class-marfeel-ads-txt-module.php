<?php

namespace Ads_Txt;

use Base\Marfeel_Press_Module;
use Ioc\Marfeel_Press_App;

class Marfeel_Ads_Txt_Module extends Marfeel_Press_Module {

	public function plugin_init() {
		Marfeel_Press_App::make( 'ads_txt_manager' )->plugin_init();
	}

	public function plugin_updated() {
		Marfeel_Press_App::make( 'ads_txt_manager' )->plugin_activated();
	}

	public function plugin_activated() {
		Marfeel_Press_App::make( 'marfeel_press_ads_txt_service' )->initialize();
	}

	public function plugin_deactivated() {
		Marfeel_Press_App::make( 'ads_txt_manager' )->plugin_deactivated();
	}
}
