<?php

namespace API\Definition;

use API\Mrf_API;
use WP_REST_Response;
use WP_REST_Request;
use WP_Error;
use Ioc\Marfeel_Press_App;

abstract class Mrf_Base_API extends Mrf_API {

	const BASE = 'definitions/index';

	/** @var string */
	protected $target_class;

	public function register() {
		register_rest_route( $this->get_namespace(), '/' . self::BASE . '/' . $this->resource_name, $this->get_methods() );
	}

	public function get() {
		$result = Marfeel_Press_App::make( 'json_serializer' )->serialize( $this->read() );
		return new WP_REST_Response( $result, 200 );
	}

	public function post( WP_REST_Request $request ) {
		$body = $request->get_body();

		$errors = $this->validate( $body );

		if ( empty( $errors ) ) {
			$body = Marfeel_Press_App::make( 'json_serializer' )->unserialize( $body, $this->target_class );
			$this->save( $body );
		} else {
			if ( is_object( $errors ) ) {
				return $errors;
			}

			return new WP_Error(
				'invalid_fields',
				'Invalid fields',
				array(
					'status' => 500,
					'fields' => $errors,
				)
			);
		}
		return new WP_REST_Response( $body, 200 );
	}

	public function save( $body ) {
		Marfeel_Press_App::make( 'settings_service' )->set( $this->normalize_resource_name(), $body );
	}

	public function validate( $body ) {
		$client = Marfeel_Press_App::make( 'http_client' );
		$logger = Marfeel_Press_App::make( 'log_provider' );
		$api_url = MRFP_INSIGHT_API . '/tenants/' . Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' ) . '/definitions/index/' . $this->resource_name . '?action=validate';
		$response = $client->request(
			$client::METHOD_POST,
			$api_url,
			array(
				'headers' => array(
					'content-type' => 'application/json',
				),
				'body' => $body,
			)
		);

		if ( is_wp_error( $response ) ) {
			$logger->write_log( 'Validation Critical Error: ' . $api_url . ' | ' . $response->get_error_message(), 'c' );
			return $response;
		}

		$status = $response['response']['code'];
		if ( $status != 200 ) {
			$logger->write_log( 'Validation Error: ' . $api_url . ' | ' . $status );
			return new WP_Error( 500, 'Can not validate request' );
		}
		if ( json_decode( $response['body'] )->status != 'OK' ) {
			return $response['errors'];
		}

		return null;
	}

	public function read() {
		return Marfeel_Press_App::make( 'definition_service' )->get( $this->normalize_resource_name() );
	}

	protected function normalize_resource_name() {
		return str_replace( '/', '.', $this->resource_name );
	}

}
