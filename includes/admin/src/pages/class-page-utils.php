<?php


namespace Admin\Pages;

use Ioc\Marfeel_Press_App;
use Base\Entities\Settings\Mrf_Availability_Modes_Enum;

class Page_Utils {
	public function track_mode_change_to_marketing_campaigns( $current_availability, $new_availability ) {
		if ( isset( $_COOKIE['mrf-press-userid'] ) ) {
			$google_code_url = 'https://script.google.com/macros/s/AKfycbxH9ZRzR0FO9gugcIW8XgN7NCAas5CHPTVOTX6AEahZHRZYsww/exec';
			$request_body = array(
				'id' => $_COOKIE['mrf-press-userid'],
			);

			if ( $current_availability === Mrf_Availability_Modes_Enum::ALL && $new_availability !== Mrf_Availability_Modes_Enum::ALL ) {
				$request_body['deactivated'] = true;
			} elseif ( $current_availability !== Mrf_Availability_Modes_Enum::ALL && $new_availability === Mrf_Availability_Modes_Enum::ALL ) {
				$request_body['activated'] = true;
			}
			Marfeel_Press_App::make( 'http_client' )->request( 'POST', $google_code_url, array(
				'body' => wp_json_encode( $request_body ),
			) );
		}
	}
}
