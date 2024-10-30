<?php

namespace Base\Plugins;

use Ioc\Marfeel_Press_App;

class Breeze_Plugin_Manager extends Abstract_Cache_Plugins_Manager {

	public function adapt_to_press() {
	}

	public function flush_cache() {
	}

	public function is_installed(){
		return function_exists( 'breeze_ob_start_callback' );
	}

	protected function has_device_detection() {
		return false;
	}

	public function is_supported() {
		return false;
	}
}
