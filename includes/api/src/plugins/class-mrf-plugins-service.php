<?php

namespace API\Plugins;

class Mrf_Plugins_Service {

	public function __construct() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
	}

	public function get() {
		return $this->normalize_plugins( get_plugins() );
	}

	private function normalize_plugins( $plugins ) {
		$normalized_plugins = array();
		$active_plugins = $this->get_active_plugins();

		foreach ( $plugins as $key => $plugin ) {
			$plugin["id"] = $key;

			array_push( $normalized_plugins, $this->normalize_plugin( $plugin, $active_plugins ) );
		}

		return $normalized_plugins;
	}

	private function normalize_plugin( $plugin, $active_plugins ) {
		$normalized_plugin = array(
			"name"        => $plugin["Name"],
			"version"     => $plugin["Version"],
			"enabled"     => in_array( $plugin["id"], $active_plugins ),
		);

		return $normalized_plugin;
	}

	private function get_active_plugins() {
		return get_option( "active_plugins" );
	}
}
