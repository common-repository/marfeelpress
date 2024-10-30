<?php

namespace Base\Entities\Layouts;

use Base\Entities\Mrf_Section;

class Mrf_Layout {

	/** @var string */
	public $name;

	/** @var int */
	public $count;

	/** @var Mrf_Section */
	public $section;

	/** @var int */
	public $repetition;

	/** @var int */
	public $page;

	/** @var array */
	public $attr = array();

	/** @var array */
	public $options = array();

	/** @var array */
	public $params = array(
		'filter' => array(),
	);

	/** @var string */
	public $key;

	/** @var bool */
	public $is_main_section = true;
}
