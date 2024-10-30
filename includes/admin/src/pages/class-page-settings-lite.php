<?php

namespace Admin\Pages;

use Ioc\Marfeel_Press_App;
use Admin\Mode\Marfeel_Press_Admin_Lite;
use Admin\Marfeel_Press_Admin_Translator;
use Admin\Pages\Settings\Mrf_Plugin_Settings;
use Admin\Pages\Settings\Mrf_Settings;

class Page_Settings_Lite extends Page {

	const PAGE_ID = 'mrf.page.settings';

	/** @var stdClass */
	protected $context;

	public function load_content( $context ) {
		$this->load_leroy_styles();
		require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-settings-tabs-template.php' );
	}

	public function add_page( $context ) {
		Marfeel_Press_App::make( 'tracker' )->identify();

		$this->handle_signup_redirect( 'mrf-settings' );

		$that = $this;
		$context->settings = array();
		$setting = new Mrf_Plugin_Settings();
		$context->page = $setting->id;
		$translated_title = Marfeel_Press_Admin_Translator::trans( 'mrf.setting.' . $setting->id );

		add_submenu_page(
			Marfeel_Press_Admin_Lite::MENU_ID,
			Mrf_Settings::PAGE_ID,
			Marfeel_Press_Admin_Translator::trans( self::PAGE_ID ),
			'manage_options',
			Mrf_Settings::PAGE_ID,
			function() use ( $context, $translated_title, $setting, $that ) {
				$setting->prepare_page( $context );

				$that->load_page( $translated_title, $context );
			}
		);
	}
}
