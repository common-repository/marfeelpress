<?php

namespace Base\Services\Metadata;

class Marfeel_Press_Metatags_Extractor implements Metadata_Extractor {

	public function extract( $parser, $tag_information ) {
		$parser->filter( 'meta' )->each( function( $meta ) use ( $tag_information ) {
			$attributes = array();

			foreach ( $meta->getNode( 0 )->attributes as $attr ) {
				$attributes[ $attr->name ] = $attr->value;
			}

			$tag_information->add_tag( 'META', null, $attributes );
		} );

		$this->add_missing_meta( $parser, $tag_information );
	}

	protected function add_missing_meta( $parser, $tag_information ) {
		if ( ! $parser->filter( 'meta[name=robots]' )->count() ) {
			$tag_information->add_tag( 'META', null, array(
				'name' => 'robots',
				'content' => 'max-snippet:-1, max-image-preview:large, max-video-preview:-1',
			) );
		}
	}
}
