<?php

namespace Base\Services;

use Base\Entities\Mrf_Marfeel_Definition;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Marfeel_Definition_Service {

	/** @var Mrf_Marfeel_Definition */
	private $definition;

	public function __construct() {
		$this->definition = $this->get_marfeel_definition();
	}

	public function get( $name = '' ) {
		$definition = $this->definition;

		if ( ! empty( $name ) ) {
			$properties = explode( '.', $name );

			foreach ( $properties as $property ) {
				$definition = $definition->$property;
			}
		}

		return $definition;
	}

	private function get_marfeel_definition() {
		$marfeel_definition = new Mrf_Marfeel_Definition();

		$definition_builders = Marfeel_Press_App::make( 'definition_builders' );

		foreach ( $definition_builders as $builder ) {
			$marfeel_definition = $builder->build( $marfeel_definition );
		}

		return $marfeel_definition;
	}
}
