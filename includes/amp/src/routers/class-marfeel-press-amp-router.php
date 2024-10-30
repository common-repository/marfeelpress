<?php

namespace Amp\Routers;

use Base\Routers\Marfeel_Press_Base_Router;
use Base\Utils\Marfeel_Press_WP_Priority;
use Amp\Services\Marfeel_Press_Amp_Head_Service;
use Ioc\Marfeel_Press_App;
use Base\Marfeel_Press_Plugin_Conflict_Manager;

class Marfeel_Press_AMP_Router extends Marfeel_Press_Base_Router {

	/** @var bool */
	protected $active = false;

	/** @var string|null */
	protected $amp_query_var;

	public function __construct() {
		parent::__construct();

		$definition = Marfeel_Press_App::make( 'settings_service' )->get();
		$this->active = $definition->marfeel_press->amp->activate;

		new Marfeel_Press_Amp_Head_Service( $this->active );
	}

	public function valid_route( $post ) {
		$amp_service = Marfeel_Press_App::make( 'amp_service' );
		$post_uri = get_permalink( $post );
		$amp_query_value = get_query_var( $this->amp_query_var );

		$this->log_request( $post_uri );

		if ( $this->is_valid_endpoint() && $amp_query_value != '1' ) {
			Marfeel_Press_App::make( 'wp_service' )->force_404();
			return false;
		}

		return $this->active
			&& $amp_query_value == '1'
			&& $this->is_valid_endpoint()
			&& $amp_service->is_post_amp_active( $post );
	}

	public function route() {
		Marfeel_Press_Plugin_Conflict_Manager::start_amp();
		Marfeel_Press_App::make( 'proxy' )->execute();
	}

	protected function get_priority() {
		return Marfeel_Press_WP_Priority::HIGH;
	}

	protected function get_query_var() {
		if ( $this->amp_query_var === null ) {
			$this->amp_query_var = apply_filters( 'amp_query_var', $this->get_query_var_value() );
		}

		return $this->amp_query_var;
	}

	protected function get_query_var_value() {
		return 'amp';
	}

	private function log_request( $post_uri ) {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: amp/valid_route' );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: amp/valid_route/post_uri: ' . $post_uri );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: amp/valid_route/active: ' . $this->active );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: amp/valid_route/is_valid_endpoint(): ' . $this->is_valid_endpoint() );
	}
}
