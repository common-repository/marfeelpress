<?php

namespace Base\Entities;

use Base\Entities\Settings\Mrf_Setting;

class Mrf_Cherokee extends Mrf_Setting {

	/** @var array */
	public $apps;

	public function __construct() {
		$this->apps = array();
	}
}
