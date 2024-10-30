<?php

namespace Base\Services\Modifiers;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Body_Modifier {
	/** @var Modifier */
	private $modifiers;

	public function __construct() {
		$this->modifiers = array( Marfeel_Press_App::make( 'toc_modifier' ) );
	}

	public function modify( $body ) {
		foreach ( $this->modifiers as $modifier ) {
			if ( $modifier->should_process( $body ) ) {
				$body = $modifier->process( $body );
			}
		}

		return $body;
	}
}
