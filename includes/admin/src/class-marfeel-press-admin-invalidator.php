<?php

namespace Admin;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Admin_Invalidator {

	/** @var Marfeel_Press_Before_Saving_Post_Info */
	public $temporary_post_info;

	private function should_invalidate_post( $post_id ) {
		$post = get_post( $post_id );

		$log_text =
		'Should invalidate post details:\n' .
		'isSetPostType: ' . isset( $post->post_type ) . '\n' .
		'isAllowedPostType: ' . in_array( $post->post_type, Marfeel_Press_App::make( 'definition_service' )->get( 'post_type' ) ) . '\n' .
		'isPublished: ' . ($post->post_status == 'publish') . '\n' .
		'isMarfeelizable: ' . Marfeel_Press_App::make( 'post_service' )->is_marfeelizable( $post ) . '\n' .
		'isValidUrl: ' . Marfeel_Press_App::make( 'post_service' )->has_post_valid_url( $post );

		Marfeel_Press_App::make( 'log_provider' )->write_log( $log_text ,'w' );

		return isset( $post->post_type )
			&& in_array( $post->post_type, Marfeel_Press_App::make( 'definition_service' )->get( 'post_type' ) )
			&& $post->post_status == 'publish'
			&& Marfeel_Press_App::make( 'post_service' )->is_marfeelizable( $post )
			&& Marfeel_Press_App::make( 'post_service' )->has_post_valid_url( $post );
	}

	public function invalidate_post( $post_id ) {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );
		$log_provider->write_log( 'Start Post invalidation for post: ' . $post_id, 'w' );

		if ( $this->should_invalidate_post( $post_id ) ) {
			$log_provider->write_log( 'Post with ID:' . $post_id . ' should be invalidated: ', 'w' );

			return Marfeel_Press_App::make( 'mrf_insight_invalidator_service' )->invalidate_post( get_post( $post_id ) );
		}
	}

	public function save_post_data_pre_update( $post_id, $data ) {
		if ( isset( $data['post_type'] ) && in_array( $data['post_type'], Marfeel_Press_App::make( 'definition_service' )->get( 'post_type' ) ) ) {
			$this->temporary_post_info = new Marfeel_Press_Before_Saving_Post_Info();
			$this->temporary_post_info->permalink = get_permalink( $post_id );
			$this->temporary_post_info->title = $data['post_title'];
			$this->temporary_post_info->except = $data['post_excerpt'];
			$this->temporary_post_info->categories = get_the_category( $post_id );
			return true;
		}
		return false;
	}

	public function invalidate_tags( $post_id ) {
		if ( $this->should_invalidate_post( $post_id ) ) {
			$tags = get_the_tags( $post_id );

			if ( $tags && ! is_wp_error( $tags ) ) {
				return Marfeel_Press_App::make( 'mrf_insight_invalidator_service' )->add_section_array( $tags );
			}
		}

		return null;
	}

	public function invalidate_section( $post_id ) {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );
		$log_provider->write_log( 'Start Section invalidation for post: ' . $post_id, 'w' );

		if ( $this->should_invalidate_post( $post_id ) ) {
			$log_provider->write_log( 'Post should be invalidated: ' . $post_id, 'w' );
			$sections_to_invalidate = $this->get_sections_to_invalidate( $post_id );

			$log_provider->write_log( 'Sections to invalidate for post: ' . $post_id . '\n' .
				'Sections: ' . wp_json_encode( $sections_to_invalidate ), 'w' );

			return Marfeel_Press_App::make( 'mrf_insight_invalidator_service' )->add_section_array( $sections_to_invalidate );
		}

		return null;
	}

	private function get_sections_to_invalidate( $post_id ) {
		$log_provider = Marfeel_Press_App::make( 'log_provider' );

		$sections_to_invalidate = get_the_category( $post_id );

		$log_provider->write_log( 'Initial sections to invalidate: ' . wp_json_encode( $sections_to_invalidate ), 'w' );

		$result = [];
		$press_service = Marfeel_Press_App::make( 'press_service' );

		foreach ( $sections_to_invalidate as $k => $section ) {
			if ( $press_service->is_marfeelizable_category( $section ) ) {
				$parent_sections_string = get_category_parents( $section->term_id, false, '#mrf#' );
				$parent_sections = explode( '#mrf#', $parent_sections_string );
				array_pop( $parent_sections );

				$log_provider->write_log( 'Parent sections to invalidate: ' . wp_json_encode( $parent_sections ), 'w' );

				foreach ( $parent_sections as $parent ) {
					$category_object = get_term_by( 'name', $parent, 'category' );
					if ( $press_service->is_marfeelizable_category( $category_object ) ) {
						$result[] = $category_object;
					}
				}
			} else {
				unset( $sections_to_invalidate[ $k ] );
			}
		}

		return $result ? Marfeel_Press_App::make( 'press_admin_utils' )->get_unique_object_array( $result ) : $sections_to_invalidate;
	}

	public function invalidate_content( $post_id ) {
		if ( ! defined( 'REST_REQUEST' ) ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'Start Content invalidation for post: ' . $post_id, 'w' );

			$this->invalidate_post( $post_id );
			$this->invalidate_section( $post_id );
			$this->invalidate_tags( $post_id );

			Marfeel_Press_App::make( 'mrf_insight_invalidator_service' )->invalidate_sections();
		}
	}
}
