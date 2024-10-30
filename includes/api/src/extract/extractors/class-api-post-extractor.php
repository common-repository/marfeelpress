<?php

namespace API\Extract\Extractors;

use Ioc\Marfeel_Press_App;

class Api_Post_Extractor implements Api_Extractor {

	protected function get_item() {
		$press_service = Marfeel_Press_App::make( 'press_service' );

		$item = null;

		if ( $_SERVER['REQUEST_URI'] == 'marfeel-checker' ) {
			$posts = wp_get_recent_posts( array(
				'numberposts' => 1,
				'status' => 'publish',
			) );
			if ( ! empty( $posts ) ) {
				$item = $press_service->get_item( $posts[0] );
			}
		} else {
			$item = $press_service->get_item();
		}

		return $item;
	}

	public function extract() {
		$press_service = Marfeel_Press_App::make( 'press_service' );

		add_filter( 'the_content', array( $press_service, 'external_content_hooks' ) );
		$disable_multipage = Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.disable_multipage' );

		if ( $disable_multipage ) {
			Marfeel_Press_App::make( 'wp_features_service' )->disable_multipage();
		}

		$item = $this->get_item();

		if ( $item !== null ) {
			Marfeel_Press_App::make( 'top_media_service' )->get_items_top_media( array( $item ) );

			$press_service->fill_item_body_parsed( $item );

			Marfeel_Press_App::make( 'head_service' )->extract_metadata( $item->detail_item );
			Marfeel_Press_App::make( 'terms_service' )->add_items_terms( array( $item ) );
		}
		return $item;
	}
}
