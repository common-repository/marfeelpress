<?php

namespace Base\Entities\Settings;

use Ioc\Marfeel_Press_App;

class Mrf_Tenant_Type {
	const ENTERPRISE = 'ENTERPRISE';
	const LONGTAIL = 'LONGTAIL';

	public static function is_longtail() {
		return Marfeel_Press_App::make( 'settings_service' )->get( Mrf_Options_Enum::OPTION_TENANT_TYPE ) == self::LONGTAIL;
	}
}
