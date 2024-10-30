<?php

namespace Base;

use Base\Entities\Insight\Events\Plugin_Uninstallation_Event;
use Ioc\Marfeel_Press_App;
use Base\Trackers\Mrf_Event_Types_Enum;
use Base\Entities\Settings\Mrf_Options_Enum;

class Marfeel_Press_Uninstaller {
	public function __construct() {
		$this->settings_service = Marfeel_Press_App::make( 'settings_service' );
	}

	public function uninstall() {
		$this->settings_service->set( Mrf_Options_Enum::OPTION_TOKEN_HANDSHAKE, false );
		$this->track_with_token();

		$this->settings_service->set( Mrf_Options_Enum::OPTION_INSIGHT_TOKEN, '' );
		$this->track_uninstall();
	}

	protected function track_with_token() {
		Marfeel_Press_App::make( 'insight_service' )->track_settings( $this->settings_service->get( 'marfeel_press' ) );
	}

	protected function track_uninstall() {
		$tracker = Marfeel_Press_App::make( 'tracker' );

		$this->settings_service->set( Mrf_Options_Enum::OPTION_PLUGIN_STATUS, 'UNINSTALLED' );
		$tracker->identify( true );
		$tracker->track( new Plugin_Uninstallation_Event() );
		$tracker->track_to_insight( Mrf_Event_Types_Enum::LIFECYCLE, 'uninstall' );
	}
}
