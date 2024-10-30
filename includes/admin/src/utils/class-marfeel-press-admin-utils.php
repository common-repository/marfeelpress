<?php

namespace Admin\Utils;

class Marfeel_Press_Admin_Utils {
	public function get_unique_object_array( $array, $keep_key_assoc = false ) {
		$duplicate_keys = array();
		$tmp = array();

		foreach ( $array as $key => $val ) {
			if ( is_object( $val ) ) {
				$val = (array) $val;
			}

			if ( ! in_array( $val, $tmp ) ) {
				$tmp[] = $val;
			} else {
				$duplicate_keys[] = $key;
			}
		}

		foreach ( $duplicate_keys as $key ) {
			unset( $array[ $key ] );
		}

		return $keep_key_assoc ? $array : array_values( $array );
	}
}
