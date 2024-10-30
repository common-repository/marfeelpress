<?php

namespace Base\Entities\Insight\Events;

class Marfeel_Deactivation_Event extends Press_Event {

	public function __construct() {
		parent::__construct();

		$this->segment_action = 'marfeel/deactivated';
	}
}
