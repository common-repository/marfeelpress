<?php


namespace Admin\Pages;

use Ioc\Marfeel_Press_App;
use Admin\Mode\Marfeel_Press_Admin;
use Admin\Marfeel_Press_Admin_Translator;

class Page_Start extends Page {

	/** @var stdClass */
	protected $context;

	public function load_content( $context ) {
		$this->load_leroy_styles();
		require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-leroy-template.php' );
	}

	public function add_page( $context ) {
		$this->handle_signup_redirect( 'mrf-onboarding' );

		$activated_once = Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.activated_once' );
		$page_id = $activated_once ? 'mrf.page.general' : 'mrf.page.start';
		$page_title = Marfeel_Press_Admin_Translator::trans( $page_id );
		$context->settings = Marfeel_Press_App::make( 'onboarding_settings' );
		$current_setting = Marfeel_Press_App::make( 'onboarding_setting' );
		$that = $this;

		add_submenu_page(
			Marfeel_Press_Admin::MENU_ID,
			$page_title,
			$page_title,
			'manage_options',
			Marfeel_Press_Admin::MENU_ID,
			function() use ( $context, $page_title, $current_setting, $that ) {
				$context->page = 'onboarding';
				$current_setting->prepare_page( $context );
				$that->load_page( $page_title, $context );
			}
		);
	}
}
