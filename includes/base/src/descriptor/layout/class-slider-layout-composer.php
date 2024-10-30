<?php

namespace Base\Descriptor\Layout;

use Ioc\Marfeel_Press_App;

class Slider_Layout_Composer extends Layout_Composer {

	public function get_required_articles() {
		return $this->layout->attr['count'];
	}
}
