<?php

namespace API;

use WP_REST_Controller;
use WP_Error;

abstract class Mrf_API extends WP_REST_Controller {

	const VERSION = 1;

	const NAMESPACE_NAME = 'marfeelpress';

	/** @var string */
	public $resource_name;

	/** @var array */
	protected $allowed_methods = array();

	public function __construct() {
		define( 'REST_REQUEST', true );
	}

	public function register() {
		register_rest_route( $this->get_namespace(), '/' . $this->resource_name, $this->get_methods() );
	}

	public function get_namespace() {
		return self::NAMESPACE_NAME . '/v' . self::VERSION;
	}

	protected function unsecure_endpoint() {
		$that = $this;
		add_filter( 'rest_authentication_errors', function() use ( $that ) {
			if ( strpos( $_SERVER['REQUEST_URI'], Mrf_API::NAMESPACE_NAME ) !== false && strpos( $_SERVER['REQUEST_URI'], $that->resource_name ) !== false ) {
				return true;
			}

			return null;
		} );
	}

	protected function get_methods() {
		$methods = array();

		if ( in_array( Marfeel_REST_API::METHOD_READABLE, $this->allowed_methods ) ) {
			$methods[] = array(
				'methods' => Marfeel_REST_API::METHOD_READABLE,
				'callback' => array( $this, 'get' ),
				'permission_callback' => array( $this, 'authenticate' ),
			);
		}

		if ( in_array( Marfeel_REST_API::METHOD_EDITABLE, $this->allowed_methods ) ) {
			$methods[] = array(
				'methods' => Marfeel_REST_API::METHOD_EDITABLE,
				'callback' => array( $this, 'put' ),
				'permission_callback' => array( $this, 'authenticate' ),
			);
		}

		if ( in_array( Marfeel_REST_API::METHOD_CREATABLE, $this->allowed_methods ) ) {
			$methods[] = array(
				'methods' => Marfeel_REST_API::METHOD_CREATABLE,
				'callback' => array( $this, 'post' ),
				'permission_callback' => array( $this, 'authenticate' ),
			);
		}

		return $methods;
	}

	public function authenticate() {
		return true;
	}
}
