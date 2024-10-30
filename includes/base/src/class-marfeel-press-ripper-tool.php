<?php

namespace Base;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Ripper_Tool {

	public function __construct() {
		add_action( 'pre_get_posts', array( $this, 'show_queries' ) );
	}

	public function show_queries( $query ) {
		// @codingStandardsIgnoreStart
		print_r( $query->query );
		// @codingStandardsIgnoreEnd
	}
}
