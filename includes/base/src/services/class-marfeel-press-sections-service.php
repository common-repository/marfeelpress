<?php

namespace Base\Services;

use Base\Entities\Mrf_Section;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Sections_Service {

	const HOME_NAME = 'home';

	/** @var Marfeel_Press_Service */
	private $marfeel_press_service;

	/** @var array */
	protected $menu_items;

	public function __construct() {
		$this->marfeel_press_service = Marfeel_Press_App::make( 'press_service' );
	}

	public function get_by_slug( $slug ) {
		if ( $slug == self::HOME_NAME ) {
			return $this->get_home_section();
		}

		$category = get_category_by_slug( $slug ) ?: get_term_by( 'slug', $slug, 'post_tag' );

		return $this->get_default_section( $category );
	}

	public function get_tag_by_slug( $slug ) {
		return $this->get_default_section( get_term_by( 'slug', $slug, 'post_tag' ) );
	}

	public function get_current_section() {
		$term = $this->marfeel_press_service->get_term();
		$post = get_post();

		if ( $this->marfeel_press_service->is_home() && ! $term ) {
			return $this->get_home_section();
		} elseif ( $post && ( ! $term || is_page( $post ) ) ) {
			return $this->get_static_page( $post );
		} else {
			return $this->get_default_section( $term );
		}
	}

	public function get_home_section() {
		$section = new Mrf_Section();

		$section->name       = self::HOME_NAME;
		$section->menu_name  = 'home';
		$section->title      = 'home';
		$section->page_title = 'home';
		$section->type       = 'DEFAULT';
		$section->path       = '/';
		$section->parent     = null;
		$section->uri        = Marfeel_Press_App::make( 'definition_service' )->get( 'uri' );
		$section->pos        = 0;

		return $section;
	}

	public function get_default_section( $term ) {
		if ( $term === false ) {
			return null;
		}

		$section = new Mrf_Section();

		if ( $term ) {
			$section->id         = $term->category_id !== null ? $term->category_id : $term->term_id;
			$section->page_title = $term->name;
			$section->path       = wp_make_link_relative( $this->marfeel_press_service->get_term_link( $term ) );
			$section->uri        = $this->marfeel_press_service->get_term_link( $term );
			$section->styles     = 'main';
			$section->title      = $term->name;
			// decode slug as it can contain special characters. eg: japanese characters
			$section->name  = urldecode( $term->slug );
			$section->type  = $term->parent == '0' ? 'DEFAULT' : 'SUBSECTION';
			$section->state = $term->parent == '0' ? 'DEFAULT' : 'SUBSECTION';
			$section->pos   = property_exists( $term, "pos" ) ? $term->pos : 0;
			$section->term  = $term;
			if ( $term->parent !== '0' ) {
				$section->parent_id = (int) $term->parent;
			}
		}

		return $section;
	}

	public function get_static_page( $post ) {
		$section = new Mrf_Section();

		$section->id         = $post->ID;
		$section->page_title = $post->title;
		$section->path       = wp_make_link_relative( $post->url );
		$section->uri        = $post->url;
		$section->styles     = 'main';
		$section->title      = $post->title;
		// decode slug as it can contain special characters. eg: japanese characters
		$section->name = 'page_' . urldecode( $post->post_name );
		$section->menu_name = $section->name;
		$section->type = 'STATIC';
		$section->pos  = $post->pos;

		if ( $post->menu_item_parent !== '0' ) {
			$section->state     = 'SUBSECTION';
			$section->parent_id = (int) $post->menu_item_parent;
		}

		return $section;
	}
}
