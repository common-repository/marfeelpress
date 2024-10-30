<?php

namespace Error_Handling;

use Error_Handling\Providers\Marfeel_Press_Text_File_Log_Provider;
use Ioc\Marfeel_Press_App;
use Base\Trackers\Marfeel_Press_Tracker;

class Marfeel_Press_Error_Handler {

	/** @var Marfeel_Press_Text_File_Log_Provider */
	protected $provider;
	/** @var Marfeel_Press_Tracker */
	protected $tracker;

	/** @var string */
	public static $file;

	/** @var string */
	public static $line;

	public function __construct( $activate = true ) {
		$this->provider = Marfeel_Press_App::make( 'log_provider' );
		$this->tracker = Marfeel_Press_App::make( 'tracker' );

		if ( $activate ) {
			$this->activate();
		}
	}

	public function error_handler( $errno, $errstr, $errfile, $errline = null, $tags = null, $level = null ) {
		if ( strpos( $errfile, 'marfeel-press.php' ) === false && strpos( $errstr, 'include_once' ) !== 0 ) {
			self::$file = $errfile;
			self::$line = $errline;

			if ( $errno == E_ERROR || $errno == E_USER_ERROR ) {
				$this->provider->write_log( $errstr, $level ?: 'e' );
				$this->tracker->identify();
				$this->tracker->track( 'plugin/error', array(
					'error' => $errstr,
				) );
			}
		}

		return false;
	}

	public function exception_handler( $exception ) {
		$this->provider->write_log( $exception );

		$this->throw_default_exception( $exception );
	}

	public function check_for_fatal() {
		$error = error_get_last();

		if ( $error["type"] == E_ERROR ) {
			$this->error_handler( $error["type"], $error["message"], $error["file"], $error["line"], null, 'c' );
		}
	}

	protected function throw_default_exception( $exception ) {
		restore_exception_handler();
		throw $exception;
	}

	public function deactivate() {
		restore_error_handler();
		restore_exception_handler();
	}

	protected function activate() {
		set_exception_handler( array( $this, 'exception_handler' ) );

		// @codingStandardsIgnoreLine
		set_error_handler( array( $this, 'error_handler' ) );

		register_shutdown_function( array( $this, 'check_for_fatal' ) );
	}

}
