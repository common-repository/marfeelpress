<?php

namespace Admin\Mode;

use Admin\Pages\Settings\Mrf_Settings;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Admin_Lite extends Marfeel_Press_Admin {

	const MENU_ID = Mrf_Settings::PAGE_ID;

	public function create_mrf_admin_submenu() {
		Marfeel_Press_App::make( 'page_settings_lite' )->add_page( $this->get_admin_context() );
		Marfeel_Press_App::make( 'page_signup' )->add_page( $this->get_admin_context() );
	}
}
