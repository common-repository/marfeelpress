<?php

namespace API\WP;

use API\Mrf_API;

class Mrf_WP_Api extends Mrf_API {

	public function get_namespace() {
		return parent::get_namespace() . '/wp';
	}
}
