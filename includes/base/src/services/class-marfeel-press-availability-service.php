<?php

namespace Base\Services;

use Base\Entities\Insight\Events\Marfeel_Activation_Event;
use Base\Entities\Insight\Events\Marfeel_Deactivation_Event;
use Base\Entities\Settings\Mrf_Availability_Modes_Enum;
use Base\Entities\Settings\Mrf_Options_Enum;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Availability_Service {

	const ACTIVATED   = 'activated';
	const DEACTIVATED = 'deactivated';

	/** @var string */
	private $availability_mode;

	public function __construct() {
		$this->settings_service  = Marfeel_Press_App::make( 'settings_service' );
		$this->utils             = Marfeel_Press_App::make( 'plugin_settings_utils' );
		$this->availability_mode = $this->read_availability();
	}

	private function read_availability() {
		$availability = $this->settings_service->get_option_data( Mrf_Options_Enum::OPTION_AVAILABILITY, null );

		return $availability ?: $this->settings_service->get( 'marfeel_press.availability' ) ?: Mrf_Availability_Modes_Enum::DEFAULT_MODE;
	}

	public function get_availability() {
		return $this->availability_mode;
	}

	public function set_availability( $new_availability, $amp = true, $is_warda_compatible = null ) {
		$current_availability = $this->availability_mode;
		$is_availability_change = $this->is_availability_change( $current_availability, $new_availability );

		if ( $new_availability != null ) {
			$this->save_availability_mode( $new_availability, $amp );

			if ( $is_warda_compatible != null ) {
				$this->set_mrf_router_mode_on_compatibility( $is_warda_compatible );
			}

			if ( $is_availability_change ) {
				$warda_active = $this->settings_service->get( Mrf_Options_Enum::OPTION_MRF_ROUTER );
				$this->track_availability_change( $current_availability, $new_availability, $warda_active );

				if ( $current_availability === Mrf_Availability_Modes_Enum::OFF ) {
					Marfeel_Press_App::make( 'mrf_insight_invalidator_service' )->invalidate_all();
				}
			}

			return true;
		}

		return false;
	}

	private function is_availability_change( $current_availability, $new_availability ) {
		return $new_availability && $current_availability !== $new_availability;
	}

	private function save_availability_mode( $new_availability, $amp ) {
		if ( $new_availability == Mrf_Availability_Modes_Enum::ALL ) {
			$this->settings_service->set( Mrf_Options_Enum::OPTION_AMP, $amp );
			$press_service = Marfeel_Press_App::make( 'press_service' );
			$this->settings_service->set_option_data( Mrf_Options_Enum::OPTION_AVAILABILITY, $new_availability );

			$press_service->flush_url_cache();

			if ( $this->settings_service->get( Mrf_Options_Enum::OPTION_ACTIVATED_ONCE ) !== true ) {
				$this->settings_service->set( Mrf_Options_Enum::OPTION_ACTIVATED_ONCE, true );
			}
		} else {
			$this->settings_service->set_option_data( Mrf_Options_Enum::OPTION_AVAILABILITY, $new_availability );
		}

		$this->availability_mode = $new_availability;
	}

	private function set_mrf_router_mode_on_compatibility( $is_warda_compatible ) {
		if ( isset( $is_warda_compatible ) && $is_warda_compatible === true ) {
			$this->settings_service->set( Mrf_Options_Enum::OPTION_MRF_ROUTER, true );
		} else {
			$this->settings_service->set( Mrf_Options_Enum::OPTION_MRF_ROUTER, false );
		}

		Marfeel_Press_App::make( 'insight_service' )->track_settings( $this->settings_service->get( 'marfeel_press' ) );
	}

	private function availability_change( $current_availability, $new_availability ) {
		if ( $new_availability === Mrf_Availability_Modes_Enum::ALL ) {
			return self::ACTIVATED;
		} elseif ( $current_availability === Mrf_Availability_Modes_Enum::ALL ) {
			return self::DEACTIVATED;
		}

		return null;
	}

	private function track_availability_change( $current_availability, $new_availability, $warda_active ) {
		$availability_change = $this->availability_change( $current_availability, $new_availability );
		Marfeel_Press_App::make( 'insight_service' )->track_settings( $this->settings_service->get( 'marfeel_press' ) );
		if ( $availability_change === self::ACTIVATED ) {
			$activation_type = Marfeel_Press_App::make( 'warda_service' )->get_activation_type( $warda_active );
			$this->tracker( new Marfeel_Activation_Event( array(), $activation_type ) );
		} elseif ( $availability_change === self::DEACTIVATED ) {
			$this->tracker( new Marfeel_Deactivation_Event() );
		}

		$this->track_mode_change_to_marketing_campaigns( $availability_change );
	}

	private function tracker( $event ) {
		$tracker = Marfeel_Press_App::make( 'tracker' );

		$tracker->identify( true );
		$tracker->track( $event );
	}

	private function track_mode_change_to_marketing_campaigns( $availability_change ) {
		if ( isset( $_COOKIE['mrf-press-userid'] ) ) {
			$google_code_url = 'https://script.google.com/macros/s/AKfycbxH9ZRzR0FO9gugcIW8XgN7NCAas5CHPTVOTX6AEahZHRZYsww/exec';
			$request_body = array(
				'id' => $_COOKIE['mrf-press-userid'],
			);

			if ( $availability_change !== null ) {
				$request_body[ $availability_change ] = true;
			}

			Marfeel_Press_App::make( 'http_client' )->request( 'POST', $google_code_url, array(
				'body' => wp_json_encode( $request_body ),
			) );
		}
	}
}
