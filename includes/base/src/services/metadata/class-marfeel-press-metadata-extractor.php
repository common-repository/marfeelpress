<?php

namespace Base\Services\Metadata;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Metadata_Extractor implements Metadata_Extractor {

	public function extract( $parser, $tag_information ) {
		$extractors = Marfeel_Press_App::make( 'metadata_extractors' );

		foreach ( $extractors as $extractor ) {
			$extractor->extract( $parser, $tag_information );
		}
	}
}
