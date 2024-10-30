<?php

namespace Base\Descriptor\Layout;

use Ioc\Marfeel_Press_App;

class Article_Layout_Composer extends Layout_Composer {

	const REQUIRED_ARTICLES = 1;

	protected function get_context( $context ) {
		$context = parent::get_context( $context );

		if ( $context !== null ) {
			$context->item = $context->items[0];
			unset( $context->items );
		}

		return $context;
	}

	public function get_required_articles() {
		return self::REQUIRED_ARTICLES;
	}
}
