<?php

namespace Base\Entities\Inventory;

use Base\Entities\Settings\Mrf_Setting;

class Mrf_Inventory extends Mrf_Setting {

	/** @var Base\Entities\Inventory\Mrf_Placement[] */
	public $placements = array();

	/**
	 * @var Base\Entities\Inventory\Mrf_Adserver[]
	 * @json adServers
	 */
	public $ad_servers = array();

	/** @var int */
	public $timestamp = 0;
}
