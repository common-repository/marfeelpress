<?php

namespace Base\Plugins;

class Wp_Super_Cache_Plugin_Manager extends Abstract_Cache_Plugins_Manager {

	public function adapt_to_press() {
		global $wp_cache_mobile_enabled, $wp_cache_config_file;

		$wp_cache_mobile_enabled = 1;
		wp_cache_replace_line( '^ *\$wp_cache_mobile_enabled', "\$wp_cache_mobile_enabled = 1;", $wp_cache_config_file );
	}

	public function flush_cache() {
		wp_cache_clear_cache();
	}

	public function is_installed(){
		global $wp_cache_config_file;

		return  isset( $wp_cache_config_file );
	}

	protected function has_device_detection() {
		global $wp_cache_mobile_enabled;

		return isset( $wp_cache_mobile_enabled ) && false != $wp_cache_mobile_enabled;
	}
}
