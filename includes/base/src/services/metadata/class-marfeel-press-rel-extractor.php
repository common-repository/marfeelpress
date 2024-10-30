<?php

namespace Base\Services\Metadata;

class Marfeel_Press_Rel_Extractor implements Metadata_Extractor {

	public function extract( $parser, $tag_information ) {
		$parser->filter( 'link[rel="next"],link[rel="prev"]' )->each( function( $link ) use ( $tag_information ) {
			$attributes = array();

			foreach ( $link->getNode( 0 )->attributes as $attr ) {
				$attributes[ $attr->name ] = $attr->value;
			}

			$tag_information->add_tag( 'LINK', null, $attributes );
		} );
	}
}
