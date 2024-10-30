<?php

namespace Base\Descriptor\Layout;

use Ioc\Marfeel_Press_App;
use Base\Entities\Mrf_Item_Hint;

class Raw_Html_Layout_Composer extends Layout_Composer {

	/** @var int */
	protected $consumed_articles = 0;

	/** @var array */
	private $allowed_keys = array(
		'category-description',
		'wordpress-widget',
	);

	public function __construct( $layout ) {
		$this->widgets_service = Marfeel_Press_App::make( 'widgets_service' );
		$layout->repetition = 0;

		parent::__construct( $layout );
	}

	public function get_required_articles() {
		return 0;
	}

	public function get_items() {
		$items = array();
		$key = $this->layout->key;
		$content = $this->get_raw_content( $key );

		if ( $content ) {
			$item_hint = new Mrf_Item_Hint();

			$item_hint->id = $this->layout->section->id;
			$item_hint->title = $this->layout->section->title;
			$item_hint->uri = $this->layout->section->uri;
			$item_hint->is_extractable = false;
			$item_hint->pocket['html'] = $content;
			$item_hint->pocket['key'] = $key;

			array_push( $items, $item_hint );
		}

		return $items;
	}

	private function get_raw_content( $key ) {
		if ( $this->is_allowed_key( $key ) ) {
			return $this->get_raw_html( $key );
		}
	}

	private function is_allowed_key( $key ) {
		return in_array( $key, $this->allowed_keys );
	}

	private function get_raw_html( $key ) {
		if ( $key === 'category-description' ) {
			return $this->layout->section->term->description;
		} elseif ( $key === 'wordpress-widget' ) {
			return $this->widgets_service->get_widget_html_from_id( $this->layout->attr['pocket']['extraction']['id'] );
		}
	}
}
