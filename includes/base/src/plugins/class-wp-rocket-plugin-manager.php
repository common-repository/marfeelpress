<?php

namespace Base\Plugins;

class Wp_Rocket_Plugin_Manager extends Abstract_Cache_Plugins_Manager {

	public function adapt_to_press() {
		update_rocket_option( 'cache_mobile', 1 );

		if ( $this->is_modern_version() ) {
			update_rocket_option( 'do_caching_mobile_files', 1 );
		}
	}

	public function flush_cache() {
		rocket_clean_domain();
	}

	public function is_installed() {
		return function_exists( 'rocket_is_plugin_active' );
	}

	protected function has_device_detection() {
		$cache_mobile = get_rocket_option( 'cache_mobile', false );

		if ( $this->is_modern_version() ) {
			return $cache_mobile && get_rocket_option( 'do_caching_mobile_files', false );
		}

		return $cache_mobile;
	}

	private function is_modern_version() {
		return version_compare( WP_ROCKET_VERSION, '2.7', '>=' );
	}
}
