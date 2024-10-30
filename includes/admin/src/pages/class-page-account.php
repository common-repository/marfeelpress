<?php

namespace Admin\Pages;

use Ioc\Marfeel_Press_App;
use Admin\Mode\Marfeel_Press_Admin;
use Admin\Marfeel_Press_Admin_Translator;

class Page_Account extends Page {
	const PAGE_ID = 'mrf.page.account';

	/** @var stdClass */
	protected $context;

	public function load_content( $context ) {
		$this->load_leroy_styles();
		require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-leroy-template.php' );
	}

	public function add_page( $context ) {
		$this->handle_signup_redirect( 'businessplan' );

		$page_title = Marfeel_Press_Admin_Translator::trans( self::PAGE_ID );
		$current_setting = Marfeel_Press_App::make( 'account_setting' );
		$context->settings = array();
		$that = $this;

		add_submenu_page(
			Marfeel_Press_Admin::MENU_ID,
			$page_title,
			$page_title,
			'manage_options',
			'businessplan',
			function() use ( $context, $page_title, $current_setting, $that ) {
				$context->page = 'businessplan';
				$context = $current_setting->prepare_page( $context );
				$that->load_page( $page_title, $context );
			}
		);
	}
}
