<?php

namespace Base\Services;

class Marfeel_Press_Custom_Service {

	/** @var bool */
	protected $custom_included = false;

	public function get_custom_version( $default = '' ) {
		if ( $this->is_custom_active() ) {
			return MARFEEL_PRESS_CUSTOM_MAJOR_VERSION . "." . MARFEEL_PRESS_CUSTOM_MINOR_VERSION . "." . MARFEEL_PRESS_CUSTOM_BUILD_NUMBER;
		}

		return $default;
	}

	public function is_custom_active() {
		return defined( 'MARFEEL_PRESS_CUSTOM_MAJOR_VERSION' );
	}

	public function include_custom() {
		if ( ! $this->custom_included && $this->is_custom_active() && file_exists( WP_PLUGIN_DIR . '/marfeelpress-custom/includes/index.php' ) ) {
			$this->custom_included = true;
			include_once( WP_PLUGIN_DIR . '/marfeelpress-custom/includes/index.php' );
		}
	}
}
