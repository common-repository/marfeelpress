<?php

namespace Base\Services\Definition;

use Base\Entities\Mrf_Marfeel_Definition;
use Base\Services\Definition\Marfeel_Press_Definition_Builder;
use Base\Entities\Settings\Mrf_Availability_Modes_Enum;

class Marfeel_Press_Definition_Default_Builder implements Marfeel_Press_Definition_Builder {

	public function build( Mrf_Marfeel_Definition $marfeel_definition ) {

		$marfeel_definition->name           = 'index';
		$marfeel_definition->tenant_home    = $_SERVER['HTTP_HOST'];

		$marfeel_definition->marfeel_press->home_name           = 'home';
		$marfeel_definition->marfeel_press->availability        = Mrf_Availability_Modes_Enum::OFF;

		return $marfeel_definition;
	}
}
