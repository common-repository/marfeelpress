<?php

namespace Base\Entities\Advertisement;

use Base\Entities\Settings\Mrf_Setting;

class Mrf_Ads_Txt extends Mrf_Setting {

	/** @var boolean */
	public $has_plugin = false;

	/** @var string */
	public $mrf_lines = '';

	/** @var string */
	public $content = '';

	/** @var string */
	public $content_merged = '';

	/** @var int */
	public $status = 0;

	/** @var int */
	public $timestamp = 0;

	/** @var bool */
	public $had_file;

	/** @var bool */
	public $merged;
}
