<?php

namespace Error_Handling\Providers;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Text_File_Log_Provider extends Marfeel_Press_Log_Provider {

	/** @var Marfeel_Press_Monolog_File_Writer */
	protected $log_writer;

	/** @var string */
	protected $log_file;

	public function __construct() {
		$this->log_writer  = Marfeel_Press_App::make( 'log_file_writer' );
	}

	public function write_log( $text, $mode = 'e' ) {
		$time = date( "F jS Y, H:i", time() + 25200 );

		try {
			parent::write_log( "[#$time] $text", $mode );
		} catch ( \Exception $e ) {

		}
	}
}
