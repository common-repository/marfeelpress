<?php

namespace Error_Handling\Writers;

use Ioc\Marfeel_Press_App;
use Marfeel\Monolog\Handler\RotatingFileHandler;

class Marfeel_Press_Monolog_File_Writer extends Marfeel_Press_Log_Writer {

	/** @var string */
	protected $log_file;

	const DATE_FORMAT = 'Y-m-d';

	const MAX_FILES = 1;

	public function __construct() {
		parent::__construct();

		$this->log_file = Marfeel_Press_App::make( 'log_file_path' );
		$log_level      = Marfeel_Press_App::make( 'log_level' );

		$stream = new RotatingFileHandler( $this->log_file . '.log', self::MAX_FILES, $log_level );
		$this->log->pushHandler( $stream );
	}

	public function get_log_filename() {
		return $this->log_file . '-' . date( self::DATE_FORMAT ) . '.log';
	}
}
