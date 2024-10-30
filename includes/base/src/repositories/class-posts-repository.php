<?php

namespace Base\Repositories;

use DateTime;
use Ioc\Marfeel_Press_App;

class Posts_Repository extends Repository {

	protected function get_sticky_posts_if_enabled() {
		if ( Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.sticky_posts_on_top' ) ) {
			return get_option( 'sticky_posts' );
		}

		return null;
	}

	protected function sort( $posts ) {
		$sticky_posts = $this->get_sticky_posts_if_enabled();

		if ( ! empty( $sticky_posts ) ) {
			usort( $posts, function( $post_a, $post_b ) use ( $sticky_posts ) {
				$date_a = new DateTime( $post_a->post_date );
				$date_b = new DateTime( $post_b->post_date );
				$post_a_is_sticky = in_array( $post_a->ID, $sticky_posts );
				$post_b_is_sticky = in_array( $post_b->ID, $sticky_posts );

				if ( ( $post_b_is_sticky && ! $post_a_is_sticky ) || ( $date_b > $date_a && ( ! $post_a_is_sticky || ( $post_a_is_sticky && $post_b_is_sticky ) ) ) ) {
					return 1;
				}

				if ( ( $post_a_is_sticky && ! $post_b_is_sticky ) || ( $date_a > $date_b && ( ! $post_b_is_sticky || ( $post_b_is_sticky && $post_a_is_sticky ) ) ) ) {
					return -1;
				}

				return 0;
			} );
		}

		return $posts;
	}

	protected function add_sticky( $posts, $loader ) {
		$sticky_posts = $this->get_sticky_posts_if_enabled();

		if ( ! empty( $sticky_posts ) ) {
			$ids = array_map( function( $post ) {
				return $post->ID;
			}, $posts );

			$sticky_posts = array_diff( $sticky_posts, $ids );
			if ( ! empty( $sticky_posts ) ) {
				$sticky_posts = $loader( $sticky_posts );

				if ( $sticky_posts !== false && ! empty( $sticky_posts ) ) {
					$posts = array_merge( $sticky_posts, $posts );
				}
			}
		}

		return $posts;
	}

	protected function get_archive_posts( $filter ) {
		global $wp_query;

		$wp_query->set( 'posts_per_page', $filter['numberposts'] );
		$wp_query->set( 'post_type', Marfeel_Press_App::make( 'definition_service' )->get( 'post_type' ) );
		$wp_query->set( 'post_status', 'publish' );

		if ( ! empty( $filter['exclude'] ) ) {
			$wp_query->set( 'post__not_in', explode( ',', $filter['exclude'] ) );
		}

		$posts = $wp_query->get_posts();

		return $this->add_sticky( $posts, function( $sticky_posts ) use ( $wp_query ) {
			$wp_query->set( 'post__in', $sticky_posts );
			$sticky_posts = $wp_query->get_posts();
			$wp_query->set( 'post__in', false );

			return $sticky_posts;
		} );
	}

	protected function get_section_posts( $filter ) {
		$filter['suppress_filters'] = 0;
		$posts = wp_get_recent_posts( $filter, OBJECT );

		return $this->add_sticky( $posts, function( $sticky_posts ) use ( $filter ) {
			$filter['include'] = $sticky_posts;
			$filter['post_type'] = Marfeel_Press_App::make( 'definition_service' )->get( 'post_type' );

			return wp_get_recent_posts( $filter, OBJECT );
		} );
	}

	public function get_latest_posts( $filter ) {
		if ( isset( $filter['tax_query'] ) || is_front_page() ) {
			$posts = $this->get_section_posts( $filter );
		} else {
			$posts = $this->get_archive_posts( $filter );
		}

		$posts = $this->sort( $posts );
		$posts = array_slice( $posts, 0, $filter['numberposts'] );

		return $posts;
	}

	public function count_published_posts() {
		$wp_posts = $this->db->get_posts_table_name();

		$row = $this->db->get_results( "
			SELECT COUNT(*) as published_posts
			FROM $wp_posts
			WHERE post_type = 'post'
				AND post_status = 'publish'
		" );

		return $row[0]->published_posts;
	}

	public function count_posts_by_category() {
		$wp_posts = $this->db->get_posts_table_name();
		$wp_terms = $this->db->get_terms_table_name();
		$wp_term_taxonomy = $this->db->get_term_taxonomy_table_name();
		$wp_term_relationships = $this->db->get_term_relationships_table_name();

		$rows = $this->db->get_results( "
			SELECT COUNT(*) as numPosts, t.name
			FROM $wp_posts p
				INNER JOIN $wp_term_relationships tr ON tr.object_id = p.ID
				INNER JOIN $wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
				INNER JOIN $wp_terms t ON t.term_id = tt.term_id
			WHERE p.post_type = 'post'
				AND p.post_status = 'publish'
				AND tt.taxonomy = 'category'
			GROUP BY t.term_id
			ORDER BY numPosts DESC
			LIMIT 10
		" );

		return $rows;
	}

	public function count_posts_by_month() {
		$wp_posts = $this->db->get_posts_table_name();

		$rows = $this->db->get_results( "
			SELECT COUNT(*) as numPosts, CONCAT(YEAR(p.post_date), '-', MONTH(p.post_date)) AS name
			FROM $wp_posts p
			WHERE p.post_type = 'post'
				AND p.post_status = 'publish'
			GROUP BY name
			ORDER BY p.post_date DESC
			LIMIT 10
		" );

		return $rows;
	}

	public function count_posts_by_author() {
		$wp_posts = $this->db->get_posts_table_name();
		$wp_users = $this->db->get_users_table_name();

		$rows = $this->db->get_results( "
			SELECT COUNT(*) as numPosts, u.display_name name
			FROM $wp_posts p
				INNER JOIN $wp_users u ON u.ID = p.post_author
			WHERE p.post_type = 'post'
				AND p.post_status = 'publish'
			GROUP BY u.ID
			ORDER BY numPosts DESC
			LIMIT 10
		" );

		return $rows;
	}

	public function get_top_media( $ids ) {
		$wp_posts = $this->db->get_posts_table_name();
		$wp_postmeta = $this->db->get_postmeta_table_name();

		return $this->db->get_results( "
			SELECT p.ID, p.guid, pmi.meta_value, pm.post_id, p.post_excerpt
			FROM $wp_posts p
				INNER JOIN $wp_postmeta pm ON pm.meta_value = p.ID
					AND pm.meta_key = '_thumbnail_id'
					AND pm.post_id IN (" . implode( ', ', $ids ) . ")
					AND pm.post_id NOT IN (SELECT post_id FROM $wp_postmeta WHERE post_id IN (" . implode( ', ', $ids ) . ") AND meta_key = 'mrf_top_media' AND meta_value = 0)
				LEFT JOIN $wp_postmeta pmi ON pmi.post_id = p.ID
					AND pmi.meta_key = '_wp_attachment_image_alt'
		" );
	}
}
