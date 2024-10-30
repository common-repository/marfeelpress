<?php

namespace Base\Entities\Insight;

class Mrf_Section_Definitions {

	/** @var string */
	public $name;

	/** @var string */
	public $title;

	/** @var string */
	public $type;

	/**
	 * @var Base\Entities\Insight\Mrf_Feed_Definitions[]
	 * @json feedDefinitions
	 */
	public $feed_definitions = array();
}
