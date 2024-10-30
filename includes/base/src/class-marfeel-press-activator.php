<?php

namespace Base;
/**
 * Main activation class
 *
 * @package marfeel-press
 */

use Base\Entities\Insight\Events\Plugin_Activation_Event;
use Ioc\Marfeel_Press_App;
use Base\Trackers\Mrf_Event_Types_Enum;

class Marfeel_Press_Activator {

	const OPTION_API_TOKEN = 'marfeel_press.api_token';
	const OPTION_INSIGHT_TOKEN = 'marfeel_press.insight_token';
	const OPTION_PLUGIN_STATUS = 'marfeel_press.plugin_status';

	public function set_wp_token() {
		$settings_service = Marfeel_Press_App::make( 'settings_service' );
		$token = $settings_service->get( self::OPTION_API_TOKEN );

		if ( ! $token ) {
			$new_token = wp_generate_password( 16, false );
			$settings_service->set( self::OPTION_API_TOKEN, $new_token );
		}
	}

	public function activate() {
		Marfeel_Press_App::make( 'error_handler' );

		$activation_requirements_checker = new Marfeel_Press_Activation_Requirements_Checker();

		$is_requirements_met = $activation_requirements_checker->is_requirements_met();
		$requirements_not_met = $activation_requirements_checker->get_requirements_not_met();
		$this->track_activation( $requirements_not_met );

		if ( $is_requirements_met ) {
			$this->create_marfeel_role();
			$this->set_wp_token();
			$this->set_activated_once();

			do_action( 'mrf_plugin_activated' );

			if ( Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.activated_once' ) ) {
				Marfeel_Press_App::make( 'mrf_insight_invalidator_service' )->invalidate_all();
			}

			Marfeel_Press_App::make( 'press_service' )->flush_url_cache();

			// Set transient for Activation
			set_transient( 'mrf_activation_redirect', true, 30 );
		} else {
			if ( isset( $requirements_not_met['post_type'] ) ) {
				wp_die( 'Our plugin is suitable for news publishers and bloggers (blogs & magazines), and not with e-commerce, corporate, classifieds or custom WordPress sites. Please, if you see this message and you have a blog contact us.' );
			} else {
				wp_die( 'MarfeelPress cannot be activated due to incompatible environment. Check if your WordPress/PHP version met the requirements.' );
			}
		}
	}

	protected function create_marfeel_role() {
		$capabilities = array(
			'activate_plugins' => true,
			'edit_theme_options' => true,
			'manage_options' => true,
			'read' => true,
			'install_plugins' => true,
			'upload_plugins' => true,
			'delete_plugins' => true,
			'update_plugins' => true,
			'upload_files' => true,
		);

		if ( get_role( 'marfeel' ) ) {
			remove_role( 'marfeel' );
		}

		add_role( 'marfeel', 'Marfeel User', $capabilities );
	}

	protected function create_and_register_menu( $menu_name, $menu_location ) {
		if ( ! has_nav_menu( $menu_location ) ) {
			$menu_id = $this->create_menu( $menu_name );
			$this->register_menu( $menu_location, $menu_id );
		}
	}

	protected function create_menu( $menu_name ) {
		$menu_name = 'Marfeel ' . $menu_name . ' Menu';
		$menu_exists = wp_get_nav_menu_object( $menu_name );
		if ( ! $menu_exists ) {
			return wp_create_nav_menu( $menu_name );
		}
		return $menu_exists->term_id;
	}

	protected function register_menu( $key, $menu_id ) {
		$this->locations[ $key ] = $menu_id;
	}

	protected function mrf_set_error() {
		set_transient( 'signup_error', true );
	}

	protected function track_activation( $requirements_not_met ) {
		$tracker = Marfeel_Press_App::make( 'tracker' );
		$settings_service = Marfeel_Press_App::make( 'settings_service' );

		$settings_service->set( self::OPTION_PLUGIN_STATUS, 'INSTALLED' );

		Marfeel_Press_App::make( 'insight_service' )->track_settings( $settings_service->get( 'marfeel_press' ) );

		$tracker->identify( true, ! isset( $requirements_not_met['post_type'] ) );
		Marfeel_Press_App::make( 'checks_service' )->send_hard();
		$tracker->track( empty( $requirements_not_met ) ? 'plugin/activation' : 'plugin/activation-failed' );
		$tracker->track_to_insight( Mrf_Event_Types_Enum::LIFECYCLE, 'install' );
	}

	protected function set_activated_once(){
		$settings_service = Marfeel_Press_App::make( 'settings_service' );
		$availability = $settings_service->get_availability();

		if ( $availability == 'ALL' ) {
			$settings_service->set( 'marfeel_press.activated_once', true );
		}
	}
}
