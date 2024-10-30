<?php

namespace Base\Descriptor\Reader;

use Base\Entities\Mrf_Section;
use Ioc\Marfeel_Press_App;

class Post_Body_Reader implements Reader {

	/** @var Mrf_Section */
	protected $section;

	public function __construct( $section ) {
		$this->section = $section;
		$this->body = file_get_contents( 'php://input' ); // @codingStandardsIgnoreLine
	}

	public function has_content() {
		return ! empty( $this->body );
	}

	public function read() {
		if ( $this->parsed === null ) {
			$this->parsed = Marfeel_Press_App::make( 'descriptor_json_parser', $this->section )->parse( $this->body );
		}

		return $this->parsed;
	}
}
