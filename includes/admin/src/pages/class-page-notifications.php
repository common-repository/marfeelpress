<?php

namespace Admin\Pages;

use Ioc\Marfeel_Press_App;
use Admin\Mode\Marfeel_Press_Admin;
use Admin\Marfeel_Press_Admin_Translator;

class Page_Notifications extends Page {
	const PAGE_ID = 'mrf.page.notifications';

	/** @var stdClass */
	protected $context;

	public function load_content( $context ) {
		$this->load_leroy_styles();
		require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-leroy-template.php' );
	}

	public function add_page( $context ) {
		$page_title = Marfeel_Press_Admin_Translator::trans( self::PAGE_ID );
		$current_setting = Marfeel_Press_App::make( 'notifications_setting' );
		$context->settings = array();
		$that = $this;

		add_submenu_page(
			Marfeel_Press_Admin::MENU_ID,
			$page_title,
			$page_title,
			'manage_options',
			'notifications',
			function() use ( $context, $page_title, $current_setting, $that ) {
				$context = $current_setting->prepare_page( $context );
				$that->load_page( $page_title, $context );
			}
		);
	}
}
