<?php

namespace Pwa\Controllers;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_SW_Controller {

	protected function set_headers() {
		header( 'Content-Type: application/javascript' );
		header( 'HTTP/1.1 200 OK' );
	}

	/**
	 * Render's the service worker javascript
	 **/
	public function render_service_worker() {
		$this->set_headers();

		$this->add_context_variables();

		echo Marfeel_Press_App::make( 'filesystem_wrapper' )->get_contents( MRFP__MARFEEL_PRESS_DIR . '/includes/pwa/src/resources/sw.js' );
	}

	protected function add_context_variables() {
		$context_variables = array(
			'adminURL' => admin_url(),
			'loginURL' => wp_login_url(),
		);

		$this->print_variables( $context_variables );
	}

	protected function print_variables( $context_variables ) {
		echo '!(function(){"use strict";self.params={};';
		foreach ( $context_variables as $key => $value ) {
			echo 'self.params.' . $key . '="' . $value . '";';
		}
		echo '}());';
	}
}
