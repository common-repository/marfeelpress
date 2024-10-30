<?php

namespace Base\Repositories;

use Ioc\Marfeel_Press_App;

class Repository {

	public function __construct() {
		$this->db = Marfeel_Press_App::make( 'database_wrapper' );
	}
}
