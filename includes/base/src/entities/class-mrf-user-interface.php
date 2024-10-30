<?php

namespace Base\Entities;

use Base\Entities\Settings\Mrf_Setting;
use Base\Entities\Mrf_Ui_Features;

class Mrf_User_Interface extends Mrf_Setting {

	/** @var string */
	public $resources_path;

	/** @var string */
	public $logo;

	/** @var boolean*/
	public $respect_top_media_ratio;

	/** @var boolean*/
	public $show_article_tags;

	/** @var Base\Entities\Mrf_Ui_Features */
	public $features;

	public function __construct() {
		$this->features = new Mrf_Ui_Features();
	}

}
