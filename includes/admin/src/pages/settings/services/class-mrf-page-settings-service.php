<?php

namespace Admin\Pages\Settings\Services;

use Ioc\Marfeel_Press_App;
use Base\Entities\Settings\Mrf_Availability_Modes_Enum;
use Base\Entities\Settings\Mrf_Tenant_Type;
use Admin\Marfeel_Press_Admin_Translator;
use Base\Entities\Settings\Mrf_Options_Enum;
use Base\Marfeel_Press_Plugin_Conflict_Manager;
use Base\Services\Marfeel_Press_Marfeel_Name_Service;

class Mrf_Page_Settings_Service {

	public function __construct() {
		$this->settings_service = Marfeel_Press_App::make( 'settings_service' );
		$this->utils = Marfeel_Press_App::make( 'plugin_settings_utils' );
	}

	public function load_settings( $context ) {
		$context = $this->load_basic_settings( $context );

		if ( $context->is_advanced || $context->is_dev ) {
			$context = $this->load_advanced_settings( $context );
		}

		if ( $context->is_dev ) {
			$context = $this->load_dev_settings( $context );
		}

		return $context;
	}

	public function load_basic_settings( $context ) {
		$context->tenant_type           = esc_attr( $this->settings_service->get( Mrf_Options_Enum::OPTION_TENANT_TYPE ) );
		$context->is_longtail           = $context->tenant_type === Mrf_Tenant_Type::LONGTAIL;
		$context->is_dev                = Marfeel_Press_App::make( 'request_utils' )->is_dev() ? 1 : 0;
		$context->is_advanced           = Marfeel_Press_App::make( 'request_utils' )->is_advanced() ? 1 : 0;
		$context->template              = 'mrf-plugin-settings-template.php';
		$context->wp_post_types         = get_post_types( array(
			'public' => true,
			'publicly_queryable' => true,
		) );
		$context->post_type             = (array) $this->settings_service->get( Mrf_Options_Enum::OPTION_POST_TYPE );
		$context->variant               = isset( $context->variant ) ? $context->variant : '';
		$context->message_txt           = isset( $context->message_txt ) ? $context->message_txt : '';
		$context->selected_availability = $this->settings_service->get_option_data( Mrf_Options_Enum::OPTION_AVAILABILITY, Mrf_Availability_Modes_Enum::DEFAULT_MODE );
		$context->is_local_env          = Marfeel_Press_App::make( 'request_utils' )->is_local_env();
		$context->permalink_structure   = get_option( 'permalink_structure' );
		$context->sticky_posts_on_top   = $this->settings_service->get( Mrf_Options_Enum::OPTION_STICKY_POSTS_ON_TOP );

		$option_amp = $this->settings_service->get( Mrf_Options_Enum::OPTION_AMP );
		$context->amp = ( $option_amp || $option_amp === null ) ? "1" : "0";

		$context->amp_url = $this->settings_service->get( Mrf_Options_Enum::OPTION_AMP_URL );
		if ( $context->amp_url === null ) {
			$context->amp_url = '/amp/';
		}

		$context->options = array(
			array(
				'mode'        => Mrf_Availability_Modes_Enum::ALL,
				'description' => Marfeel_Press_Admin_Translator::trans( 'mrf.activation.all' ),
			),
			array(
				'mode'        => Mrf_Availability_Modes_Enum::LOGGED,
				'description' => Marfeel_Press_Admin_Translator::trans( 'mrf.activation.logged' ),
			),
			array(
				'mode'        => Mrf_Availability_Modes_Enum::OFF,
				'description' => Marfeel_Press_Admin_Translator::trans( 'mrf.activation.off' ),
			),
		);

		$context->languages = Marfeel_Press_Marfeel_Name_Service::$languages;

		return $context;
	}

	public function load_advanced_settings( $context ) {
		$context->mrf_router            = $this->settings_service->get( Mrf_Options_Enum::OPTION_MRF_ROUTER );
		$context->custom_garda          = $this->settings_service->get( Mrf_Options_Enum::OPTION_CUSTOM_GARDA );
		$context->cache                 = $this->settings_service->get( Mrf_Options_Enum::OPTION_CACHE );
		$context->multilanguage         = $this->settings_service->get( Mrf_Options_Enum::OPTION_MULTILANGUAGE );
		$context->multilanguage_options = $this->settings_service->get( Mrf_Options_Enum::OPTION_MULTILANGUAGE_OPTIONS );

		$softchecks = Marfeel_Press_App::make( 'checks_service' )->get_soft_checks();

		$context->has_cache_plugin_installed      = $softchecks->hasCachePlugin; //@codingStandardsIgnoreLine
		$context->all_cache_plugin_are_supported = $softchecks->allCachePluginAreSupported; //@codingStandardsIgnoreLine
		$context->has_cache_plugin_needing_fix    = $softchecks->hasCachePluginNeedingFix; //@codingStandardsIgnoreLine

		// TO-DO: Change can_activate_warda logic
		$context->can_activate_warda = true;
		$context->can_activate_warda_with_plugin_change = $context->all_cache_plugin_are_supported && $context->has_cache_plugin_needing_fix;
		$context->has_unsupported_cache_plugin = Marfeel_Press_App::make( 'plugin_conflict_manager' )->has_cache_plugin_unsupported();

		return $context;
	}

	public function load_dev_settings( $context ) {
		$context->tenant_home        = esc_attr( $this->settings_service->get( Mrf_Options_Enum::OPTION_TENANT_HOME ) );
		$context->tenant_uri         = esc_attr( $this->settings_service->get( Mrf_Options_Enum::OPTION_TENANT_URI ) );
		$context->media_group        = esc_attr( $this->settings_service->get( Mrf_Options_Enum::OPTION_MEDIA_GROUP ) );
		$context->api_token          = esc_attr( $this->settings_service->get( Mrf_Options_Enum::OPTION_API_TOKEN ) );
		$context->log_provider       = Marfeel_Press_App::make( 'text_file_log_provider' );
		$context->avoid_query_params = esc_attr( $this->settings_service->get( Mrf_Options_Enum::OPTION_AVOID_QUERY_PARAMS ) );
		$context->disable_multipage  = esc_attr( $this->settings_service->get( Mrf_Options_Enum::OPTION_DISABLE_MULTIPAGE ) );

		return $context;
	}

	public function save_settings( $context ) {
		$this->previous_warda = $this->settings_service->get( Mrf_Options_Enum::OPTION_MRF_ROUTER );
		$this->previous_availability = $this->settings_service->get_option_data( Mrf_Options_Enum::OPTION_AVAILABILITY, null );

		if ( $this->is_factory_reset() ) {
			$this->reset_all_settings();
		}

		if ( $this->is_update_leroy() ) {
			$this->update_leroy();
		}

		if ( $this->utils->has_been_submitted( 'enable_mobile_cache' ) ) {
			Marfeel_Press_Plugin_Conflict_Manager::enable_cache_mobile_detection();
			return $context;
		}

		if ( $this->is_save_ok() ) {
			$this->utils->set_success_msg( $context );

			$values = $this->get_basic_form_values( $context );

			if ( $this->needs_advanced_values() ) {
				$values = array_merge( $values, $this->get_advanced_form_values() );
			}

			if ( $this->needs_dev_values() ) {
				$values = array_merge( $values, $this->get_dev_form_values( $context ) );
			}

			$current_post_type = $this->settings_service->get( Mrf_Options_Enum::OPTION_POST_TYPE );

			if ( array_key_exists( Mrf_Options_Enum::OPTION_POST_TYPE, $values )
				&& $values[ Mrf_Options_Enum::OPTION_POST_TYPE ]
				!= $current_post_type ) {
				Marfeel_Press_App::make( 'mrf_insight_invalidator_service' )->invalidate_all();
			}

			$this->settings_service->set( $values );

			Marfeel_Press_App::make( 'insight_service' )->track_settings( $this->settings_service->get( 'marfeel_press' ) );
			Marfeel_Press_App::make( 'warda_service' )->track_warda_if_needed( $this->previous_warda, $values[ Mrf_Options_Enum::OPTION_MRF_ROUTER ], $this->previous_availability, $_POST['availability'] );
		} else {
			$this->utils->set_error_msg( $context, 'Error!' );
		}

		$context->content_saved = true;

		$amp = isset( $_POST['amp'] ) && $_POST['amp'] == 1;
		$is_availability_updated = Marfeel_Press_App::make( 'availability_service' )->set_availability( $_POST['availability'], $amp );

		if ( ! $is_availability_updated ) {
			$this->utils->set_error_msg( $context, 'Your Marfeel mobile version is being generated. Please try in a few minutes!' );
			$context->content_saved = false;
		}

		return $context;
	}

	private function needs_advanced_values() {
		return isset( $_POST['is-advanced'] ) && $_POST['is-advanced'] == 1 || isset( $_POST['is-dev'] ) && $_POST['is-dev'] == 1;
	}

	private function needs_dev_values() {
		return isset( $_POST['is-dev'] ) && $_POST['is-dev'] == 1;
	}

	private function is_factory_reset() {
		return $this->utils->has_been_submitted( 'reset' ) && $_POST['is-dev'] == 1;
	}

	private function reset_all_settings() {
		$this->settings_service->remove_all();
		echo '<h1>Factory settings reset</h1>';
		die;
	}

	private function is_update_leroy() {
		return isset( $_POST['reset-version'] );
	}

	private function update_leroy() {
		$versions = $this->settings_service->get( 'marfeel_press.versions' );
		$versions['timestamp'] = 0;
		$this->settings_service->set( 'marfeel_press.versions', $versions );
	}

	private function is_save_ok() {
		return $this->utils->has_been_submitted( 'ok' );
	}

	private function get_basic_form_values( $context ) {
		return array(
			Mrf_Options_Enum::OPTION_INSIGHT_TOKEN   => array_key_exists( 'insight-token', $_POST ) ? sanitize_text_field( $_POST['insight-token'] ) : $context->insight_token,
			Mrf_Options_Enum::OPTION_AMP             => isset( $_POST['amp'] ) && $_POST['amp'] == 1,
			Mrf_Options_Enum::OPTION_AMP_URL         => $_POST['amp-url'],
			Mrf_Options_Enum::OPTION_STICKY_POSTS_ON_TOP         => isset( $_POST['sticky-posts-on-top'] ) && $_POST['sticky-posts-on-top'] == 1,
		);
	}

	private function get_advanced_form_values() {
		return array(
			Mrf_Options_Enum::OPTION_POST_TYPE             => isset( $_POST['post-type'] ) ? array_map( 'sanitize_text_field', $_POST['post-type'] ) : array(),
			Mrf_Options_Enum::OPTION_MRF_ROUTER            => isset( $_POST['mrf_router'] ),
			Mrf_Options_Enum::OPTION_CUSTOM_GARDA          => isset( $_POST['custom_garda'] ),
			Mrf_Options_Enum::OPTION_CACHE                 => isset( $_POST['cache'] ),
			Mrf_Options_Enum::OPTION_MULTILANGUAGE         => isset( $_POST['multilanguage'] ),
			Mrf_Options_Enum::OPTION_MULTILANGUAGE_OPTIONS => $_POST['multilanguage_options'],
		);
	}

	private function get_dev_form_values( $context ) {
		$permalink_structure = get_option( 'permalink_structure' );

		return array(
			Mrf_Options_Enum::OPTION_TENANT_HOME        => sanitize_text_field( $_POST['tenant-home'] ),
			Mrf_Options_Enum::OPTION_TENANT_URI         => sanitize_text_field( $_POST['tenant-uri'] ),
			Mrf_Options_Enum::OPTION_TENANT_TYPE        => isset( $_POST['move-to-enterprise'] ) ? Mrf_Tenant_Type::ENTERPRISE : sanitize_text_field( $_POST['tenant-type'] ),
			Mrf_Options_Enum::OPTION_MEDIA_GROUP        => sanitize_text_field( $_POST['media-group'] ),
			Mrf_Options_Enum::OPTION_API_TOKEN          => sanitize_text_field( $_POST['api-token'] ),
			Mrf_Options_Enum::OPTION_AVOID_QUERY_PARAMS => ! empty( $permalink_structure ) && isset( $_POST['avoid-query'] ),
			Mrf_Options_Enum::OPTION_DISABLE_MULTIPAGE  => isset( $_POST['disable-multipage'] ),
		);
	}

}
