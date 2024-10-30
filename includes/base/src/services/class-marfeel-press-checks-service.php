<?php

namespace Base\Services;

use Base\Entities\Checks\Softchecks;
use Base\Marfeel_Press_Plugin_Conflict_Manager;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Checks_Service {

	const OPTION_SOFTCHECKS = 'mrf_softchecks';

	public function __construct() {
		$insight_url = Marfeel_Press_App::make( 'insight_service' )->get_insight_url();

		$this->plugin_activation_uri = $insight_url . '/marfeelpress/pluginactivation';
		$this->soft_requirements_audit_uri = $insight_url . '/marfeelpress/audit';
	}

	public function get_basic_checks() {
		$requirements_checker = Marfeel_Press_App::make( 'requirements_checker' );
		$requirements_checker->is_requirements_met();
		$requirements_not_met = $requirements_checker->get_requirements_not_met();

		$softchecks = new Softchecks();

		// @codingStandardsIgnoreStart
		$softchecks->tenant = Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' );
		$softchecks->pluginVersion = MRFP_PLUGIN_VERSION; //
		$softchecks->phpVersion = $requirements_checker->get_php_version();
		$softchecks->wordpressVersion = $requirements_checker->get_wordpress_version();

		$softchecks->hasWordPressMinVersion = ! isset( $requirements_not_met['wp_version'] );
		$softchecks->hasPhpMinVersion = ! isset( $requirements_not_met['php_version'] );
		$softchecks->hasXmlLib = ! isset( $requirements_not_met['ext:xml'] );
		// @codingStandardsIgnoreEnd

		return $softchecks;
	}

	public function get_soft_checks() {
		$adstxt_file_manager = Marfeel_Press_App::make( 'ads_txt_file_manager' );
		$cache_plugin_conflict_manager = Marfeel_Press_App::make( 'plugin_conflict_manager' );
		$posts_repository = Marfeel_Press_App::make( 'posts_repository' );
		$sections_repository = Marfeel_Press_App::make( 'sections_repository' );

		$softchecks = Marfeel_Press_App::make( 'checks_service' )->get_basic_checks();

		$current_plugins = Marfeel_Press_App::make( 'plugins_service' )->get();

		// @codingStandardsIgnoreStart
		$softchecks->hasAdsTxtWriteAccess = $adstxt_file_manager->is_valid();
		$softchecks->hasAdsTxtFile = $adstxt_file_manager->file_exists();

		$softchecks->hasCachePlugin = $cache_plugin_conflict_manager->has_cache_plugin_installed();
		$softchecks->allCachePluginAreSupported = ! $cache_plugin_conflict_manager->has_cache_plugin_unsupported();
		$softchecks->hasCachePluginNeedingFix = $cache_plugin_conflict_manager->has_cache_plugin_needing_device_detection_fix();

		$softchecks->numPosts = $posts_repository->count_published_posts();
		$softchecks->numSections = $sections_repository->count_sections();
		$softchecks->postsByMonth = $posts_repository->count_posts_by_month();
		$softchecks->postsBySection = $posts_repository->count_posts_by_category();
		$softchecks->postsByAuthor = $posts_repository->count_posts_by_author();
		$softchecks->numSidebars = count(Marfeel_Press_App::make( 'widgets_service' )->get());
		$softchecks->usedWidgets = Marfeel_Press_App::make( 'widgets_service' )->get_all_different_used_widgets();
		$softchecks->numUsedWidgets = count( $softchecks->usedWidgets );
		$softchecks->blacklistedCategories = $this->get_blacklisted_categories();
		$softchecks->incompatiblePlugins = $this->get_incompatible_plugins( $current_plugins );
		$softchecks->hasIncompatiblePlugins = sizeof( $softchecks->incompatiblePlugins ) > 0;
		// @codingStandardsIgnoreEnd

		$softchecks->plugins = $current_plugins;

		return $softchecks;
	}

	private function get_incompatible_plugins( $current_plugins = array() ) {
		$incompatible_plugins = array();

		foreach ( $current_plugins as $plugin ) {
			if ( $plugin['enabled'] && strpos( $plugin['name'], 'tagDiv' ) !== false && ! ( in_array( 'tagDiv', $incompatible_plugins ) ) ) {
				array_push( $incompatible_plugins, 'tagDiv' );
			}
		}

		return $incompatible_plugins;
	}

	private function get_blacklisted_categories() {

		$result = array();
		$categories = get_categories( array(
			'orderby' => 'term_id',
			'hide_empty' => false,
		) );

		$press_service = Marfeel_Press_App::make( 'press_service' );

		foreach ( $categories as $key => $cat ) {
			if ( ! $press_service->is_marfeelizable_category( $cat ) ) {
				$format_cat = new \stdClass();

				$format_cat->link = get_category_link( $cat->term_id );
				$format_cat->name = $cat->name;
				$format_cat->slug = $cat->slug;

				if ( $cat->parent != 0 ) {
					// @codingStandardsIgnoreStart
					$format_cat->parentLink = get_category_link( $cat->parent );
					$format_cat->parentName = get_cat_name( $cat->parent );
					// @codingStandardsIgnoreEnd
				}

				array_push( $result, $format_cat );
			}
		}

		return $result;
	}

	public function send_hard() {
		$checks = $this->get_basic_checks();

		Marfeel_Press_App::make( 'http_client' )->fire_and_forget( 'POST', $this->plugin_activation_uri, array(
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'body' => wp_json_encode( $checks ),
		) );
	}

	public function send_soft( $force = false ) {
		$settings_service = Marfeel_Press_App::make( 'settings_service' );
		$softchecks = false;

		if ( ! $force ) {
			$softchecks = $settings_service->get_option_data( self::OPTION_SOFTCHECKS, null );
		}
		if ( ! $softchecks ) {
			$key = Marfeel_Press_App::make( 'insight_service' )->get_insight_key();

			$response = Marfeel_Press_App::make( 'http_client' )->request( 'POST', $this->soft_requirements_audit_uri, array(
				'timeout' => 30,
				'headers' => array(
					'Content-Type' => 'application/json',
					'mrf-secret-key' => Marfeel_Press_App::make( 'insight_service' )->get_insight_key(),
				),
				'body' => wp_json_encode( $this->get_soft_checks() ),
			) );

			if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 ) {
				$softchecks = $response['body'];
				$settings_service->set_option_data( self::OPTION_SOFTCHECKS, $softchecks );
			} else {
				return $response;
			}
		}

		return $softchecks;
	}
}
