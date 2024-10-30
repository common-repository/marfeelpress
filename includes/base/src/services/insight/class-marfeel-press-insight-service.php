<?php


namespace Base\Services\Insight;

use Base\Utils\Http_Client;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Insight_Service {

	public function get_insight_key() {
		return Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.insight_token' );
	}

	public function get_insight_url() {
		return Marfeel_Press_App::make( 'request_utils' )->is_dev() && $_SERVER['HTTP_HOST'] == MRF_DEV_DOMAIN ? MRFP_DEV_INSIGHT_URL : MRFP_INSIGHT_URL;
	}

	public function get_base_api() {
		$tenant_home = Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' );
		$base_url = Marfeel_Press_App::make( 'request_utils' )->is_dev() && $_SERVER['HTTP_HOST'] == MRF_DEV_DOMAIN ? MRFP_DEV_INSIGHT_API : MRFP_INSIGHT_API;

		if ( Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.avoid_query_params' ) ) {
			$base_url = str_replace( "?rest_route=", "wp-json", $base_url );
			$base_url = str_replace( "&url", "?url", $base_url );
		}

		return $base_url . '/tenants/' . preg_replace( '/^www\./i', '', $tenant_home );
	}

	public function track_settings( $settings ) {
		Marfeel_Press_App::make( 'http_client' )->request( Http_Client::METHOD_POST, $this->get_base_api() . '/definitions/index/pressSettings', array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'mrf-secret-key' => $this->get_insight_key(),
			),
			'body' => wp_json_encode( $settings ),
		) );
	}
}
