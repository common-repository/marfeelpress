<?php

namespace Base\Routers;

use Base\Routers\Marfeel_Press_Base_Router;
use Base\Utils\Marfeel_Press_WP_Priority;

class Marfeel_Press_Rewrite_Router extends Marfeel_Press_Base_Router {

	public function route() {}
	public function get_query_var_value() {}
	public function get_path_value() {}
	public function get_query_var() {}

	public function accepted_type() {
			return true;
	}

	public function valid_route( $post ) {
		$ads_txt_query = get_query_var( $this->get_query_var_value() , false );

		return $ads_txt_query == 1;
	}

	public function init_endpoint() {
		$path_rewrite = 'index.php?' . $this->get_query_var_value() . '=1';
		add_rewrite_rule( $this->get_path_value(), $path_rewrite, 'top' );
	}

	public function init_custom_query_params( $vars ) {
		$vars[] = $this->get_query_var_value();

		return $vars;
	}

	public function get_priority() {
		return Marfeel_Press_WP_Priority::HIGH;
	}
}
