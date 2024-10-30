<?php

namespace Base\Services\Definition;

use Base\Entities\Mrf_Marfeel_Definition;
use Base\Services\Definition\Marfeel_Press_Definition_Builder;

class Marfeel_Press_Definition_WP_Builder implements Marfeel_Press_Definition_Builder {

	public function build( Mrf_Marfeel_Definition $marfeel_definition ) {
		$marfeel_definition->uri                  = get_site_url();
		$marfeel_definition->title                = get_option( 'blogname' );

		return $marfeel_definition;
	}

	private function normalize_locale( $locale ) {
		$result = explode( '_', $locale );
		return $result[0];
	}
}
