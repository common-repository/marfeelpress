<?php

namespace Base\Services;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Log_Service {

	/** @var string */
	private $file;

	public function __construct() {
		$this->file = Marfeel_Press_App::make( 'log_file_writer' )->get_log_filename();
	}

	public function get_content() {
		return Marfeel_Press_App::make( 'filesystem_wrapper' )->get_contents( $this->file );
	}

	public function clean() {
		Marfeel_Press_App::make( 'filesystem_wrapper' )->put_contents( $this->file, '' );
	}
}
