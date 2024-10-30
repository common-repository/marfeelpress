<?php


use Error_Handling\Writers\Marfeel_Press_Log_Writer;
use Error_Handling\Providers\Marfeel_Press_Text_File_Log_Provider;
use Ioc\Marfeel_Press_App;
use Ioc\WP_IOC_UnitTestCase;

class Marfeel_Press_Text_File_Log_Provider_Test extends WP_IOC_UnitTestCase {

	/** @var Marfeel_Press_File_Log_Writer */
	protected $log_writer;

	/** @var Marfeel_Press_Text_File_Log_Provider */
	private $log_provider;

	public function setUp()
	{
		parent::setUp();

		$this->bind('file_reader', 'supply_file_reader');
		$this->singleton( 'log_file_writer', 'supply_log_writer' );

		$this->log_writer = Marfeel_Press_App::make( 'log_file_writer' );
		$this->log_provider = Marfeel_Press_App::make( 'text_file_log_provider' );
	}

	public function test_write_log_error() {
		$this->log_writer->expects($this->once())
		                 ->method('error')
		                 ->with(
				$this->stringContains('test error')
			);

		$this->log_provider->write_log( 'test error' );
	}

	public function test_write_log_error_explicid() {
		$this->log_writer
		->expects($this->once())
			->method('error')
			->with(
				$this->stringContains('test error')
			);

		$this->log_provider->write_log( 'test error', 'e' );
	}

	public function test_write_log_warning() {
		$this->log_writer
			->expects($this->once())
			->method('warning')
			->with(
				$this->stringContains('test error')
			);

		$this->log_provider->write_log( 'test error', 'w' );
	}

	public function supply_log_writer() {
		return $this->getMockBuilder('Mocked_Logger' )
		            ->setMethods( array( 'warning', 'error' ) )
		            ->getMock();
	}

	public function supply_file_reader() {
		return $this->getMockBuilder( 'Base\Utils\Marfeel_Press_WordPress_File_Reader' )
		            ->setMethods( array( 'get_content' ) )
		            ->getMock();
	}

}

class Mocked_Logger extends Marfeel_Press_Log_Writer {

	public function warning( $text ) {}

	public function error( $text ) {}

	public function get_log_filename() {}
}