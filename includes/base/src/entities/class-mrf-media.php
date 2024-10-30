<?php

namespace Base\Entities;

class Mrf_Media {

	/** @var string */
	public $alt;

	/** @var string */
	public $caption;

	/**
	 * @var float
	 * @json imageRatio
	 */
	public $image_ratio;

	/** @var string */
	public $src;

	/** @var int */
	public $width;

	/** @var int */
	public $height;

	/**
	 * @var Base\Entities\Mrf_Size
	 * @jsonRemove
	 */
	public $sizes;

}
