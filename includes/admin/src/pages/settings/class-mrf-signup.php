<?php

namespace Admin\Pages\Settings;

use Ioc\Marfeel_Press_App;
use API\Menu\Mrf_Menu_Input_Converter;

class Mrf_Signup extends Mrf_Insight_Settings {

	public function get_setting_id() {
		return 'signup';
	}

	public function prepare_page( $context ) {
		delete_transient( 'mrf_activation_redirect' );

		$context = parent::prepare_page( $context );

		if ( $this->is_autoload() ) {
			$protocol = Marfeel_Press_App::make( 'uri_utils' )->is_site_secure() ? 'https' : 'http';
			$context->host_with_protocol = $protocol . "://" . $_SERVER['HTTP_HOST'];

			$menu   = Marfeel_Press_App::make( 'default_menu_service' )->get_default_menu();
			$context->draft_definition = Mrf_Menu_Input_Converter::convert( $menu );

			require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-token-handshake.php' );
		}

		return $context;
	}

	private function is_autoload() {
		return isset( $_GET['autoload'] ) && $_GET['autoload'];
	}
}
