<?php

namespace Base\Services;

use Base\Entities\Mrf_Media;
use Base\Utils\Image_Utils;
use Base\Utils\Array_Utils;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Top_Media_Service {

	const MIN_IMG_WIDTH = 450;
	const RATIO_RANGE = 0.1;

	/** @var Image_Utils */
	private $image_utils;

	/** @var Array_Utils */
	private $array_utils;

	public function __construct() {
		$this->image_utils = Marfeel_Press_App::make( 'image_utils' );
		$this->array_utils = Marfeel_Press_App::make( 'array_utils' );
	}

	protected function is_similar_ratio( $ratio, $size ) {
		$size_ratio = $size['width'] / $size['height'];

		return $size_ratio >= $ratio - self::RATIO_RANGE && $size_ratio <= $ratio + self::RATIO_RANGE;
	}

	protected function get_media_fallback( $ids ) {
		$html_utils = Marfeel_Press_App::make( 'html_utils' );
		$uri_utils = Marfeel_Press_App::make( 'uri_utils' );
		$images = array();

		foreach ( $ids as $id ) {
			$content = get_the_content( null, false, $id );
			$src = $html_utils->get_tag_attribute( $content, 'img', 'src' );

			if ( $src ) {
				$media = new Mrf_Media();
				$media->alt = $html_utils->get_tag_attribute( $content, 'img', 'alt' );
				$media->caption = $media->alt;
				$media->width = $html_utils->get_tag_attribute( $content, 'img', 'width' ) ?: 0;
				$media->height = $html_utils->get_tag_attribute( $content, 'img', 'height' ) ?: 0;
				$media->src = $uri_utils->get_image_url( $src );

				$images[ $id ] = $media;
			}
		}

		return $images;
	}

	protected function get_media( $ids ) {
		$uri_utils = Marfeel_Press_App::make( 'uri_utils' );
		$results = Marfeel_Press_App::make( 'posts_repository' )->get_top_media( $ids );

		$images = array();
		foreach ( $results as $result ) {
			$media = new Mrf_Media();
			$media->alt = $result->meta_value ?: $result->post_excerpt;
			$media->caption = $result->post_excerpt ?: $result->meta_value;

			if ( $media->caption === null ) {
				$media->caption = "";
			}

			$image = wp_get_attachment_image_src( $result->ID, 'full', false );

			list( $media->src, $media->width, $media->height ) = $image;

			$media->width = $media->width ?: 0;
			$media->height = $media->height ?: 0;

			$media->src = $uri_utils->get_image_url( $media->src );

			$images[ $result->post_id ] = $media;
		}

		$images = $images + $this->get_media_fallback( array_diff( $ids, array_keys( $images ) ), $images );

		return $images;
	}

	public function get_items_top_media( $items ) {
		$ids = array();

		foreach ( $items as $item ) {
			if ( $item->media->src === null ) {
				$ids[] = $item->id;
			}
		}

		if ( sizeof( $ids ) === 0 || $this->array_utils->contains_only_nulls( $ids ) ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'get_items_top_media called with empty or null $items' ,'w' );

			return;
		}

		$images = $this->get_media( $ids );

		foreach ( $items as $item ) {
			if ( isset( $images[ $item->id ] ) ) {
				$item->media = $images[ $item->id ];
			} else {
				$item->media = null;
			}

			if ( $item->media ) {
				$item->media->image_ratio = $this->image_utils->calculate_image_ratio( $item->media->width, $item->media->height );
			}
		}
	}
}
