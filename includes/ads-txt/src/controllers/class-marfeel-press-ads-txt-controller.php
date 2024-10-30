<?php

namespace Ads_Txt\Controllers;

use Base\Entities\Settings\Mrf_Tenant_Type;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Ads_Txt_Controller {

	const OPTION_TENANT_TYPE = 'marfeel_press.tenant_type';

	public function render_ads_txt() {
		$settings_service = Marfeel_Press_App::make( 'settings_service' );
		$ads_txt = $settings_service->get( 'ads.ads_txt' );
		$tenant_type = esc_attr( $settings_service->get( self::OPTION_TENANT_TYPE ) );

		if ( ! empty( $ads_txt->content_merged ) ) {
			$this->echo_content( $ads_txt->content_merged );
		} elseif ( $tenant_type === Mrf_Tenant_Type::LONGTAIL ) {
			$this->echo_content( $ads_txt->mrf_lines );
		} else {
			$this->set_404_header();
		}
	}

	private function echo_content( $content ) {
		$this->set_headers();

		echo $content;
	}

	protected function set_headers() {
		header( 'Content-Type: text/plain' );
		header( 'HTTP/1.1 200 OK' );
	}

	protected function set_404_header() {
		header( 'HTTP/1.1 404 Not Found' );
	}
}
