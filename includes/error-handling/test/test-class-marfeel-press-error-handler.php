<?php


use Error_Handling\Marfeel_Press_Error_Handler;
use Ioc\Marfeel_Press_App;
use Ioc\WP_IOC_UnitTestCase;

class Marfeel_Press_Error_Handler_Test extends WP_IOC_UnitTestCase {

	/** @var PHPUnit_Framework_MockObject_MockBuilder */
	protected $error_handler;

	/** @var PHPUnit_Framework_MockObject_MockBuilder */
	protected $log_provider;

	/** @var PHPUnit_Framework_MockObject_MockBuilder */
	protected $tracker;

	public function setUp(){
		parent::setUp();

		$this->singleton( 'log_provider', 'supply_log_provider' );
		$this->singleton( 'tracker', 'supply_tracker' );
		$this->singleton( 'error_handler', 'supply_error_handler' );

		$this->log_provider  = Marfeel_Press_App::make( 'log_provider' );
		$this->error_handler = Marfeel_Press_App::make( 'error_handler' );
		$this->tracker = Marfeel_Press_App::make( 'tracker' );
	}

	public function tearDown(){
		parent::tearDown();
	}

	public function test_error_warning_called_writes() {
		$this->log_provider
			->expects($this->never())
			->method('write_log')
			->with(
				'test log',
				'w'
			);

		$this->error_handler->error_handler( E_USER_WARNING, 'test log', 'test.file' );
	}

	public function test_error_called_writes() {
		$this->log_provider
			->expects($this->at(0))
			->method('write_log')
			->with(
				'test log',
				'e'
			);

		$this->error_handler->error_handler( E_ERROR, 'test log', 'test.file', 12 );
	}

	public function test_error_called_track(){
		$this->tracker
			->expects( $this->once() )
			->method( 'identify' );

		$this->tracker
			->expects( $this->once() )
			->method( 'track' )
			->with(
				'plugin/error', array(
					'error' => 'test log',
				)
			);

		$this->error_handler->error_handler( E_ERROR, 'test log', 'test.file' );
	}

	public function exception_handler() {
		$this->log_provider
			->expects($this->once())
			->method('write_log')
			->with(
				'test exception log',
				'e'
			);

		$this->error_handler->exception_handler( 'test exception log' );
	}

	public function supply_error_handler() {
		return new Marfeel_Press_Error_Handler( false );
	}

	public function supply_log_provider() {
		$provider = $this->getMockBuilder( 'Error_Handling\Providers\Marfeel_Press_Text_File_Log_Provider' )
		            ->setMethods( array( 'write_log' ) )
		            ->getMock();

		return $provider;
	}

	public function supply_tracker() {
		$tracker = $this->getMockBuilder( 'Base\Trackers\Marfeel_Press_Tracker' )
			->setMethods( array( 'identify', 'track' ) )
			->getMock();

		return $tracker;
	}
}
