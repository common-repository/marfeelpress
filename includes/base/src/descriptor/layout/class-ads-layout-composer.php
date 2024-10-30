<?php

namespace Base\Descriptor\Layout;

class Ads_Layout_Composer extends Layout_Composer {

	/** @var int */
	protected $consumed_articles = 0;

	protected function get_context( $context ) {
		parent::get_context( $context );

		return $context;
	}

	public function get_required_articles() {
		return 0;
	}
}
