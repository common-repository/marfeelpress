<?php

namespace Admin;

use Base\Entities\Mrf_Model;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Admin_Ajax {

	public function handle() {
		$action = $_GET['method'];

		$this->$action();
	}

	public function track() {
		Marfeel_Press_App::make( 'tracker' )->track( $_GET['key'] );
	}
}
