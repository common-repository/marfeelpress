<?php

namespace Base;

use Ioc\Marfeel_Press_App;
use Base\Services\Marfeel_Press_Settings_Service;

class Marfeel_Press_Updater {

	public function update( $upgrader_object, $options ) {
		if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
			foreach ( $options['plugins'] as $each_plugin ) {
				Marfeel_Press_App::make( 'log_provider' )->write_log( "Start Updating MarfeelPress" ,'d' );
				if ( $each_plugin == MRFP_MARFEEL_PRESS_PLUGIN_NAME ) {
					$this->set_activated_once();
					Marfeel_Press_App::make( 'ads_txt_manager' )->update();

					do_action( 'mrf_plugin_update' );
				}
			}
		}
	}

	private function set_activated_once() {
		$settings_service = new Marfeel_Press_Settings_Service();
		$activated_once = $settings_service->get( 'marfeel_press.activated_once' );

		if ( empty( $activated_once ) ) {
			$activated = $settings_service->get_availability() === 'ALL';
			$settings_service->set( 'marfeel_press.activated_once', $activated );
		}
	}
}

