<?php

namespace Pwa\Routers;
/**
 * Marfeel press router for service workers
 *
 * @package marfeel-press-pwa-routers
 */

use Base\Routers\Marfeel_Press_Rewrite_Router;
use Base\Utils\Marfeel_Press_WP_Priority;
use Pwa\Controllers\Marfeel_Press_SW_Controller;

class Marfeel_Press_SW_Router extends Marfeel_Press_Rewrite_Router {

	/** @var string */
	protected $sw_query_var;

	public function route() {
		$sw_controller = new Marfeel_Press_SW_Controller();
		$sw_controller->render_service_worker();
		exit;
	}

	public function is_marfeelizable() {
		return true;
	}

	public function get_query_var() {
		if ( ! isset( $this->sw_query_var ) ) {
			$this->sw_query_var = apply_filters( $this->get_query_var_value(), $this->get_path_value() );
		}

		return $this->sw_query_var;
	}

	public function get_path_value() {
		return 'marfeel_sw(?:\.js)?$';
	}

	public function get_query_var_value() {
		return 'sw_mrf_query_var';
	}
}
