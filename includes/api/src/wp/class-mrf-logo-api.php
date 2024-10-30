<?php

namespace API\WP;

use API\Marfeel_REST_API;
use WP_REST_Response;

class Mrf_Logo_Api extends Mrf_WP_Api {

	public function __construct() {
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	protected function accepted_logo_extension( $url ) {
		return strpos( $url, '.png' ) !== false || strpos( $url, '.svg' ) !== false;
	}

	protected function accepted_logo_size( $image ) {
		return $image[1] >= 80 && $image[2] >= 80;
	}

	public function register() {
		register_rest_route( $this->get_namespace(), '/logo', $this->get_methods() );
	}

	public function get() {
		$result = null;

		if ( has_custom_logo() ) {
			$logo_id = get_theme_mod( 'custom_logo' );
			$image = wp_get_attachment_image_src( $logo_id , 'full' );

			if ( $this->accepted_logo_extension( $image[0] ) && $this->accepted_logo_size( $image ) ) {
				$result = array(
					'url' => $image[0],
					'width' => $image[1],
					'height' => $image[2],
				);
			}
		}

		return new WP_REST_Response( $result, 200 );
	}

}
