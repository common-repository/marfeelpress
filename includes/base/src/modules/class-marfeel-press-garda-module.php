<?php

namespace Base\Modules;

use Base\Marfeel_Press_Module;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Garda_Module extends Marfeel_Press_Module {

	public function plugin_init( $should_route ) {
		if ( ! is_admin() && $should_route ) {
			Marfeel_Press_App::make( 'head_service' )->add_marfeelgarda_if_needed();
		}
	}
}
