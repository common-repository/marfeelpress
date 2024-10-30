<?php

namespace Base\Descriptor\Filter;

use Ioc\Marfeel_Press_App;

class Ripper_Filter implements Filter {

	public function should_add( $layout ) {
		return $layout->add_to_ripper();
	}
}
