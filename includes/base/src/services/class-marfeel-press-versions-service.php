<?php

namespace Base\Services;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Versions_Service {

	public function init() {
		$settings_service = Marfeel_Press_App::make( 'settings_service' );
		$versions = $settings_service->get( 'marfeel_press.versions' );

		if ( ! isset( $versions['timestamp'] ) || $versions['timestamp'] < time() ) {
			$versions_url = MRFP_ALEXANDRIA_URI . 'latest/marfeelpress.json?_=' . time();
			$response = Marfeel_Press_App::make( 'http_client' )->request( 'GET', $versions_url );

			if ( is_wp_error( $response ) ) {
				Marfeel_Press_App::make( 'log_provider' )->write_log( 'VersionsService: wp error for: ' . $versions_url . ' | ' . $response->get_error_message() );
			} elseif ( $response['response']['code'] == 200 ) {
				$versions = json_decode( $response['body'], true );
				$versions['timestamp'] = time() + HOUR_IN_SECONDS;

				$settings_service->set( 'marfeel_press.versions', $versions );
			}
		}

		foreach ( $versions as $name => $value ) {
			if ( $name != 'timestamp' ) {
				defined( $name ) || define( $name, $value );
			}
		}
	}
}
