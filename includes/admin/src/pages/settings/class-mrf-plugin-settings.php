<?php

namespace Admin\Pages\Settings;

use Base\Entities\Settings\Mrf_Availability_Modes_Enum;
use Base\Entities\Settings\Mrf_Options_Enum;
use Ioc\Marfeel_Press_App;

class Mrf_Plugin_Settings extends Mrf_Settings {

	public function __construct() {
		parent::__construct();

		$this->utils = Marfeel_Press_App::make( 'plugin_settings_utils' );
	}

	public function is_default() {
		return true;
	}

	public function get_setting_id() {
		return 'plugin';
	}

	public function prepare_page( $context ) {
		$has_warda = $this->settings_service->get( Mrf_Options_Enum::OPTION_MRF_ROUTER );
		$context->toast_confirm = $has_warda && $this->needs_confirm_cache_plugin();
		$context->toast_display = isset( $context->toast_display ) ? $context->toast_display : 'none';

		$context->insight_token = $this->settings_service->get( self::OPTION_INSIGHT_TOKEN );

		$context = Marfeel_Press_App::make( 'page_settings_service' )->load_settings( $context );

		if ( $this->needs_saving( $context ) ) {
			$this->save( $context );
		}

		$context->back = true;

		return $context;
	}

	public function save( $context ) {
		$this->prepare_page( Marfeel_Press_App::make( 'page_settings_service' )->save_settings( $context ) );
	}

	private function needs_confirm_cache_plugin() {
		return $this->utils->has_been_submitted( 'ok' )
			&& isset( $_POST['availability'] )
			&& $_POST['availability'] == Mrf_Availability_Modes_Enum::ALL
			&& Marfeel_Press_App::make( 'plugin_conflict_manager' )->has_cache_plugin_needing_device_detection_fix()
			&& ! Marfeel_Press_App::make( 'plugin_conflict_manager' )->has_cache_plugin_unsupported();
	}

	private function needs_saving( $context ) {
		return ! isset( $context->content_saved ) && $_SERVER['REQUEST_METHOD'] === 'POST';
	}
}
