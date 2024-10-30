<?php

namespace Base;

class Marfeel_Press_Module {

	public function __construct() {
		$this->add_action( 'plugin_init' );
		$this->add_action( 'plugin_activated' );
		$this->add_action( 'plugin_updated' );
		$this->add_action( 'plugin_deactivated' );
	}

	protected function add_action( $method, $priority = 10, $arguments = 1 ) {
		if ( method_exists( $this, $method ) ) {
			add_action( 'mrf_' . $method, array( $this, $method ), $priority, $arguments );
		}
	}
}
