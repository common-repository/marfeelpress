<?php

namespace API\Menu;

use Base\Entities\Insight\Mrf_Feed_Definitions;
use Base\Entities\Insight\Mrf_Menu;
use Base\Entities\Insight\Mrf_Section_Definitions;

class Mrf_Default_Menu_Service {

	/** @var array[] */
	private static $blacklist = array( 'marfeel', 'footer', 'social' );


	public function get_default_menu() {
		$mrf_menu = new Mrf_Menu();
		$menu     = $this->get_primary_menu();

		if ( ! empty( $menu ) ) {
			$menu_items = wp_get_nav_menu_items( $menu->term_id );

			foreach ( $menu_items as $key => $menu_item ) {
				$menu_item->level = $this->find_menu_item_level( $menu_item, $menu_items );

				if ( ! $this->is_duplicated_url( $menu_item->url, $mrf_menu->section_definitions ) ) {
					$section_definition = $this->create_section_definition( $menu_item );

					if ( $this->is_valid_for_extraction( $menu_item->url, $section_definition->type ) ) {
						array_push( $mrf_menu->section_definitions, $section_definition );
					}
				}
			}
		}

		return $mrf_menu;
	}

	private function find_menu_item_level( $menu_item, $menu_items ) {
		if ( $menu_item->menu_item_parent == 0 ) {
			return 0;
		}

		return 1 + $this->find_menu_item_level( $this->get_menu_item_by_id( $menu_item->menu_item_parent, $menu_items ), $menu_items );
	}

	private function get_menu_item_by_id( $menu_item_id, $menu_items ) {
		foreach ( $menu_items as $menu_item ) {
			if ( $menu_item->db_id == $menu_item_id ) {
				return $menu_item;
			}
		}
	}

	private function get_primary_menu() {
		$location  = $this->get_primary_location( get_nav_menu_locations() );
		$menu_item = wp_get_nav_menu_object( $location );

		return $menu_item != false ? $menu_item : null;
	}

	private function get_primary_location( $locations ) {
		$i = 0;

		$locations_keys = array_keys( $locations );

		$number_locations = sizeof( $locations_keys );
		while ( $i < $number_locations ) {
			if ( $this->is_blacklisted( $locations_keys[ $i ] ) && $this->has_items( $locations[ $locations_keys[ $i ] ] ) ) {
				return $locations[ $locations_keys[ $i ] ];
			}

			$i ++;
		}

		return null;
	}

	private function has_items( $menu ) {
		return $menu != null && sizeof( wp_get_nav_menu_items( $menu ) ) > 0;
	}

	private function is_blacklisted( $location ) {
		$j             = 0;
		$num_blacklist = sizeof( self::$blacklist );
		$primary       = true;
		while ( $j < $num_blacklist && $primary ) {
			$primary = strpos( $location, self::$blacklist[ $j ] ) === false ? true : false;
			$j ++;
		}

		return $primary;
	}

	private function create_section_definition( $menu_item ) {

		$section_definition        = new Mrf_Section_Definitions();

		$section_definition->name  = $this->encode_section_name( $this->get_slug( $menu_item ) );

		$section_definition->title = $menu_item->title;

		$feed_definition      = new Mrf_Feed_Definitions();
		$feed_definition->uri = $this->get_uri( $menu_item->url );

		$section_definition->feed_definitions = array( $feed_definition );

		$section_definition->type = $this->get_type( $menu_item->object, $menu_item->url );

		if ( $menu_item->level > 0 ) {
			$section_definition->level = $menu_item->level;
		}
		return $section_definition;
	}

	public function get_slug( $menu_item ) {
		switch ( strtoupper( $menu_item->object ) ) {
			case 'CATEGORY':
				return get_category( $menu_item->object_id )->slug;
			case 'POST_TAG':
				return get_term_by( 'name', $menu_item->title, 'post_tag' )->slug;
			case 'PAGE':
				return get_post( $menu_item->object_id )->post_name;
			case 'POST':
				return get_post( $menu_item->object_id )->post_name;
			case 'CUSTOM':
			default:
				return $menu_item->post_name;
		}
	}

	public function get_type( $object, $url ) {
		switch ( strtoupper( $object ) ) {
			case 'PAGE':
			case 'POST':
				return 'STATIC';
			case 'TAG':
			case 'POST_TAG':
			case 'CATEGORY':
				return 'DEFAULT';
			case 'CUSTOM':
			default:
				return $this->is_empty_link( $url ) ? 'GROUP' : 'EXTERNAL';
		}
	}

	public function get_uri( $url ) {
		return $this->is_empty_link( $url ) ? null : $url;
	}

	private function is_empty_link( $url ) {
		return $url == '#';
	}

	private function is_duplicated_url( $url, $section_definitions ) {
		foreach ( $section_definitions as $section_definition ) {
			if ( $section_definition->feed_definitions[0]->uri == $url ) {
				return true;
			}
		}

		return false;
	}

	private function is_valid_for_extraction( $url, $type ) {
		if ( empty( $url ) ) {
			return $type === 'GROUP';
		}

		if ( ! $this->is_empty_link( $url ) && $this->is_home_url( $url ) && $type !== 'DEFAULT' ) {
			return false;
		}

		return true;
	}

	public static function is_home_url( $uri ) {
		$path = ltrim( wp_parse_url( $uri, PHP_URL_PATH ) );
		$query = ltrim( wp_parse_url( $uri, PHP_URL_QUERY ) );

		return $query === '' && ($path === null || $path === '' || $path === '/');
	}

	public static function encode_section_name( $slug ) {
		if ( is_numeric( $slug ) ) {
			return 'mrf-slug-' . $slug;
		} elseif ( substr( $slug, 0, 1 ) === '%' ) {
			return 'mrf-slug-' . str_replace( '%', '_', $slug );
		}

		return $slug;
	}

	public static function decode_section_name( $enc_slug ) {
		if ( substr( $enc_slug, 0, 9 ) === 'mrf-slug-' ) {
			$slug = str_replace( 'mrf-slug-', '', $enc_slug );
			$slug = str_replace( '_', '%', $slug );

			return urldecode( $slug );
		}

		return $enc_slug;
	}
}
