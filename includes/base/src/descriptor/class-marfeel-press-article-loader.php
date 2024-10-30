<?php

namespace Base\Descriptor;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Article_Loader {

	/** @var array */
	protected $requirements = array();

	/** @var Marfeel_Item_Buffer */
	protected $buffer;

	public function __construct() {
		$this->buffer = Marfeel_Press_App::make( 'item_buffer' );
	}

	protected function get_key( $section, $filter ) {
		return crc32( $section->name . wp_json_encode( $filter ) );
	}

	public function add_requirements( $articles, $section, $filter, $exclude_used_articles ) {
		if ( $articles > 0 ) {
			$key = $this->get_key( $section, $filter );

			if ( ! isset( $this->requirements[ $key ] ) ) {
				$this->requirements[ $key ] = array(
					'section' => $section,
					'filter' => $filter,
					'key' => $key,
					'required' => 0,
					'exclude_used_articles' => $exclude_used_articles,
				);
			}

			$this->requirements[ $key ]['required'] += $articles;
		}
	}

	public function get_items( $count, $section, $filter ) {
		$key = $this->get_key( $section, $filter );
		$items = array();

		for ( $i = 0; $i < $count; $i++ ) {
			$item = $this->buffer->get_item( $key );
			if ( $item !== null ) {
				$items[] = $item;
			} else {
				break;
			}
		}

		return $items;
	}

	public function load() {
		$press_service = Marfeel_Press_App::make( 'press_service' );

		foreach ( $this->requirements as $requirement ) {
			$filter = array_merge(
				array(
					'exclude' => implode( ',', $this->buffer->get_used_ids() ),
				),
				$requirement['filter']
			);

			$this->buffer->add(
				$requirement['key'],
				$press_service->fetch_section_items( $requirement['section'], $requirement['required'], $filter ),
				$requirement['exclude_used_articles']
			);
		}
	}
}
