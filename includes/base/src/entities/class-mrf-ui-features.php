<?php

namespace Base\Entities;

use Base\Entities\Settings\Mrf_Setting;
use Base\Entities\Settings\Mrf_Pwa_Setting;

class Mrf_Ui_Features extends Mrf_Setting {

	/** @var Base\Entities\Settings\Mrf_Pwa_Setting  */
	public $pwa;

	public function __construct() {
		$this->pwa = new Mrf_Pwa_Setting();
	}

	public function get( $key ) {
		if ( property_exists( $this, $key ) ) {
			return $this->$key;
		}

		return null;
	}
}
