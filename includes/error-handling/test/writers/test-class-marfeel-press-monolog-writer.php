test-class-marfeel-press-error-handler.php

<?php


use Error_Handling\Providers\Marfeel_Press_Monolog_File_Writer;
use Ioc\Marfeel_Press_App;
use Ioc\WP_IOC_UnitTestCase;

class Marfeel_Press_Monolog_Writer_Test extends WP_IOC_UnitTestCase {

	/** @var PHPUnit_Framework_MockObject_MockBuilder */
	protected $mocked_log;

	/** @var Marfeel_Press_Monolog_File_Writer */
	protected $monolog_writer;

	public function setUp()
	{
		parent::setUp();

		$this->bind( 'log_file_path', 'supply_log_file_path' );
		$this->singleton( 'logger', 'supply_logger' );

		$this->mocked_log = Marfeel_Press_App::make( 'logger' );
		$this->monolog_writer = Marfeel_Press_App::make( 'log_file_writer' );
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	public function test_write_log_error() {
		$this->mocked_log->expects($this->once())
			->method('error')
			->with(
				$this->stringContains('test error')
			);

		$this->monolog_writer->error( 'test error' );
	}

	public function test_write_log_warning() {
		$this->mocked_log
		->expects($this->once())
			->method('warning')
			->with(
				$this->stringContains('test error')
			);

		$this->monolog_writer->warning( 'test error' );
	}

	public function supply_logger() {
		return $this->getMockBuilder( 'Monolog_Mocked_Logger' )
		            ->setMethods( array( 'warning', 'error' ) )
		            ->getMock();
	}

	public function supply_log_file_path() {
		return 'test.file';
	}

}

class Monolog_Mocked_Logger {

	public function warning() {}

	public function error() {}

	public function pushHandler() {}
}
