<?php

namespace Error_Handling\Providers;
use Ioc\Marfeel_Press_App;

abstract class Marfeel_Press_Log_Provider {

	/** @var array */
	protected $modes = array(
		'd' => 'debug',
		'w' => 'warning',
		'e' => 'error',
		'c' => 'critical',
	);

	public function write_log( $text, $mode = 'e' ) {
		$log_entry = array(
			'msg' => $text,
			'buildNumber' => MRFP_MARFEEL_PRESS_BUILD_NUMBER,
		);

		$this->log_writer->{$this->modes[ $mode ]}( wp_json_encode( $log_entry ) );
	}

	public function debug_if_dev( $text ) {
		if ( Marfeel_Press_App::make( 'request_utils' )->is_dev() ) {
			$this->write_log( $text, 'd' );
		}
	}
}
