<?php

namespace Base\Utils;

class Html_Utils {

	public function get_tag_attribute( $html, $tag, $attr ) {
		if ( preg_match( '/<' . $tag . ' [^>]*' . $attr . '="([^"]+)"[^>]*>/', $html, $matches ) ) {
			return $matches[1];
		}

		return null;
	}
}
