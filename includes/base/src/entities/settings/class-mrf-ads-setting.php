<?php

namespace Base\Entities\Settings;

use Base\Entities\Settings\Mrf_Setting;
use Base\Entities\Advertisement\Mrf_Ads_Txt;

class Mrf_Ads_Setting extends Mrf_Setting {

	/** @var Base\Entities\Advertisement\Mrf_Ads_Txt */
	public $ads_txt;

	public function __construct() {
		$this->ads_txt = new Mrf_Ads_Txt();
	}
}
