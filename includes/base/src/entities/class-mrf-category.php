<?php

namespace Base\Entities;

class Mrf_Category {

	/** @var integer */
	public $id;

	/** @var string */
	public $name;

	/** @var string */
	public $content;

	/** @var Mrf_Category */
	public $parent;

}
