<?php

namespace Base\Services;

use Base\Entities\Mrf_Author;
use Base\Entities\Mrf_Item;
use Base\Entities\Mrf_Item_Hint;
use Base\Entities\Mrf_Media;
use Base\Entities\Mrf_Size;
use Base\Entities\Mrf_Tag_Information;
use Base\Marfeel_Press_Device_Detection;
use Base\Marfeel_Press_Plugin_Conflict_Manager;
use Base\Services\Modifiers\Marfeel_Press_Body_Modifier;
use Base\Utils\Http_Client;
use Ioc\Marfeel_Press_App;


class Marfeel_Press_Service {

	/** @var string */
	protected $navigation_level;

	/** @var int */
	private static $number_of_posts = 30;

	/** @var array */
	private static $default_taxonomies = array( 'category', 'post_tag', 'post_format', 'link_category' );

	/** @var Marfeel_Press_Device_Detection */
	private $mobile_detector;

	/** @var Marfeel_Press_Top_Media_Service */
	private $topmedia_service;

	/** @var Marfeel_Press_Terms_Service */
	private $terms_service;


	/** @var Marfeel_Press_Body_Modifier */
	private $body_modifier;

	public function __construct() {
		$this->mobile_detector        = Marfeel_Press_App::make( 'device_detection' );
		$this->topmedia_service       = Marfeel_Press_App::make( 'top_media_service' );
		$this->terms_service          = Marfeel_Press_App::make( 'terms_service' );
		$this->body_modifier          = Marfeel_Press_App::make( 'body_modifier' );
	}

	public function is_amp() {
		return get_query_var( 'amp' );
	}

	public function is_marfeelizable( $object ) {
		Marfeel_Press_App::make( 'log_provider' )->debug_if_dev( 'marfeelPressWatcher: current_object: ' . wp_json_encode( $object ) );

		if ( $object !== null ) {
			switch ( get_class( $object ) ) {
				case 'WP_Post':
					return Marfeel_Press_App::make( 'post_service' )->is_marfeelizable( $object );
				case 'WP_Term':
					return $this->is_marfeelizable_category( $object );
			}
		}

		return true;
	}

	public function is_marfeelizable_category( $category ) {
		$meta = get_term_meta( $category->term_id, 'no_marfeelize', true );
		return $category && ! (is_numeric( $meta ) && $meta == 1);
	}

	public function get_author( $item_hint, $post ) {
		if ( ! $item_hint->author->name ) {
			if ( is_object( $post ) ) {
				$item_hint->author->name = get_the_author_meta( 'display_name', $post->post_author );
				$item_hint->author->url = get_author_posts_url( $post->post_author );
				$item_hint->author->description = wpautop( get_the_author_meta( 'description', $post->post_author ) );
				$item_hint->author->avatar = get_avatar_url( get_the_author_meta( 'ID', $post->post_author ) );
			} else {
				$item_hint->author->name = $post;
			}
		}
	}

	public function get_item( $post = null ) {
		$fetch_details = $post === null;
		$current_section = Marfeel_Press_App::make( 'section_service' )->get_current_section();
		if ( $post === null ) {
			if ( $this->get_navigation_level() === "mosaic" && $current_section->type !== "STATIC" ) {
				return null;
			}

			$post = $this->get_queried_post();
			if ( $post === null ) {
				return null;
			}
		}

		$post_uri  = get_permalink( $post );
		$item_hint = $this->create_item();

		$item_hint->id       = $post->ID;
		$item_hint->uri      = $post_uri;
		$item_hint->is_extractable = Marfeel_Press_App::make( 'post_service' )->is_marfeelizable( $post );
		$item_hint->path     = wp_make_link_relative( get_permalink( $post ) );
		$item_hint->title    = $post->post_title;
		$item_hint->headline = $post->post_title;
		$item_hint->excerpt  = strip_tags( apply_filters( 'get_the_excerpt', $post->post_excerpt, $post ) );
		$item_hint->categories = array();

		$item_hint->date     = $this->get_date_formatted( $post->post_date );
		$item_hint->updated_at = get_the_modified_date( 'U', $post );

		do_action( 'mrf_author_name', $item_hint, $this->get_navigation_level() );
		do_action( 'mrf_item_media', $item_hint );

		$this->get_author( $item_hint, $post );

		if ( $fetch_details ) {
			$this->decorate_item_with_details( $item_hint, $post );
		}

		return $item_hint;
	}

	public function flush_url_cache() {
		Marfeel_Press_App::make( 'mrf_router' )->init_endpoint();
		Marfeel_Press_App::make( 'sw_router' )->init_endpoint();
		Marfeel_Press_App::make( 'ads_txt_router' )->init_endpoint();

		if ( Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.amp.activate' ) ) {
			Marfeel_Press_App::make( 'amp_router' )->init_endpoint();
		}

		$rewrite_rules_utils = Marfeel_Press_App::make( 'rewrite_rules_utils' );
		$rewrite_rules_utils->flush_rewrite_rules();
	}

	public function get_queried_post() {
		$post = get_post();
		if ( $post === null ) {
			$post = get_queried_object();

			if ( $post === null || get_class( $post ) != 'WP_Post' ) {
				return null;
			}
		}

		return $post;
	}

	private function is_custom_taxonomy( $taxonomy ) {
		return ! in_array( $taxonomy, self::$default_taxonomies );
	}

	private function decorate_item_with_details( Mrf_Item_Hint $item, $post ) {
		$detail_item                  = new Mrf_Item();
		$detail_item->tag_information = new Mrf_Tag_Information();
		$detail_item->detail_media    = array( new Mrf_Media() );
		$item->detail_item            = $detail_item;
		$detail_item->body            = $post->post_content;
		$detail_item->number_of_words = 0;
		$detail_item->reading_time  = 0;
		$detail_item->summary       = strip_tags( apply_filters( 'get_the_excerpt', $post->post_excerpt, $post ) );
		$detail_item->advertisement = 'ALL';
		$detail_item->id            = $post->ID;
	}

	public function fill_item_body_parsed( $item ) {
		Marfeel_Press_Plugin_Conflict_Manager::remove_unsupported_filters();

		$initial_body = Marfeel_Press_App::make( 'content_service' )->get_post_content( $item );

		$item->detail_item->body = $this->body_modifier->modify( $initial_body );
	}

	public function external_content_hooks( $content ) {
		$content = $this->get_related_links( $content );
		$content = $this->get_headline( $content );
		$content = $this->get_signature( $content );
		$content = $this->get_breadcrumbs( $content );

		return $content;
	}

	public function get_article_uri() {
		return get_permalink();
	}

	private function get_signature( $content ) {
		if ( function_exists( 'base_post_signed' ) ) {
			ob_start();
			base_post_signed();
			$content = ob_get_contents() . $content;
			ob_end_clean();
		}
		return $content;
	}

	private function get_headline( $content ) {
		$post = get_post();
		if ( $post !== null ) {
			$headline = get_post_meta( $post->ID, "headline", true );

			if ( ! empty( $headline ) ) {
				$content = '<blockquote class="headline">' . $headline . '</blockquote>' . $content;
			}
		}

		return $content;
	}

	private function get_breadcrumbs( $content ) {
		if ( function_exists( 'base_breadcrumbs' ) ) {
			ob_start();
			base_breadcrumbs();
			$content = ob_get_contents() . $content;
			ob_end_clean();
		}
		return $content;
	}

	private function get_related_links( $content ) {
		if ( function_exists( 'base_post_related_links' ) ) {
			ob_start();
			base_post_related_links();
			$content = ob_get_contents() . $content;
			ob_end_clean();
		}
		return $content;
	}

	public function get_items( $posts = false, $limit = null ) {
		if ( ! $posts ) {
			$posts = $this->get_recent_posts( null, $limit ?: self::$number_of_posts );
		}

		$items = array();
		foreach ( (array) $posts as $post ) {
			array_push( $items, $this->get_item( $post ) );
		}

		$this->topmedia_service->get_items_top_media( $items );
		$this->terms_service->add_items_terms( $items );

		return $items;
	}

	public function get_navigation_level() {
		if ( $this->navigation_level === null ) {
			$current_section = Marfeel_Press_App::make( 'section_service' )->get_current_section();
			$this->navigation_level = $this->is_home() || ! ( is_single() || is_page() ) || $current_section->type === "STATIC" ? "mosaic" : "details";
		}

		return $this->navigation_level;
	}

	private function get_date_formatted( $timestamp ) {
		return ucwords( date_i18n( get_option( 'date_format' ), strtotime( $timestamp ) ) );
	}

	private function create_item() {
		$item_hint = new Mrf_Item_Hint();
		$author    = new Mrf_Author();
		$media     = new Mrf_Media();
		$size      = new Mrf_Size();

		$media->sizes = $size;

		$item_hint->author = $author;
		$item_hint->media  = $media;

		return $item_hint;
	}

	protected function has_valid_param() {
		$search = get_query_var( 's' );
		$post = isset( $_GET['p'] ) ? $_GET['p'] : null;
		$cat = isset( $_GET['cat'] ) ? $_GET['cat'] : null;
		$page = isset( $_GET['page_id'] ) ? $_GET['page_id'] : null;

		return ! empty( $search ) || ! empty( $post ) || ! empty( $cat ) || ! empty( $page );
	}

	protected function is_home_url() {
		return str_replace( '?' . $_SERVER['QUERY_STRING'] , '', $_SERVER['REQUEST_URI'] ) == '/';
	}

	public function is_home() {
		return ( $this->is_home_url() || is_home() ) && ! $this->has_valid_param();
	}

	public function get_latest_timestamp() {
		$latest_posts = $this->get_recent_posts( null, 1 );
		return get_the_time( 'U', $latest_posts[0] );
	}

	public function get_term( $post = null ) {
		global $wp_query;

		if ( $post && $post->post_type === 'nav_menu_item' ) {
			return get_term_by( 'id', $post->object_id, $post->object );
		}

		if ( $this->is_home() ) {
			return false;
		}

		$slug = $this->get_category_slug( $wp_query );
		if ( $slug ) {
			return get_term_by( 'slug', $slug, 'category' );
		}
		$slug = $wp_query->get( 'tag', false );
		if ( $slug ) {
			return get_term_by( 'slug', $slug, 'post_tag' );
		}
		$first_post = $this->get_first_or_default( $wp_query->get_posts( array(
			'posts_per_page' => self::$number_of_posts,
		) ), 0 );

		$custom_taxonomies = $this->get_custom_taxonomy( $first_post );
		if ( has_filter( 'sanitize_custom_taxonomies' ) ) {
			$taxonomies = apply_filters( 'sanitize_custom_taxonomies', $custom_taxonomies, array( 'category', 'post_tag' ) );
		} else {
			$taxonomies = array( 'category', 'post_tag', $custom_taxonomies );
		}

		foreach ( $taxonomies as $taxonomy ) {
			$term = get_the_terms( $first_post, $taxonomy );
			if ( $term ) {
				return $term[0];
			}
		}

		return false;
	}

	private function get_custom_taxonomy( $post ) {
		foreach ( get_post_taxonomies( $post ) as $taxonomy ) {
			if ( $this->is_custom_taxonomy( $taxonomy ) ) {
				return $taxonomy;
			}
		}

		return null;
	}

	private function get_category_slug( $wp_query ) {
		$slug = $wp_query->get( 'category_name', false );

		return substr( $slug, (strrpos( $slug, '/' ) ?: -1) + 1 );
	}

	public function get_term_link( $term ) {
		$custom_taxonomy = $term->taxonomy;
		$link = get_term_link( $term, $custom_taxonomy );

		if ( ! $link ) {
			$link = get_term_link( $term, 'category' );
		}

		if ( ! $link ) {
			$link = get_term_link( $term, 'post_tag' );
		}

		$definition_uri = Marfeel_Press_App::make( 'definition_service' )->get( 'uri' );

		if ( $definition_uri != get_site_url() ) {
			$link = str_replace( get_site_url(), $definition_uri , $link );
		}

		return $link;
	}

	private function get_first_or_default( array $array, $default ) {
		if ( count( $array ) > 0 ) {
			return $array[0];
		}

		return $default;
	}

	public function fetch_section_items( $section, $number_of_items = false, $filter = array() ) {
		$posts = $this->get_recent_posts_section( $section, $number_of_items, $filter );
		$items = $this->get_items( $posts );
		return $items;
	}

	private function get_recent_posts_section( $section, $number_of_items, $filter ) {
		$related_posts = $this->get_recent_posts( $section->term, $number_of_items, $filter );

		if ( is_Array( $related_posts ) ) {
			return $related_posts;
		}

		return array();
	}

	private function get_recent_posts( $term = null, $count = false, $filter = array() ) {
		if ( $term && isset( $term->term_id ) ) {
			$term_taxonomy = $term->taxonomy;

			$filter['tax_query'] = array( // @codingStandardsIgnoreLine
				array(
					'taxonomy' => ( $term_taxonomy == 'tag' ? 'post_tag' : $term_taxonomy ),
					'terms' => $term->slug,
					'field' => 'slug',
					'include_children' => true,
				),
			);
		}

		$filter['post_status'] = 'publish';
		$filter['post_type'] = Marfeel_Press_App::make( 'definition_service' )->get( 'post_type' );
		$filter['numberposts'] = $count ?: self::$number_of_posts;

		return Marfeel_Press_App::make( 'posts_repository' )->get_latest_posts( $filter );
	}
}
