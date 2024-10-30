<?php

namespace Base\Descriptor\Layout\Decorator\Tags_Slider;

abstract class Tags_Slider_Decorator {

	/** @var array */
	protected $attributes;

	public function __construct( $attributes ) {
		$this->attributes = $attributes;
	}

	public abstract function build_context( $context );

	public abstract function get_required_articles();
}
