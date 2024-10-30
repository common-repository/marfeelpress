<?php

namespace Base;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Plugin_Conflict_Manager {

	public static function activate() {
		self::prepare_for_yoast();
	}

	public function has_cache_plugin_installed() {
		$known_cache_plugins = Marfeel_Press_App::make( 'known_cache_plugins' );

		foreach ( $known_cache_plugins as $cache_plugin ) {
			if ( $cache_plugin->is_installed() ) {
				return true;
			}
		}

		return false;
	}

	public function has_cache_plugin_needing_device_detection_fix() {
		$known_cache_plugins = Marfeel_Press_App::make( 'known_cache_plugins' );
		foreach ( $known_cache_plugins as $cache_plugin ) {
			if ( $cache_plugin->is_installed() && $cache_plugin->needs_device_detection_fix() ) {
				return true;
			}
		}
		return false;
	}

	public function has_cache_plugin_unsupported() {
		$known_cache_plugins = Marfeel_Press_App::make( 'known_cache_plugins' );
		foreach ( $known_cache_plugins as $cache_plugin ) {
			if ( $cache_plugin->is_installed() && ! $cache_plugin->is_supported() ) {
				return true;
			}
		}
		return false;
	}

	public static function enable_cache_mobile_detection() {
		$known_cache_plugins = Marfeel_Press_App::make( 'known_cache_plugins' );

		foreach ( $known_cache_plugins as $cache_plugin ) {
			if ( $cache_plugin->needs_device_detection_fix() && $cache_plugin->is_supported() ) {
				$cache_plugin->adapt_to_press();
				$cache_plugin->flush_cache();
			}
		}
	}

	public static function start_api() {
		self::stop_buffer_handlers();
		self::disable_superpwa();
		self::disable_bj_lazy_load();
		self::detect_wmpl_multilanguage();
		add_action( 'wp_head', '\Base\Marfeel_Press_Plugin_Conflict_Manager::disable_adthrive' );
	}

	public static function detect_wmpl_multilanguage() {
		global $sitepress, $wpml_request_handler, $asfd;

		if ( isset( $sitepress ) && isset( $_GET['url'] ) ) {
			$current_language = $sitepress->get_language_from_url( $_GET['url'] );
			$wpml_request_handler->set_language_cookie( $current_language );
			define( 'WP_ADMIN', 1 );
			$sitepress->maybe_set_this_lang();
		}
	}

	public static function disable_extraction_plugins() {
		self::disable_adinserter();
	}

	public static function start_amp() {
		self::disable_newrelic();
	}

	public static function remove_unsupported_filters() {
		remove_filter( 'get_comments_number', 'mts_comment_count', 0 );
		remove_filter( 'the_content', 'mts_content_image_lazy_load_attr' );
		add_filter( 'sharing_show', '__return_false' );
	}

	public static function prepare_for_yoast() {
		if ( self::is_yoast_seo_activated() ) {
			add_filter( 'sanitize_custom_taxonomies', function( $custom_taxonomy, $taxonomies ) {
				if ( is_array( $custom_taxonomy ) ) {
					return $custom_taxonomy;
				}

				if ( $custom_taxonomy !== null && $custom_taxonomy !== 'yst_prominent_words' ) {
					$taxonomies[] = $custom_taxonomy;
				}
				return $taxonomies;
			}, 10, 2);
		}
	}

	public static function disable_adthrive() {
		global $wp_filter;

		if ( ! empty( $wp_filter['wp_head']->callbacks ) ) {
			foreach ( $wp_filter['wp_head']->callbacks as $index => $filter ) {
				foreach ( $filter as $key => $value ) {
					if ( strpos( $key, 'ad_head' ) !== false ) {
						foreach ( $value as $k => $v ) {
							if ( is_callable( $v ) ) {
								unset( $wp_filter['wp_head']->callbacks[ $index ][ $key ] );
							}
						}
					}
				}
			}
		}
	}

	protected static function disable_bj_lazy_load() {
		global $wp_filter;

		if ( ! empty( $wp_filter['wp']->callbacks ) ) {
			foreach ( $wp_filter['wp']->callbacks as $index => $filter ) {
				foreach ( $filter as $key => $value ) {
					if ( is_array( $value['function'] ) && is_object( $value['function'][0] ) && get_class( $value['function'][0] ) == 'BJLL' ) {
						unset( $wp_filter['wp']->callbacks[ $index ][ $key ] );
					}
				}
			}
		}
	}

	protected static function stop_buffer_handlers() {
		foreach ( ob_list_handlers() as $handler ) {
			ob_end_clean();
		}
	}

	public static function disable_superpwa() {
		remove_action( 'wp_head', 'superpwa_add_manifest_to_wp_head', 0 );
	}

	public static function disable_adinserter() {
		remove_filter( 'the_content', 'ai_content_hook', 99999 );
		remove_filter( 'wp_head', 'ai_wp_head_hook', 99999 );
	}

	public static function is_yoast_seo_activated() {
		return class_exists( 'WPSEO_Options' );
	}

	protected static function disable_newrelic() {
		if ( function_exists( 'newrelic_disable_autorum' ) ) {
			newrelic_disable_autorum();
		}
	}

	public static function get_modified_image_url( $url ) {
		if ( function_exists( 'ud_get_stateless_media' ) ) {
			$url = ud_get_stateless_media()->the_content_filter( $url );
		} elseif ( function_exists( 'jetpack_photon_url' ) ) {
			$url = jetpack_photon_url( $url );
		}

		return $url;
	}
}
