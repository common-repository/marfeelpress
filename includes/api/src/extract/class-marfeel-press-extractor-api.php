<?php

namespace API\Extract;

use API\Mrf_API;
use WP_REST_Response;
use Ioc\Marfeel_Press_App;
use API\Marfeel_REST_API;


class Marfeel_Press_Extractor_API extends Marfeel_Press_Gutenberg_Content_API {

	public function __construct() {
		parent::__construct();
		$this->resource_name = 'extractor';
	}

	protected function get_extractor() {
		return Marfeel_Press_App::make( 'api_extractor_wp_post' );
	}
}
