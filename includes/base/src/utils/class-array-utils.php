<?php

namespace Base\Utils;

class Array_Utils {

	public function contains_only_nulls( $array = array() ) {
		$values_not_null = array_filter( $array, function ( $a ) {
			return $a !== null;
		} );

		return empty( $values_not_null );
	}

}
