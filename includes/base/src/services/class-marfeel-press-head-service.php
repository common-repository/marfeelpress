<?php

namespace Base\Services;

use WPSEO_Options;
use WPSEO_OpenGraph;
use Ioc\Marfeel_Press_App;
use Wa72\HtmlPageDom\HtmlPage;
use Base\Entities\Settings\Mrf_Options_Enum;

class Marfeel_Press_Head_Service {

	const WP_HEAD = "wp_head";
	const HTML = "html";
	const DOMELEMENT_TYPE = 1;

	/** @var string */
	public static $head;

	public function extract_metadata( $object ) {
		$this->prepare_head();

		$object->tag_information->html = $this->get_head();
		$parser = new HtmlPage( $object->tag_information->html );

		Marfeel_Press_App::make( 'metadata_extractor' )->extract( $parser, $object->tag_information );
	}

	public function capture_head() {
		add_action( self::WP_HEAD, function() {
			ob_start( array( '\Base\Services\Marfeel_Press_Head_Service', 'store_head' ) );
		}, 0);

		add_action( self::WP_HEAD, function() {
			ob_end_flush();
		}, PHP_INT_MAX);
	}

	public static function store_head( $head ) {
		Marfeel_Press_Head_Service::$head = $head;
	}

	public function get_head() {
		ob_start();

		apply_filters( self::WP_HEAD, self::$head );

		$head = ob_get_clean();

		$final_head = strlen( $head ) > strlen( self::$head ) ? $head : self::$head;

		return $this->clean_amplink( $final_head );
	}

	public function clean_amplink( $html ) {
		if ( $html === null ) {
			return $html;
		}

		preg_match( '/<link rel=\"amphtml\" href="([^"]*)"/', $html, $amphtml_href );

		if ( count( $amphtml_href ) === 0 ) {
			return $html;
		}

		$clean_amp_uri = Marfeel_Press_App::make( 'uri_utils' )->clean_amp_uri( $amphtml_href[1] );
		$clean_html = preg_replace( '/<link rel="amphtml".*?>/',
		'<link rel="amphtml" href="' . $clean_amp_uri . '">',
		$html );

		return $clean_html;
	}

	public function add_robots() {
		$this->capture_head();

		add_action( self::WP_HEAD, function() {
			echo Marfeel_Press_Head_Service::$head;

			if ( ! preg_match( '/<meta\s+name=["\']robots["\']/', Marfeel_Press_Head_Service::$head ) ) {
				echo '<meta name="robots" content="max-snippet:-1, max-image-preview:large, max-video-preview:-1">';
			}
		}, PHP_INT_MAX);
	}

	public function add_marfeelgarda_if_needed() {
		add_action( 'wp_head', function() {
			if ( $this->needs_garda() ) {
				$this->add_marfeelgarda();
			}
		}, 0 );
	}

	public function add_mrf_extractable_false_if_needed() {
		add_action( 'wp_head', function() {
			$should_forbid_extractions = false;

			if ( is_category() && ! Marfeel_Press_App::make( 'press_service' )->is_marfeelizable_category( get_queried_object() ) ) {
				$should_forbid_extractions = true;
			}

			if ( is_single() && ! Marfeel_Press_App::make( 'post_service' )->is_marfeelizable( get_post() ) ) {
				$should_forbid_extractions = true;
			}

			if ( $should_forbid_extractions ) {
				$mrf_extractable = 'false';
				include_once MRFP__MARFEEL_PRESS_DIR . 'includes/base/src/templates/meta-mrf-extractable.php';
			}
		});
	}

	public function add_generator_marfeel_if_needed() {
		add_action( 'wp_head', function() {
			if ( Marfeel_Press_App::make( 'request_utils' )->is_test_device() && Marfeel_Press_App::make( 'device_detection' )->is_mobile() ) {
				echo '<meta name="generator" content="MarfeelPress">';
			}
		}, 0 );
	}

	private function needs_garda() {
		return ! Marfeel_Press_App::make( 'settings_service' )->get( Mrf_Options_Enum::OPTION_MRF_ROUTER )
				&& ! Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.custom_garda' )
				&& ! is_admin()
				&& ! defined( 'REST_REQUEST' )
				&& ( is_front_page() || $this->is_marfeelizable_category() || ( ! is_category() && ! is_single() && ! is_page() ) || Marfeel_Press_App::make( 'post_service' )->is_marfeelizable( get_post() ) );
	}

	private function is_marfeelizable_category() {
		return is_category() && Marfeel_Press_App::make( 'press_service' )->is_marfeelizable_category( get_queried_object() );
	}

	private function add_marfeelgarda() {
		$host = Marfeel_Press_App::make( 'uri_utils' )->remove_protocol( MRFP_RESOURCES_HOST );

		$marfeel_ct = Marfeel_Press_App::make( 'ct_service' )->get_data_mrf_ct();

		include_once MRFP__MARFEEL_PRESS_DIR . 'includes/base/src/templates/marfeelgarda.php';
	}

	public function add_gardac_press() {
		$url = MRFP_RESOURCES_HOST . '/statics/marfeel/gardacpress.js';
		echo '<script src="' . $url . '"></script>';
	}

	public function add_resizer( $html = null ) {
		if ( $html === null ) {
			add_action( 'wp_enqueue_scripts', function() {
				wp_enqueue_script( 'mrf_resizer', MRFP__MARFEEL_PRESS_RESOURCES_URL . 'js/resizer.js', array(), false, true );
			});
		} elseif ( isset( $_GET['mrf'] ) && $_GET['mrf'] == 1 ) {
			return str_replace( '</body>', '<script type="text/javascript" src="' . MRFP__MARFEEL_PRESS_RESOURCES_URL . 'js/resizer.js' . '"></script></body>', $html );
		}

		return $html;
	}

	protected function prepare_head() {
		if ( $this->is_yoast_seo_activated() ) {
			$this->prepare_yoast_seo();
		}
	}

	protected function prepare_yoast_seo() {
		$options = WPSEO_Options::get_option( 'wpseo_social' );
		if ( $options['twitter'] === true ) {
			add_action( 'wpseo_head', array( 'WPSEO_Twitter', 'get_instance' ), 40 );
		}

		if ( $options['opengraph'] === true ) {
			$GLOBALS['wpseo_og'] = new WPSEO_OpenGraph();
		}
	}

	protected function is_yoast_seo_activated() {
		return class_exists( 'WPSEO_Options' ) && class_exists( 'WPSEO_OpenGraph' );
	}

}
