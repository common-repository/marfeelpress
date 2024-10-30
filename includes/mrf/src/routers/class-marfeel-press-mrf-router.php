<?php

namespace Mrf\Routers;
/**
 * Marfeel press router for service workers
 *
 * @package marfeel-press-mrf-routers
 */

/**
 * Base router class
 */
use Base\Routers\Marfeel_Press_Base_Router;
use Ioc\Marfeel_Press_App;
use Base\Services\Marfeel_Press_Head_Service;

/**
 * Marfeel Press MRF router class
 */
class Marfeel_Press_MRF_Router extends Marfeel_Press_Base_Router {

	/** @var string */
	protected $mrf_query_var;

	/**
	 * Return true if we want to follow this route.
	 *
	 * @param post $post Current post, can be null.
	 */
	public function __construct( $device_type = null ) {
		parent::__construct( $device_type );
		add_action( 'wp_head', array( new Marfeel_Press_Head_Service(), 'add_gardac_press' ) );
	}

	public function valid_route( $post ) {
		$post_uri = get_permalink( $post );
		$uri_utils = Marfeel_Press_App::make( 'uri_utils' );
		$is_feed = $uri_utils->is_feed();
		$is_amp = $uri_utils->is_amp_uri( strtok( $_SERVER['REQUEST_URI'],'?' ) );
		$is_cherokee = Marfeel_Press_App::make( 'request_utils' )->is_cherokee();

		$this->log_request( $is_feed, $post, $post_uri );

		return ( $this->is_valid_endpoint() || 's' === $this->device_type )
			&& ! $is_feed
			&& ! $is_amp
			&& ! get_query_var( 'embed' )
			|| $is_cherokee;
	}

	/**
	 * Creates the controller for the current route.
	 */
	public function route() {
		Marfeel_Press_App::make( 'proxy' )->execute();
	}

	/**
	 * Returns the value of the name query param for this route
	 */
	protected function get_query_var() {
		if ( ! isset( $this->mrf_query_var ) ) {
			$this->mrf_query_var = apply_filters( 'mrf_query_var', $this->get_query_var_value() );
		}

		return $this->mrf_query_var;
	}

	/**
	 * Returns the value of the query param value for this route
	 */
	protected function get_query_var_value() {
		return 'mrf';
	}

	private function log_request( $is_feed, $post, $post_uri ) {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: mrf valid_route' );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: mrf/is_valid_endpoint(): ' . $this->is_valid_endpoint() );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: mrf/is_mobile: ' . ( 's' === $this->device_type ) );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: mrf/is_feed: ' . $is_feed );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: mrf/is_embed: ' . get_query_var( 'embed' ) );
		$log_provider->debug_if_dev( 'marfeelPressWatcher: mrf/post: ' . wp_json_encode( $post ) );
	}
}
