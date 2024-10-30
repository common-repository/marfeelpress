<?php

namespace Admin\Pages;

use Admin\Marfeel_Press_Admin_Translator;
use Admin\Mode\Marfeel_Press_Admin;
use Admin\Pages\Settings\Mrf_Settings;
use Ioc\Marfeel_Press_App;

class Page_Settings extends Page {

	const PAGE_ID = 'mrf.page.settings';

	/** @var stdClass */
	protected $context;

	public function load_content( $context ) {
		$this->load_leroy_styles();
		require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-settings-tabs-template.php' );
	}

	public function add_page( $context, $add_to_sidebar = true ) {
		Marfeel_Press_App::make( 'tracker' )->identify();
		$context->settings = Marfeel_Press_App::make( 'admin_settings' );

		$this->handle_signup_redirect( 'mrf-settings' );

		$that = $this;

		add_submenu_page(
			$add_to_sidebar ? Marfeel_Press_Admin::MENU_ID : null,
			Mrf_Settings::PAGE_ID,
			Marfeel_Press_Admin_Translator::trans( self::PAGE_ID ),
			'manage_options',
			Mrf_Settings::PAGE_ID,
			function() use ( $context, $that ) {
				$context->page = isset( $_GET['action'] ) ? $_GET['action'] : 'advanced';

				foreach ( $context->settings as $setting ) {
					if ( $setting->id == $context->page ) {
						break;
					}
				}

				$setting->prepare_page( $context );

				$that->load_page( Marfeel_Press_Admin_Translator::trans( 'mrf.setting.' . $setting->id ), $context );
			}
		);
	}
}
