<?php

namespace Base\Services\Metadata;

class Marfeel_Press_Title_Extractor implements Metadata_Extractor {

	public function extract( $parser, $tag_information ) {
		if ( $parser->filter( 'title' )->count() ) {
			$title = $parser->filter( 'title' )->text();

			if ( $title === get_option( 'blogname' ) ) {
				$title = get_option( 'blogname' ) . ' - ' . get_option( 'blogdescription' );
			}

			$tag_information->add_tag( 'TITLE', $title );
			$tag_information->html = $this->remove_title( $tag_information->html );
		} else {
			$tag_information->add_tag( 'TITLE', wp_title( '-', false ) );
		}
	}

	protected function remove_title( $head ) {
		return preg_replace( '/<title>.*?<\/title>/i', '', $head );
	}
}
