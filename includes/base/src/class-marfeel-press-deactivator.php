<?php

namespace Base;

use Base\Entities\Insight\Events\Plugin_Deactivation_Event;
use Ioc\Marfeel_Press_App;
use Base\Entities\Settings\Mrf_Options_Enum;

class Marfeel_Press_Deactivator {
	const OPTION_PLUGIN_STATUS = 'marfeel_press.plugin_status';
	const REASON_MESSAGE_SEPARATOR = '|Comments:';

	public function deactivate() {
		do_action( 'mrf_plugin_deactivated' );

		$rewrite_rules_utils = Marfeel_Press_App::make( 'rewrite_rules_utils' );
		$rewrite_rules_utils->flush_rewrite_rules();
		$this->track_plugin_deactivation();
	}

	public function track_plugin_deactivation( $set_plugin_deactivated = true ) {
		$tracker          = Marfeel_Press_App::make( 'tracker' );
		$settings_service = Marfeel_Press_App::make( 'settings_service' );

		if ( $set_plugin_deactivated ) {
			$settings_service->set( self::OPTION_PLUGIN_STATUS, 'DEACTIVATED' );
		}

		Marfeel_Press_App::make( 'insight_service' )->track_settings( $settings_service->get( 'marfeel_press' ) );

		$tracker->identify( true );

		$reason_parts = $this->get_reason_parts();
		$reason = $this->get_reason( $reason_parts );
		$reason_msg = $this->get_reason_message( $reason_parts );

		$tracker->track( new Plugin_Deactivation_Event( $reason, $reason_msg ) );
	}

	public function add_deactivation_popup() {
		global $pagenow;
		if ( $pagenow === 'plugins.php' ) {
			add_action( 'admin_footer', array( $this, 'deactivation_popup' ) );
			$this->load_deactivation_popup_styles();
		}
	}

	public function deactivation_popup() {
		include MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . '../../templates/mrf-deactivation-popup-template.php';
	}

	public function enqueue_deactivation_popup_styles() {
		wp_register_style(
			'deactivation_popup',
			MRFP__MARFEEL_PRESS_ADMIN_RESOURCES_DIR . 'dist/main-deactivation-popup.css?buildnumber=' . MRFP_PLUGIN_VERSION
		);
		wp_enqueue_style( 'deactivation_popup' );
	}

	public function load_deactivation_popup_styles() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_deactivation_popup_styles' ) );
	}

	private function get_reason_parts() {
		return isset( $_GET['message'] ) ? explode( self::REASON_MESSAGE_SEPARATOR, $_GET['message'] ) : null;
	}

	private function get_reason( $reason_parts ) {
		return isset( $reason_parts ) ? $reason_parts[0] : null;
	}

	private function get_reason_message( $reason_parts ) {
		return is_array( $reason_parts ) && count( $reason_parts ) > 1 ? $reason_parts[1] : null;
	}
}
