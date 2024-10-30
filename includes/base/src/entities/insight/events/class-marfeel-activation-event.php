<?php

namespace Base\Entities\Insight\Events;

class Marfeel_Activation_Event extends Press_Event {

	/** @var string */
	public $activationType; // @codingStandardsIgnoreLine

	public function __construct( $errors = array(), $activation_type ) {
		parent::__construct();
		$this->activationType = $activation_type; // @codingStandardsIgnoreLine

		$this->segment_action = 'marfeel/activated';
		$this->error = ! empty( $errors );

		if ( $this->error ) {
			$this->errorMsg = wp_json_encode( $errors ); // @codingStandardsIgnoreLine
		}
	}
}
