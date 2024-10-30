<?php

namespace Base;

use Ioc\Marfeel_Press_App;
use Base\Marfeel_Press_Router;
use Base\Marfeel_Press_Ripper_Tool;
use Base\Entities\Settings\Mrf_Availability_Modes_Enum;

/**
 * Main class
 *
 * @package marfeel-press
 */

/**
 * Marfeel Press object. Manages all the logic of the plugin.
 **/
class Marfeel_Press {

	/** @var string */
	protected $plugin_name;

	/** @var string */
	protected $version;

	/** @var Marfeel_Press_Router; */
	protected $marfeel_press_router;

	public function __construct() {
		if ( defined( 'MRFP_PLUGIN_VERSION' ) ) {
			$this->version = MRFP_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = MRFP_MARFEEL_PRESS_PLUGIN_NAME;
	}

	public function run() {
		Marfeel_Press_App::make( 'log_provider' )->debug_if_dev( 'marfeelPressWatcher: started running MarfeelPress plugin version: ' . MRFP_PLUGIN_VERSION );

		add_action( 'plugins_loaded', array( $this, 'marfeel_press_init' ), 9 );
		add_action( 'rest_api_init', array( $this, 'marfeel_press_rest_init' ), 9 );
	}

	private function should_route() {
		$availability_mode = Marfeel_Press_App::make( 'settings_service' )->get_availability();
		Marfeel_Press_App::make( 'log_provider' )->debug_if_dev( 'marfeelPressWatcher: mode is ' . $availability_mode );

		return ( $availability_mode === Mrf_Availability_Modes_Enum::ALL
		|| ( $availability_mode === Mrf_Availability_Modes_Enum::LOGGED
		&& is_user_logged_in() ) );

	}

	public function marfeel_press_init() {
		Marfeel_Press_App::make( 'log_provider' )->debug_if_dev( 'marfeelPressWatcher: init' );
		Marfeel_Press_App::make( 'error_handler' );

		do_action( 'mrf_plugin_init', $this->should_route() );

		if ( ! is_admin() && $this->should_route() ) {
			Marfeel_Press_App::make( 'custom_service' )->include_custom();

			new Marfeel_Press_Router();

			Marfeel_Press_App::make( 'head_service' )->add_robots();
			Marfeel_Press_App::make( 'head_service' )->add_resizer();
		}

		Marfeel_Press_App::make( 'head_service' )->add_mrf_extractable_false_if_needed();
		Marfeel_Press_App::make( 'head_service' )->add_generator_marfeel_if_needed();

		if ( Marfeel_Press_App::make( 'request_utils' )->is_ripper() ) {
			new Marfeel_Press_Ripper_Tool();
		}

		if ( is_admin() ) {
			Marfeel_Press_App::make( 'admin' );

			new Marfeel_Press_Router();
		}

		$invalidator = Marfeel_Press_App::make( 'press_admin_invalidator' );
		add_action( 'publish_post', array( $invalidator, 'invalidate_content' ), 1 );
	}

	public function marfeel_press_rest_init() {
		if ( ! is_admin() ) {
			Marfeel_Press_App::make( 'rest_api' )->register();
		}
	}
}
