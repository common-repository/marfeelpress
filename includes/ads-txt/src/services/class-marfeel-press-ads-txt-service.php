<?php

namespace Ads_Txt\Services;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Ads_Txt_Service {

	const CACHE_TIME = 3600;
	const ADS_TXT_LACKS_MARFEEL_LINES = 1;
	const ADS_TXT_OPTION = 'ads.ads_txt';

	public function initialize() {
		$this->get_origin();

		Marfeel_Press_App::make( 'ads_txt_manager' )->update();
		Marfeel_Press_App::make( 'ads_txt_manager' )->plugin_activated();
	}

	public function get_origin() {
		$client = Marfeel_Press_App::make( 'http_client' );
		$url = MRFP_INSIGHT_API . '/adstxt/' . Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' ) . '/origin';

		$response = $client->request( $client::METHOD_POST, $url, array(
			'headers' => array(
				'mrf-secret-key' => Marfeel_Press_App::make( 'insight_service' )->get_insight_key(),
			),
			'body' => '',
		) );

		if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 ) {
			Marfeel_Press_App::make( 'settings_service' )->set( self::ADS_TXT_OPTION . '.content', $response['body'] );
		}
	}

	public function start_with_new_line( $content ) {
		return implode( PHP_EOL, $content );
	}

	public function get_ads_txt_status_from_insight() {
		$client = Marfeel_Press_App::make( 'http_client' );
		$url = MRFP_INSIGHT_API . '/adstxt/' . Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' ) . '/?action=validate';

		return $client->request(
			$client::METHOD_GET,
			$url,
			array(
				'headers' => array(
					'Accept' => 'application/json',
				),
			)
		);
	}

	public function should_refresh( $ads_txt_timestamp, $now ) {
		return $ads_txt_timestamp + self::CACHE_TIME < $now;
	}

	public function is_status_ko( $status ) {
		return $status === self::ADS_TXT_LACKS_MARFEEL_LINES;
	}

	public function update( $force = false ) {
		$settings_service = Marfeel_Press_App::make( 'settings_service' );

		$adstxt_loader = Marfeel_Press_App::make( 'marfeel_ads_txt_loader' );
		$ads_txt = $adstxt_loader->load_merged( $settings_service->get( self::ADS_TXT_OPTION ) );
		$ads_txt->mrf_lines = $adstxt_loader->load_mrf_lines();
		$ads_txt->timestamp = current_time( 'timestamp' );
		$settings_service->set( self::ADS_TXT_OPTION, $ads_txt );

		Marfeel_Press_App::make( 'tracker' )->identify( false );

		return $ads_txt;
	}
}
