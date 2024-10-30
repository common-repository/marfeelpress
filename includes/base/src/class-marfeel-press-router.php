<?php

namespace Base;

use Ioc\Marfeel_Press_App;
use Base\Entities\Settings\Mrf_Options_Enum;

class Marfeel_Press_Router {

	public function __construct() {
		if ( Marfeel_Press_App::make( 'settings_service' )->get( Mrf_Options_Enum::OPTION_MRF_ROUTER ) ) {
			Marfeel_Press_App::make( 'mrf_router' );
		}

		Marfeel_Press_App::make( 'sw_router' );

		if ( Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.amp.activate' ) ) {
			Marfeel_Press_App::make( 'amp_router' );
		}

		Marfeel_Press_App::make( 'log_provider' )->debug_if_dev( 'marfeelPressWatcher: Marfeel_Press_Router created' );
	}
}
