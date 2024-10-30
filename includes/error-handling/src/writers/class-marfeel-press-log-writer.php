<?php

namespace Error_Handling\Writers;

use Ioc\Marfeel_Press_App;

abstract class Marfeel_Press_Log_Writer {

	/** @var Logger */
	protected $log;

	public function __construct() {
		$this->log = Marfeel_Press_App::make( 'logger' );
	}

	public function warning( $text ) {
		$this->log->warning( $text );
	}

	public function error( $text ) {
		$this->log->error( $text );
	}

	public function debug( $text ) {
		$this->log->debug( $text );
	}

	public function critical( $text ) {
		$this->log->critical( $text );
	}
}
