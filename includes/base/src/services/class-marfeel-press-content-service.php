<?php

namespace Base\Services;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Content_Service {

	private function disable_content_output() {
		add_filter('the_content', function( $content ) {
			ob_start();
			return $content;
		}, 0);
		add_filter('the_content', function( $content ) {
			ob_end_clean();
			return $content;
		}, PHP_INT_MAX);
	}

	public function get_post_content( $item ) {
		$this->disable_content_output();

		add_filter( 'the_content', array( $this, 'parse_content' ), 12, 2 );
		add_filter( 'the_content', array( $this, 'add_input_post_id' ), 12, 2 );

		$post = Marfeel_Press_App::make( 'press_service' )->get_queried_post();

		return apply_filters( 'the_content', $post->post_content, $item );
	}

	public function parse_content( $content, $item = null ) {
		if ( $item ) {
			$content = $this->clean_duplicated_top_media( $content, $item );
		}

		return $content;
	}

	public function add_input_post_id( $content, $item = null ) {
		if ( comments_open( $item->id ) ) {
			$input = '<input type="hidden" name="comment_post_ID" value="' . $item->id . '" id="comment_post_ID" />';
		}

		return $content . $input;
	}

	protected function clean_duplicated_top_media( $content, $item ) {
		$top_media_url = basename( $item->media->src );

		if ( $top_media_url ) {
			preg_match( '/<img [^>]*>/', $content, $content_images );

			if ( strpos( $content_images[0], $top_media_url ) !== false ) {
				$content = str_replace( $content_images[0], '', $content );
			}
		}

		return $content;
	}
}
