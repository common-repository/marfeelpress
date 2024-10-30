<?php

namespace Admin\Pages\Settings;

use Base\Entities\Settings\Mrf_Availability_Modes_Enum;
use Ioc\Marfeel_Press_App;

class Mrf_Ads_Txt extends Mrf_Settings {

	public function get_setting_id() {
		return 'adstxt';
	}
}
