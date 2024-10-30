<?php

namespace Base\Entities;

use Base\Entities\Mrf_Tag_Information;

class Mrf_Ripper_Execution_Result {

	/** @var string */
	public $logs;

	/** @var Base\Entities\Mrf_Item_Hint[] */
	public $items = array();

	/**
	 * @var Base\Entities\Mrf_Tag_Information
	 * @json tagInformation
	 * */
	public $tag_information;

	/** @var string */
	public $metadata;

	public function __construct() {
		$this->tag_information = new Mrf_Tag_Information();
	}
}
