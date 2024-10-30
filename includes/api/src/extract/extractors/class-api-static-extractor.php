<?php

namespace API\Extract\Extractors;

use stdClass;
use Ioc\Marfeel_Press_App;

class Api_Static_Extractor extends Api_Items_Extractor {

	protected function get_item_hints() {
		$press_service = Marfeel_Press_App::make( 'press_service' );
		$item = $press_service->get_item();
		$press_service->fill_item_body_parsed( $item );

		$item->pocket = new stdClass();
		$item->pocket->layout = 'static';
		$item->pocket->invalidate = true;

		Marfeel_Press_App::make( 'top_media_service' )->get_items_top_media( array( $item ) );

		return array( $item );
	}
}
