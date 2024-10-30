<?php

namespace Base\Descriptor\Layout\Decorator\Tags_Slider;

class Tags_Slider_Menu_Decorator extends Tags_Slider_Decorator {

	public function get_required_articles() {
		return 0;
	}

	public function build_context( $context ) {
		$context->title = $this->attributes['title'];
		$context->items = wp_get_nav_menu_items( $this->attributes['name'] );
	}
}
