<?php

namespace API\SignUp\Services;

use Ioc\Marfeel_Press_App;

class Mrf_Insight_Invalidator_Service {

	const MAX_TIMEOUT_INSIGHT = 1;

	const INVALIDATE_INDEX_1 = '/index.html?invalidate=1';

	/** @var string */
	public $action_param;

	/** @var string */
	public $insight_api_token;

	/** @var array */
	private $sections_to_invalidate = array();

	public function __construct() {
		$this->action_param = 'action=invalidate';
		$this->insight_api_token = Marfeel_Press_App::make( 'insight_service' )->get_insight_key();
	}

	public function invalidate_post( $post ) {
		$post_url = Marfeel_Press_App::make( 'post_service' )->get_post_url( $post );
		$marfeel_name_param = Marfeel_Press_App::make( 'marfeel_name_service' )->get_marfeel_name_param( $post_url );
		$api_uri_param = 'uri=' . $post_url;

		$url = MRFP_INSIGHT_API . '/diy/articles?' . $this->action_param . '&' . $api_uri_param . '&' . $marfeel_name_param;

		Marfeel_Press_App::make( 'log_provider' )->write_log( 'Building invalidation url for post: ' . wp_json_encode( $post ),'w' );
		return $this->invalidate( $url, 'Post' );
	}

	public function add_section_array( $section_array ) {
		$this->sections_to_invalidate[] = Marfeel_Press_App::make( 'uri_utils' )->get_home_url();

		foreach ( $section_array as $section ) {
			$this->add_section( $section );
		}

		return true;
	}

	public function invalidate_all() {
		$tenant_home = Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' );

		$url = MRFP_PRODUCER_HOST . '/' . $tenant_home . self::INVALIDATE_INDEX_1;

		return $this->invalidate( $url, 'Tenant' );
	}

	private function add_section( $section ) {
		$term = get_term_link( $section );

		if ( is_wp_error( $term ) ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'Error while invalidating section ' . $section . ' error: ' . $term->get_error_message() );
		} elseif ( Marfeel_Press_App::make( 'uri_utils' )->is_valid_url( $term ) ) {
			$this->sections_to_invalidate[] = $term;
		} else {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'Error while invalidating section ' . $section . ' error: invalid url' );
		}
	}

	public function invalidate_sections() {
		$urls = wp_json_encode( $this->sections_to_invalidate );
		$marfeel_name_param = Marfeel_Press_App::make( 'marfeel_name_service' )->get_marfeel_name_param( $this->sections_to_invalidate[0] );
		$url = MRFP_INSIGHT_API . '/diy/sections?' . $this->action_param . '&' . $marfeel_name_param;

		$response = Marfeel_Press_App::make( 'http_client' )->request( 'POST', $url, array(
			'timeout' => 3,
			'headers' => array(
				'Content-Type' => 'application/json',
				'mrf-secret-key' => $this->insight_api_token,
			),
			'body' => $urls,
		) );

		return $this->manage_response( $urls, 'section', $response );
	}

	private function manage_response( $url, $type, $response ) {
		if ( is_wp_error( $response ) ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( $type . ' invalidation error at: ' . $url . ':' . $response->get_error_message() );
		} elseif ( $response['response']['code'] > 204 ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( $type . ' invalidation error at: ' . $url . ':' . $response['response']['code'] . ':' . $response['body'] );
		}

		return $response;
	}

	private function invalidate( $url, $type ) {
		Marfeel_Press_App::make( 'log_provider' )->write_log( $type . ' invalidated at: ' . $url, 'w' );
		$response = Marfeel_Press_App::make( 'http_client' )->request( 'GET', $url, array(
			'timeout' => 3,
			'headers' => array(
				'mrf-secret-key' => $this->insight_api_token,
			),
		) );

		return $this->manage_response( $url, $type, $response );
	}
}

