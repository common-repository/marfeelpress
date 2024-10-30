<?php

namespace Base\Services;

use Base\Entities\Mrf_Marfeel_Definition;
use Ioc\Marfeel_Press_App;
use Base\Entities\Settings\Mrf_Availability_Modes_Enum;

class Marfeel_Press_Settings_Service {

	/** @var string */
	const OPTION_NAME = 'mrf_definition';

	/** @var string */
	protected $options = array( 'mrf_definition', 'mrf_availability', 'mrf_layouts.s.css', 'mrf_custom.s.css', 'mrf_default.json', 'mrf_home.default.json', 'mrf_inventory' );

	/** @var Mrf_Marfeel_Definition */
	private $definition;

	/** @var string */
	private $availability_mode;

	/** @var array */
	private $overwritten_settings = array();

	public function __construct() {
		$this->definition = Marfeel_Press_App::make( 'definition_entity' );
		$this->definition->merge( $this->get_option_data() );
	}

	protected function set_value( $name, $value ) {
		$path_components = explode( '.', $name );
		$property = array_pop( $path_components );
		$parent_setting = $this->get( join( '.', $path_components ) );

		if ( is_object( $parent_setting->$property ) && isset( $parent_setting->$property->merge ) ) {
			$parent_setting->$property->merge( $value );
		} else {
			$parent_setting->$property = $value;
		}
	}

	public function overwrite( $setting, $value ) {
		$this->overwritten_settings[ $setting ] = $value;
	}

	public function get_availability() {
		if ( $this->availability_mode === null ) {
			$this->availability_mode = $this->get_option_data( 'mrf_availability', null );

			if ( $this->availability_mode === null ) {
				$this->availability_mode = $this->get( 'marfeel_press.availability' ) ?: Mrf_Availability_Modes_Enum::OFF;
			}
		}

		return $this->availability_mode;
	}

	public function remove_all() {
		foreach ( $this->options as $name ) {
			delete_option( $name );
		}

		Marfeel_Press_App::make( 'requirements_checker' )->force_disable_plugin();
	}

	public function get_option_data( $option_name = self::OPTION_NAME, $default = array() ) {
		return get_option( $option_name, $default );
	}

	public function set_option_data( $option_name, $value ) {
		update_option( $option_name, $value );
	}

	public function get( $name = '' ) {
		if ( isset( $this->overwritten_settings[ $name ] ) ) {
			return $this->overwritten_settings[ $name ];
		}

		$definition = $this->definition;

		if ( ! empty( $name ) ) {
			$properties = explode( '.', $name );

			foreach ( $properties as $property ) {
				$definition = $definition->$property;
			}
		}

		return $definition;
	}

	public function save() {
		$this->set_option_data( self::OPTION_NAME, $this->definition );
	}

	public function set( $name, $value = null ) {
		if ( is_array( $name ) ) {
			foreach ( $name as $key => $value ) {
				$this->set_value( $key, $value );
			}
		} else {
			$this->set_value( $name, $value );
		}

		$this->save();
	}
}
