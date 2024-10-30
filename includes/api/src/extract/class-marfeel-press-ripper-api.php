<?php

namespace API\Extract;

use API\Mrf_API;
use WP_REST_Response;
use Ioc\Marfeel_Press_App;
use API\Marfeel_REST_API;


class Marfeel_Press_Ripper_API extends Marfeel_Press_Gutenberg_Content_API {

	const WP_CLASS_FOR_SECTION = 'WP_Term';
	const WP_CLASS_FOR_POST = 'WP_Post';

	public function __construct() {
		parent::__construct();
		$this->resource_name = 'ripper';
	}

	public function init_query() {
		global $wp_query;

		$wp_query->is_home = true;
		$show_front = get_option( 'show_on_front' );

		if ( $this->is_show_front_page_or_post( $show_front ) ) {
			$this->set_show_front( $wp_query, $show_front );
		}
	}

	private function is_show_front_page_or_post( $show_front ) {
		return $show_front === 'page' || $show_front === 'post';
	}

	private function set_show_front( $query, $show_front ) {

		$home_post = get_post( get_option( $show_front . '_on_front' ) );

		if ( $home_post->post_type === $show_front ) {
			global $post, $page;

			$post = $home_post;
			$page = $home_post;

			$query->is_singular = true;
			$query->is_page = $show_front === 'page';
			$query->is_post = $show_front === 'post';
			$query->post = $post;
			$query->queried_object = $post;
		}
	}

	protected function get_extractor() {
		$object = get_queried_object();
		if ( $object !== null && get_class( $object ) === self::WP_CLASS_FOR_POST ) {
			return Marfeel_Press_App::make( 'api_extractor_static' );
		} else {
			if ( $this->is_home() ) {
				$this->init_query();
				add_action( 'pre_get_posts', array( $this, 'init_query' ) );
			}

			return Marfeel_Press_App::make( 'api_extractor_section' );
		}
	}

	protected function is_home() {
		return get_queried_object() === null && ( empty( $_SERVER['REQUEST_URI'] ) || $_SERVER['REQUEST_URI'] == '/');
	}

}
