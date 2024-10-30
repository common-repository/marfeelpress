<?php

namespace Base\Services\Definition;

use Base\Entities\Mrf_Marfeel_Definition;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Definition_Settings_Builder implements Marfeel_Press_Definition_Builder {

	public function build( Mrf_Marfeel_Definition $marfeel_definition ) {
		$settings_service = Marfeel_Press_App::make( 'settings_service' );
		$settings = $settings_service->get_option_data();

		$marfeel_definition->merge( $settings );

		$marfeel_definition->marfeel_press->availability = get_option( 'mrf_availability' );
		$this->process_fallbacks( $marfeel_definition );

		return $marfeel_definition;
	}

	protected function process_fallbacks( $marfeel_definition ) {
		$marfeel_definition->post_type = (array) $marfeel_definition->post_type;
	}

	protected function get_font( $font, $select ) {
		if ( empty( $select ) ) {
			return $font;
		}

		return $select;
	}
}
