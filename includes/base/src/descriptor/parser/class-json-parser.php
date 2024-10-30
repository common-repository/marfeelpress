<?php

namespace Base\Descriptor\Parser;

use Base\Entities\Mrf_Section;
use Base\Entities\Layouts\Mrf_Layout;
use Base\Entities\Layouts\Mrf_Theme_Descriptor;
use API\Menu\Mrf_Default_Menu_Service;
use Ioc\Marfeel_Press_App;

class Json_Parser {

	/** @var Mrf_Section */
	protected $section;

	public function __construct( $section ) {
		$this->section = $section;
	}

	protected function parse_array_layout( $layout_json ) {
		$layout = new Mrf_Layout();
		$layout->name = $layout_json[0];
		$layout->count = $layout_json[1];
		$layout->repetition = isset( $layout_json[2] ) ? $layout_json[2] : null;

		return $layout;
	}

	protected function is_main_section( $layout ) {
		return $layout->section->id == $this->section->id && empty( $layout->params['filter'] );
	}

	protected function parse_object_layout( $layout_json ) {
		$layout = new Mrf_Layout();
		$layout->name = $layout_json['name'];
		$layout->count = isset( $layout_json['count'] ) ? $layout_json['count'] : 1;
		$layout->repetition = isset( $layout_json['repetition'] ) ? $layout_json['repetition'] : null;
		$layout->page = isset( $layout_json['page'] ) ? $layout_json['page'] : null;

		if ( isset( $layout_json['key'] ) ) {
			if ( isset( $layout_json['attr'] ) && isset( $layout_json['attr']['pocket'] ) && isset( $layout_json['attr']['pocket']['section'] ) ) {
				$pocket_section = $layout_json['attr']['pocket']['section'];

				$layout->section = Marfeel_Press_App::make( 'section_service' )->get_by_slug(
					Mrf_Default_Menu_Service::decode_section_name( $pocket_section )
				);
			} else {
				$layout->section = $this->section;
			}

			$layout->key = $layout_json['key'];
		} elseif ( isset( $layout_json['section'] ) ) {
			$layout->section = Marfeel_Press_App::make( 'section_service' )->get_by_slug(
				Mrf_Default_Menu_Service::decode_section_name( $layout_json['section'] )
			);
			if ( $layout->section == null ) {
				$layout->section = $this->section;
			}
		} elseif ( isset( $layout_json['tag'] ) ) {
			$layout->section = Marfeel_Press_App::make( 'section_service' )->get_tag_by_slug( $layout_json['tag'] );
		}

		if ( isset( $layout_json['options'] ) ) {
			$layout->options = $layout_json['options'];
		}

		if ( isset( $layout_json['title'] ) && $layout->section ) {
			$layout->section->title = $layout_json['title'];
		}

		if ( isset( $layout_json['attr'] ) ) {
			$layout->attr = $layout_json['attr'];
		}

		if ( isset( $layout_json['params'] ) ) {
			$layout->params = array_merge( $layout->params, $layout_json['params'] );
		}

		$layout->is_main_section = $this->is_main_section( $layout );

		return $layout;
	}

	protected function parse_string_layout( $layout_json ) {
		$layout = new Mrf_Layout();
		$layout->name = $layout_json;
		$layout->count = 1;

		return $layout;
	}

	public function parse( $json ) {
		if ( is_string( $json ) ) {
			$json = json_decode( $json, true );
		}

		$descriptor = new Mrf_Theme_Descriptor();
		$descriptor->max_articles = isset( $json['maxArticles'] ) ? $json['maxArticles'] : 30;
		$default_params = isset( $json['params'] ) ? $json['params'] : array();

		if ( isset( $json['layouts'] ) ) {
			foreach ( $json['layouts'] as $layout_json ) {
				if ( is_string( $layout_json ) ) {
					$layout = $this->parse_string_layout( $layout_json );
				} elseif ( is_array( $layout_json ) && isset( $layout_json[0] ) ) {
					$layout = $this->parse_array_layout( $layout_json );
				} else {
					$layout = $this->parse_object_layout( $layout_json );
				}

				if ( $layout->section !== null && isset( $default_params[ $layout->section->name ] ) ) {
					$layout->params = array_merge_recursive( $default_params[ $layout->section->name ], $layout->params );
				}

				if ( ! isset( $layout->attr['pocket']['exclude_used_articles'] ) ) {
					$layout->attr['pocket']['exclude_used_articles'] = true;
				}

				$descriptor->layouts[] = $layout;
			}
		}

		return $descriptor;
	}
}
