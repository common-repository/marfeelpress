<?php

namespace Base\Entities\Settings;

use Base\Entities\Settings\Mrf_Setting;
use Base\Entities\Mrf_Push_Notifications;

class Mrf_Pwa_Setting extends Mrf_Setting {

	/** @var int */
	public $activate = 0;

	/**
	 * @var Base\Entities\Mrf_Push_Notifications
	 * @json pushNotifications
	 */
	public $push_notifications;

	public function __construct() {
		$this->push_notifications = new Mrf_Push_Notifications();
	}
}
