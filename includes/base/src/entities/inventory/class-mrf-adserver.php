<?php

namespace Base\Entities\Inventory;

use Base\Entities\Settings\Mrf_Setting;

class Mrf_Adserver extends Mrf_Setting {

	/** @var string */
	public $type;

	/** @var string */
	public $width = 300;

	/** @var string */
	public $height = 250;

	/** @var string */
	public $heights = null;

	/** @var array */
	public $json;

	/** @var array */
	public $rtc_config;

	/** @var string */
	public $layout = null;
}
