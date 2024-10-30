<?php

namespace API\Test;

class Mrf_Api_Status {
	/**
	 * @var string
	 * @json status
	 */
	public $status;

	public function __construct( $status ) {
		$this->status = $status;
	}
}
