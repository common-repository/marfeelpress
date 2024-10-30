<?php

namespace Base\Entities\Insight\Events;

class Plugin_Activation_Event extends Press_Event {

	public function __construct( $errors = array() ) {
		parent::__construct();

		$this->error = ! empty( $errors );
		$this->segment_action = $this->error ? 'plugin/activation-failed' : 'plugin/activation';

		if ( $this->error ) {
			$this->errorMsg = wp_json_encode( $errors ); // @codingStandardsIgnoreLine
		}
	}
}
