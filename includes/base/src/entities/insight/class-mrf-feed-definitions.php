<?php

namespace Base\Entities\Insight;

class Mrf_Feed_Definitions {

	/** @var string */
	public $uri;

	/**
	 * @var Base\Entities\Insight\Mrf_Alibaba_Definition
	 * @json alibabaDefinition
	 */
	public $alibaba_definition;

	public function __construct() {
		$this->alibaba_definition = new Mrf_Alibaba_Definition();
	}
}
