<?php

namespace Base\Entities\Insight\Events;

class Plugin_Deactivation_Event extends Press_Event {

	/** @var string */
	public $action = 'plugin_deactivation';

	/** @var string */
	public $reason;

	/** @var string */
	public $reasonMsg; // @codingStandardsIgnoreLine

	public function __construct( $reason, $reason_msg ) {
		parent::__construct();

		$this->reason = $reason;
		$this->reasonMsg = $reason_msg; // @codingStandardsIgnoreLine
		$this->segment_action = 'plugin/deactivated';
	}
}
