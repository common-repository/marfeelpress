<?php


namespace Base\Utils;

use Ioc\Marfeel_Press_App;

class Json_Serializer {

	public function __construct() {
		$this->reflection_utils = Marfeel_Press_App::make( 'reflection_utils' );
	}

	protected function is_plain( $var ) {
		return in_array( $var, array(
			'int',
			'integer',
			'float',
			'bool',
			'boolean',
			'string',
			'array',
			'[]',
			null,
		) );
	}

	protected function serialize_value( $value ) {
		if ( is_object( $value ) ) {
			return $this->serialize( $value );
		} elseif ( is_array( $value ) ) {
			foreach ( $value as $k => $val ) {
				$value[ $k ] = $this->serialize_value( $val );
			}
		}

		return $value;
	}

	protected function unserialize_value( $value, $type = null ) {
		if ( ! $this->is_plain( $type ) && ! empty( $value ) ) {
			if ( strpos( $type, '[]' ) !== false ) {
				$type = str_replace( '[]', '', $type );
				foreach ( $value as $key => $val ) {
					$value[ $key ] = $this->unserialize( $val, $type );
				}

				return $value;
			}

			return $this->unserialize( $value, str_replace( '[]', '', $type ) );
		}

		return $value;
	}

	public function serialize( $object ) {
		$properties = get_object_vars( $object );
		$serialized = array();

		foreach ( $properties as $prop => $value ) {
			$php_doc = $this->reflection_utils->get_doc_values( get_class( $object ), $prop );

			if ( ! isset( $php_doc['jsonRemove'] ) && ( ! isset( $php_doc['jsonRemoveEmpty'] ) || ! empty( $value ) ) ) {
				$prop_name = $prop;
				if ( isset( $php_doc['json'] ) ) {
					$prop_name = $php_doc['json'];
				}

				$value = $this->serialize_value( $value );

				if ( ! isset( $php_doc['jsonRemoveEmpty'] ) || ! empty( $value ) ) {
					$serialized[ $prop_name ] = $this->serialize_value( $value );
				}
			}
		}

		return $serialized;
	}

	public function unserialize( $json, $target_class ) {
		$obj = new $target_class();

		if ( is_string( $json ) ) {
			$json = json_decode( $json, true );
		}

		if ( $json ) {
			foreach ( $json as $prop => $value ) {
				$prop_name = $prop;
				if ( ! property_exists( $obj, $prop_name ) ) {
					foreach ( get_object_vars( $obj ) as $pname => $val ) {
						$php_doc = $this->reflection_utils->get_doc_values( $target_class , $pname );

						if ( isset( $php_doc['json'] ) && $php_doc['json'] == $prop ) {
							$prop_name = $pname;
							break;
						}
					}
				} else {
					$php_doc = $this->reflection_utils->get_doc_values( $target_class, $prop );
				}

				$php_doc['var'] = isset( $php_doc['var'] ) ? $php_doc['var'] : null;

				$obj->$prop_name = $this->unserialize_value( $value, $php_doc['var'] );
			}
		}

		return $obj;
	}
}
