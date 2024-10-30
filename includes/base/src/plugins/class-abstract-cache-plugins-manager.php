<?php

namespace Base\Plugins;

use Ioc\Marfeel_Press_App;

abstract class Abstract_Cache_Plugins_Manager implements Interface_Cache_Plugins_Manager {

	public function needs_device_detection_fix() {
		return $this->is_installed() && ! $this->has_device_detection();
	}

	public function is_supported() {
		return true;
	}

	abstract public function adapt_to_press();
	abstract public function flush_cache();
	abstract public function is_installed();
	abstract protected function has_device_detection();
}
