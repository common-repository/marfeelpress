<?php

namespace Base\Utils;

use ReflectionMethod;
use ReflectionProperty;

class Reflection_Utils {

	public function get_doc_values( $class, $prop ) {
		if ( method_exists( $class, $prop ) || property_exists( $class, $prop ) ) {
			$reflection = method_exists( $class, $prop ) ? new ReflectionMethod( $class, $prop ) : new ReflectionProperty( $class, $prop );

			preg_match_all( '/@(?<name>[a-zA-Z_0-9]+)(\s+(?<value>[a-zA-Z_0-9\[\]\\\]+))?/', $reflection->getDocComment(), $matches, PREG_SET_ORDER );

			$values = array();
			foreach ( $matches as $match ) {
				if ( isset( $values[ $match['name'] ] ) ) {
					$values[ $match['name'] ] = (array) $values[ $match['name'] ];
					$values[ $match['name'] ][] = $match['value'];
				} elseif ( ! array_key_exists( 'value', $match ) || $match['value'] == null ) {
					$values[ $match['name'] ] = 1;
				} else {
					$values[ $match['name'] ] = $match['value'];
				}
			}

			return $values;
		}

		return array();
	}
}
