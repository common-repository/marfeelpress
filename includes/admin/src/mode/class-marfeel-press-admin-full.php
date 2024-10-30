<?php

namespace Admin\Mode;

use Admin\Pages\Settings\Mrf_Settings;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Admin_Full extends Marfeel_Press_Admin {

	public function create_mrf_admin_submenu() {
		if ( Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.activated_once' ) ) {
			Marfeel_Press_App::make( 'page_start' )->add_page( $this->get_admin_context() );
			Marfeel_Press_App::make( 'page_settings' )->add_page( $this->get_admin_context() );
			Marfeel_Press_App::make( 'page_account' )->add_page( $this->get_admin_context() );
		} else {
			Marfeel_Press_App::make( 'page_start' )->add_page( $this->get_admin_context() );
			Marfeel_Press_App::make( 'page_settings' )->add_page( $this->get_admin_context(), false );
		}

		Marfeel_Press_App::make( 'page_signup' )->add_page( $this->get_admin_context() );
	}
}
