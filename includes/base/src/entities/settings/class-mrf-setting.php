<?php

namespace Base\Entities\Settings;

abstract class Mrf_Setting {
	public function merge( $object ) {
		$props = get_object_vars( $this );

		foreach ( $props as $prop => $value ) {
			if ( ! isset( $object->$prop ) || ( ! is_bool( $object->$prop ) && empty( $object->$prop ) ) || ( is_string( $object->$prop ) && trim( $object->$prop ) === '' ) ) {
				continue;
			} elseif ( $this->$prop instanceof self ) {
				$this->$prop->merge( $object->$prop );
			} else {
				$this->$prop = $object->$prop;
			}
		}
	}
}
