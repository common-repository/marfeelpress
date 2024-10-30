<?php

namespace Admin\Pages;

use Ioc\Marfeel_Press_App;
use Admin\Marfeel_Press_Admin_Translator;
use Base\Entities\Settings\Mrf_Options_Enum;

class Page_Signup extends Page {
	const PAGE_ID = 'mrf.page.start';
	const REDIRECTION_URL = 'mrf-onboarding';

	/** @var stdClass */
	protected $context;

	public function load_content( $context ) {
		$this->load_leroy_styles();
		require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-leroy-template.php' );
	}

	public function add_page( $context ) {
		$page_title = Marfeel_Press_Admin_Translator::trans( self::PAGE_ID );
		$current_setting = Marfeel_Press_App::make( 'signup_setting' );
		$context->settings = array();
		$that = $this;

		$this->handle_redirect_if_necessary();

		add_submenu_page(
			null,
			$page_title,
			$page_title,
			'manage_options',
			'mrf-signup',
			function() use ( $context, $page_title, $current_setting, $that ) {
				$context->page = 'marfeelpress/signup';
				$context = $current_setting->prepare_page( $context );
				$that->load_page( $page_title, $context );
			}
		);
	}

	private function handle_redirect_if_necessary() {
		$settings_service = Marfeel_Press_App::make( 'settings_service' );

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'mrf-signup' ) {
			if ( $this->is_secret_key_informed() ) {
				$key = $_GET['mrf-secret-key'];
				$settings_service->set( Mrf_Options_Enum::OPTION_INSIGHT_TOKEN, $key );
				$settings_service->set( Mrf_Options_Enum::OPTION_TOKEN_HANDSHAKE, true );

				if ( $this->is_tenant_type_informed() ) {
					$settings_service->set( Mrf_Options_Enum::OPTION_TENANT_TYPE, $_GET['tenantType'] );
				}

				if ( $this->is_mediagroup_informed() ) {
					$settings_service->set( Mrf_Options_Enum::OPTION_MEDIA_GROUP, $_GET['mediagroup'] );
				}

				if ( isset( $_GET['closePopup'] ) ) {
					require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'mrf-reload-window-template.php' );
				} elseif ( empty( $key ) ) {
					Marfeel_Press_App::make( 'request_utils' )->redirect( '/wp-admin/?page=mrf-signup' );
				} else {
					Marfeel_Press_App::make( 'request_utils' )->redirect( add_query_arg( array(
						'page' => self::REDIRECTION_URL,
					), admin_url( 'admin.php' ) ) );
				}

				Marfeel_Press_App::make( 'insight_service' )->track_settings( $settings_service->get( 'marfeel_press' ) );
			}
		}
	}

	private function is_secret_key_informed() {
		return isset( $_GET['mrf-secret-key'] );
	}

	private function is_mediagroup_informed() {
		return isset( $_GET['mediagroup'] );
	}

	private function is_tenant_type_informed() {
		return isset( $_GET['tenantType'] );
	}
}
