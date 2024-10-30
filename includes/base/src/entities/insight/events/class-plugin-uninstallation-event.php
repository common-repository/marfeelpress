<?php

namespace Base\Entities\Insight\Events;

class Plugin_Uninstallation_Event extends Press_Event {

	public function __construct() {
		parent::__construct();

		$this->segment_action = 'plugin/uninstall';
	}
}
