<?php

namespace Base\Services;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Post_Service {

	private function contains_non_marfeelizable_category( $categories ) {
		foreach ( $categories as $category ) {
			if ( ! Marfeel_Press_App::make( 'press_service' )->is_marfeelizable_category( $category ) ) {
				return true;
			}
		}
		return false;
	}

	public function is_marfeelizable( $post ) {
		$categories = get_the_category();

		$marfeelizable_post_meta = get_post_meta( $post->ID, 'mrf_marfeelizable', true );
		$is_marfeelizable_post = ! ( is_numeric( $marfeelizable_post_meta ) && $marfeelizable_post_meta == 0 );
		$are_marfeelizable_categories = ! $this->contains_non_marfeelizable_category( $categories );

		return $is_marfeelizable_post && $are_marfeelizable_categories;
	}

	public function get_post_url( $post ) {
		$home_url = Marfeel_Press_App::make( 'uri_utils' )->get_home_url();
		$parsed = wp_parse_url( get_permalink( $post ) );

		$url = $home_url . $parsed['path'];

		if ( isset( $parsed['query'] ) && ! empty( $parsed['query'] ) ) {
			$url .= '?' . $parsed['query'];
		}

		return $url;
	}

	public function has_post_valid_url( $post ) {
		return Marfeel_Press_App::make( 'uri_utils' )->is_valid_url( $this->get_post_url( $post ) );
	}
}
