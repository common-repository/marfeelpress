<?php

namespace Base\Utils;

class Rewrite_Rules_Utils {

	public function flush_rewrite_rules() {
		flush_rewrite_rules();
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		delete_option( 'rewrite_rules' );
	}

}
