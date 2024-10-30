<?php

namespace Base\Descriptor\Layout;

use Ioc\Marfeel_Press_App;

class Photo2_Layout_Composer extends Layout_Composer {

	const REQUIRED_ARTICLES = 2;

	public function get_required_articles() {
		return self::REQUIRED_ARTICLES;
	}
}
