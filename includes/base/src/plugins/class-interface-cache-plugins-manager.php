<?php

namespace Base\Plugins;

use Ioc\Marfeel_Press_App;

interface Interface_Cache_Plugins_Manager {

	function needs_device_detection_fix();
	function adapt_to_press();
	function flush_cache();
	function is_supported();
}
