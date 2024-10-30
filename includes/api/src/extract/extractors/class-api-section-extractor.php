<?php

namespace API\Extract\Extractors;

use stdClass;
use Base\Descriptor\Marfeel_Press_Layouts_Descriptor_Manager;
use Ioc\Marfeel_Press_App;

class Api_Section_Extractor extends Api_Items_Extractor {

	protected function get_item_hints() {
		$context = new stdClass();
		$context->param = new stdClass();
		$context->param->marfeel_context = '';

		$object = get_queried_object();
		$max_articles = 0;
		if ( $object === null ) {
			$section = Marfeel_Press_App::make( 'section_service' )->get_home_section();
		} else {
			$section = Marfeel_Press_App::make( 'section_service' )->get_default_section( $object );
			$max_articles = get_option( 'posts_per_page' );
		}

		$descriptor_reader = Marfeel_Press_App::make( 'descriptor_body_reader', $section );

		if ( $descriptor_reader->has_content() && ! empty( $descriptor_reader->read()->layouts ) ) {
			$ripper_filter = Marfeel_Press_App::make( 'descriptor_ripper_filter' );
			$manager = new Marfeel_Press_Layouts_Descriptor_Manager( $context, $descriptor_reader, $ripper_filter, $max_articles );
			$items = $manager->get_items();
		} else {
			$items = Marfeel_Press_App::make( 'press_service' )->get_items( null, $max_articles ?: $descriptor_reader->read()->max_articles );
		}

		foreach ( $items as $item ) {
			$item->detail_item = null;
			unset( $item->categories ); // TODO: Remove this line once categories are added in Gutenberg
		}

		return $items;
	}
}
