<?php

namespace Base\Services;

use Ioc\Marfeel_Press_App;
use Base\Entities\Insight\Events\Marfeel_Activation_Event;

class Marfeel_Press_Warda_Service {

	const ACTIVATION_TYPE_GARDA = 'GARDA';
	const ACTIVATION_TYPE_WARDA = 'WARDA';

	public function track_warda_if_needed( $previous_warda, $current_warda, $previous_availability, $current_availability ) {
		if ( $current_availability === 'ALL' && ( $previous_warda !== $current_warda ) && ( $previous_availability === $current_availability ) ) {
			$activation_type = $this->get_activation_type( $current_warda );
			$tracker = Marfeel_Press_App::make( 'tracker' );
			$tracker->identify( true );

			$tracker->track( new Marfeel_Activation_Event( array(), $activation_type ) );
		}
	}

	public function get_activation_type( $warda_active ) {
		return $warda_active ? self::ACTIVATION_TYPE_WARDA : self::ACTIVATION_TYPE_GARDA;
	}
}
