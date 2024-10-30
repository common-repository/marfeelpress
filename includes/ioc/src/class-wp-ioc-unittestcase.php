<?php

namespace Ioc;

use WP_UnitTestCase;

class WP_IOC_UnitTestCase extends WP_UnitTestCase {

	private $pristine_container;

	public function setUp() {
		parent::setUp();

		$container = Marfeel_Press_App::getContainer();
		$container->forgetInstances();

		$this->pristine_container = clone $container;
	}

	public function tearDown() {
		Marfeel_Press_App::setContainer( $this->pristine_container );

		parent::tearDown();
	}

	protected function bind( $abstract, $concrete_supplier_method, $arguments = array() ) {
		$that = $this;
		Marfeel_Press_App::bind( $abstract, function () use ( $that, $concrete_supplier_method, $arguments ) {
			return call_user_func_array( array( $that, $concrete_supplier_method ), $arguments );
		} );
	}

	protected function singleton( $abstract, $concrete_supplier_method, $arguments = array() ) {
		$that = $this;
		Marfeel_Press_App::singleton( $abstract, function () use ( $that, $concrete_supplier_method, $arguments ) {
			return call_user_func_array( array( $that, $concrete_supplier_method ), $arguments );
		} );
	}

	public function test_is_not_necessary_here() {
		$this->assertEquals( 1, 1 );
	}

}
