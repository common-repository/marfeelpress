<?php


namespace Base\Routers;

use Base\Services\Marfeel_Press_Custom_Header_Service;
use Base\Utils\Marfeel_Press_WP_Priority;
use Base\Utils\Request_Utils;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Base_Router {

	/** @var string */
	protected $device_type;

	/** @var Marfeel_Press_Custom_Header_Service */
	private $custom_header_service;

	public function __construct( $device_type = null ) {
		if ( ! is_null( $device_type ) ) {
			$this->device_type = $device_type;
		} else {
			$this->detect_device();
		}

		if ( null !== $this->get_query_var() ) {
			add_action( 'init', array( $this, 'init_endpoint' ) );
			add_filter( 'request', array( $this, 'mrf_force_query_var_value' ) );
			add_filter( 'query_vars', array( $this, 'init_custom_query_params' ) );
		}

		add_action( 'wp', array( $this, 'route_if_necessary' ), $this->get_priority() );

		$this->custom_header_service = Marfeel_Press_App::make( 'custom_headers_service' );
	}

	protected function is_marfeelizable() {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: is_home(): ' . Marfeel_Press_App::make( 'press_service' )->is_home() );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: wp is_home(): ' . is_home() );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: wp is_front_page(): ' . is_front_page() );

		if ( Marfeel_Press_App::make( 'press_service' )->is_home() ) {
			return true;
		}

		if ( ! Marfeel_Press_App::make( 'press_service' )->is_marfeelizable( get_queried_object() ) ) {
			return false;
		}

		$current_uri = Marfeel_Press_App::make( 'uri_utils' )->get_current_uri();
		$log_provider->debug_if_dev( 'marfeelPressWatcher: current_uri: ' . $current_uri );

		return Marfeel_Press_App::make( 'posts_meta_repository' )->get_marfeelizable( $current_uri );
	}

	public function valid_route( $post ) {
	}

	public function route() {
	}

	protected function get_priority() {
		return Marfeel_Press_WP_Priority::LOW;
	}

	protected function get_query_var() {
		return null;
	}

	public function init_endpoint() {
		$query_var = $this->get_query_var();

		add_rewrite_endpoint( $query_var, EP_PAGES | EP_PERMALINK | EP_AUTHORS | EP_ALL_ARCHIVES | EP_ROOT );
		add_post_type_support( 'post', $query_var );
		add_post_type_support( 'page', $query_var );
	}

	protected function get_endpoint_option() {
		return get_query_var( $this->get_query_var_value(), '' );
	}

	public function init_custom_query_params( $vars ) {
		$vars[] = 'marfeelContext';
		$vars[] = 'marfeelRebase';
		$vars[] = Request_Utils::MARFEEL_DEV_PARAM;
		$vars[] = 'marfeelCherokee';
		$vars[] = Request_Utils::MARFEEL_CHEROKEE_PARAM;
		$vars[] = 'marfeelAds';
		$vars[] = Request_Utils::MARFEEL_TEST_DEVICE_PARAM;

		return $vars;
	}


	public function route_if_necessary() {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: route_if_necessary' );
		$is_cherokee = Marfeel_Press_App::make( 'request_utils' )->is_cherokee();
		$this->user_agent_logs();

		global $wp_query;
		$post = $wp_query->post;

		if ( $this->valid_route( $post ) ) {
			$log_provider->debug_if_dev( 'marfeelPressWatcher: in valid route' );

			if ( $this->accepted_type() && $this->is_marfeelizable() || $is_cherokee ) {
				add_filter( 'the_content', array( Marfeel_Press_App::make( 'press_service' ), 'external_content_hooks' ) );

				$log_provider->debug_if_dev( 'marfeelPressWatcher: accepted type and page is marfeelizable' );
				$this->disable_forbidden_extensions();
				$this->route();
			}
			$this->mark_not_accepted_type();
		}
	}

	protected function disable_forbidden_extensions() {
		// Disable newrelic to not broke AMP pages: https://github.com/ampproject/amphtml/issues/2380
		if ( extension_loaded( 'newrelic' ) ) {
			newrelic_disable_autorum();
		}
	}

	protected function is_valid_taxonomy() {
		return is_category() || is_tag() || is_tax() || is_page();
	}

	protected function accepted_type() {
		Marfeel_Press_App::make( 'log_provider' )->debug_if_dev(
			'marfeelPressWatcher: [
				is_single(): ' . is_single() . ',
				is_home(): ' . Marfeel_Press_App::make( 'press_service' )->is_home() . ',
				is_valid_taxonomy(): ' . $this->is_valid_taxonomy() .
			']'
		);

		return is_single() || Marfeel_Press_App::make( 'press_service' )->is_home() || $this->is_valid_taxonomy();
	}

	protected function mark_not_accepted_type() {
		// TODO: send error about the not accepted type
	}

	protected function is_valid_endpoint() {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: base_router/is_valid_endpoint/get_query_var_value()' . $this->get_query_var_value() );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: base_router/is_valid_endpoint/get_query_var()' . get_query_var( $this->get_query_var_value(), false ) );
		return false !== get_query_var( $this->get_query_var_value(), false );
	}

	public function detect_device() {
		$mobile_detector   = Marfeel_Press_App::make( 'device_detection' );
		$this->device_type = $mobile_detector->get_device_type();
	}

	public function mrf_force_query_var_value( $query_vars ) {
		if ( isset( $query_vars[ $this->get_query_var() ] ) && '' === $query_vars[ $this->get_query_var() ] ) {
			$query_vars[ $this->get_query_var() ] = 1;
		}

		return $query_vars;
	}

	private function get_server_information( $index ) {
		return isset( $_SERVER[ $index ] ) ? $_SERVER[ $index ] : 'nonexistent';
	}

	private function user_agent_logs() {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: user agent: ' . $this->get_server_information( 'HTTP_USER_AGENT' ) );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: varnish_ua_device_headers: ' . $this->get_server_information( 'HTTP_X_UA_DEVICE' ) );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: varnish_device_headers: ' . $this->get_server_information( 'HTTP_X_DEVICE' ) );
	}
}
