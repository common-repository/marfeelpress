<?php

namespace API\Definition;

use API\Definition\Mrf_Base_API;
use API\Marfeel_REST_API;
use Ioc\Marfeel_Press_App;

class Mrf_Ads_Txt_Update_API extends Mrf_Base_API {

	public function __construct() {
		$this->resource_name = 'ads/ads_txt/update';
		$this->target_class = 'Base\Entities\Advertisement\Mrf_Ads_Txt';
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_CREATABLE,
		);
	}

	public function validate( $body ) {
		return null;
	}

	public function post( $request ) {
		$settings_service = Marfeel_Press_App::make( 'settings_service' );

		$body = json_decode( $request->get_body(), true );

		foreach ( $body as $prop => $value ) {
			$settings_service->set( 'ads.ads_txt.' . $prop, $value );

			if ( $prop === 'content_merged' ) {
				Marfeel_Press_App::make( 'ads_txt_manager' )->save( $value );
			}
		}
	}
}
