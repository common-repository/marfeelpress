<?php

namespace Base\Descriptor\Layout;

use Ioc\Marfeel_Press_App;

class Balcon_Layout_Composer extends Layout_Composer {

	public function __construct( $layout ) {
		if ( ! isset( $layout->attr['count'] ) ) {
			$layout->attr['count'] = count( $layout->attr['layouts'] );
		}

		parent::__construct( $layout );
	}

	public function add_to_ripper() {
		return $this->layout->key !== null;
	}

	public function get_required_articles() {
		return $this->layout->attr['count'];
	}

	public function get_items() {
		$items = parent::get_items();

		if ( $this->add_to_ripper() ) {
			foreach ( $items as $item ) {
				$item->pocket['key'] = $this->layout->key;
			}
		}

		return $items;
	}
}
