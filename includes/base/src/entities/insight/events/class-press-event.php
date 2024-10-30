<?php

namespace Base\Entities\Insight\Events;

class Press_Event {

	/** @var string */
	public $segment_action;

	/** @var string */
	public $action = '';

	/** @var string */
	public $tenant;

	/** @var string */
	public $mediaGroup; // @codingStandardsIgnoreLine

	/** @var bool */
	public $error = false;

	/** @var string */
	public $errorMsg; // @codingStandardsIgnoreLine

	/** @var string */
	public $pluginVersion = MRFP_PLUGIN_VERSION; // @codingStandardsIgnoreLine

	public function __construct() {
		$this->action = str_replace( '_event', '', strtolower( get_class( $this ) ) );
		$this->action = substr( $this->action, strrpos( $this->action, '\\' ) + 1 );
	}
}
