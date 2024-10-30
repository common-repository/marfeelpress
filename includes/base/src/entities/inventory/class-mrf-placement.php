<?php

namespace Base\Entities\Inventory;

use Base\Entities\Settings\Mrf_Setting;

class Mrf_Placement extends Mrf_Setting {

	/**
	 * @var string
	 * @json adServer
	 */
	public $ad_server;

	/**
	 * @var array
	 * @json adServers
	 */
	public $ad_servers;

	/** @var array */
	public $params = array();

	public function get_all_ad_servers() {
		if ( $this->ad_server !== null ) {
			return array(
				$this->ad_server,
			);
		}

		return $this->ad_servers;
	}
}
