<?php

namespace Base\Entities;

use Base\Entities\Settings\Mrf_Setting;

class Mrf_Push_Notifications extends Mrf_Setting {

	/** @var string */
	public $id;

	/** @var array */
	public $conf = array();
}
