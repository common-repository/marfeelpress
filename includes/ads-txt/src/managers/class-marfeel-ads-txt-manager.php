<?php

namespace Ads_Txt\Managers;

use Base\Entities\Settings\Mrf_Tenant_Type;
use Ioc\Marfeel_Press_App;

abstract class Marfeel_Ads_Txt_Manager {

	const OPTION_TENANT_TYPE = 'marfeel_press.tenant_type';

	public abstract function is_valid();

	public function update() {
		$ads_txt = Marfeel_Press_App::make( 'marfeel_press_ads_txt_service' )->update();

		$tenant_type = esc_attr( Marfeel_Press_App::make( 'settings_service' )->get( self::OPTION_TENANT_TYPE ) );

		if ( ! empty( $ads_txt->content_merged ) ) {
			$this->save( $ads_txt->content_merged );
		} elseif ( $tenant_type === Mrf_Tenant_Type::LONGTAIL ) {
			$this->save( $ads_txt->mrf_lines );
		}
	}

	public function save( $lines ) {}

	public function plugin_init() {}

	public function plugin_activated() {}

	public function plugin_deactivated() {}
}
