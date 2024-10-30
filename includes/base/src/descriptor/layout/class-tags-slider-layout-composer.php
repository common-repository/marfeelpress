<?php

namespace Base\Descriptor\Layout;

use Base\Entities\Layouts\Mrf_Layout;
use Ioc\Marfeel_Press_App;

class Tags_Slider_Layout_Composer extends Layout_Composer {

	public function __construct( Mrf_Layout $layout ) {
		$this->decorator = Marfeel_Press_App::make( 'tags_slider_decorator_' . $layout->attr['type'], $layout->attr );

		parent::__construct( $layout );
	}

	protected function get_context( $context ) {
		parent::get_context( $context );

		$this->decorator->build_context( $context );

		return empty( $context->items ) ? null : $context;
	}

	public function get_required_articles() {
		return $this->decorator->get_required_articles();
	}

	public function get_items() {
		$items = parent::get_items();

		if ( $this->add_to_ripper() && $this->layout->key !== null ) {
			foreach ( $items as $item ) {
				$item->pocket['widget'] = $this->layout->key;
			}
		}

		return $items;
	}
}
