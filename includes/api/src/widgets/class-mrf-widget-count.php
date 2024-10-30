<?php

namespace API\Widgets;

class Mrf_Widget_Count {
	/**
	 * @var string
	 * @json name
	 */
	public $name;

	/**
	 * @var int
	 * @json count
	 */
	public $count;

	public function __construct( $name, $count ) {
		$this->name = $name;
		$this->count = $count;
	}
}
