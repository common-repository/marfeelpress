<?php

namespace Base\Services;

use Ioc\Marfeel_Press_App;
use Base\Entities\Mrf_Tag;
use Base\Entities\Mrf_Category;

class Marfeel_Press_Terms_Service {

	private function category_with_parent( $category ) {
		$id = $category->term_id;

		if ( $id == 0 ) {
			return null;
		}

		$result = new Mrf_Category();
		$result->id = $id;
		$result->name = $category->name;
		$result->content = get_category_link( $category );
		$result->parent = $this->category_with_parent( get_category( $category->parent ) );

		return $result;
	}

	public function add_items_terms( $items ) {
		$ids = array_map( function( $item ) {
			return $item->id;
		}, $items );

		$array_utils = Marfeel_Press_App::make( 'array_utils' );

		if ( sizeof( $ids ) === 0 || $array_utils->contains_only_nulls( $ids ) ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'add_items_terms called with empty or null $items' ,'w' );

			return;
		}

		$results = Marfeel_Press_App::make( 'terms_repository' )->get_terms_by_post_ids( $ids );

		$terms = array();
		foreach ( $results as $result ) {
			$terms[ $result->object_id ][ $result->taxonomy ][] = $result;
		}

		foreach ( $items as $item ) {
			if ( isset( $terms[ $item->id ] ) ) {
				if ( isset( $terms[ $item->id ]['category'] ) ) {
					$item->pocket['categories'] = array();

					foreach ( $terms[ $item->id ]['category'] as $category ) {
						$item->pocket['categories'][] = $category->name;
						$item->categories[] = $this->category_with_parent( $category );
					}
				}

				$mrf_tags = array();

				if ( isset( $terms[ $item->id ]['post_tag'] ) ) {
					foreach ( $terms[ $item->id ]['post_tag'] as $tag ) {
						$mrf_tag = new Mrf_Tag();

						$mrf_tag->name = $tag->name;
						$mrf_tag->content = get_term_link( $tag, 'post_tag' );

						$mrf_tags[] = $mrf_tag;
					}
				}

				$item->detail_item->pocket['tags'] = $mrf_tags;
			}
		}
	}
}
