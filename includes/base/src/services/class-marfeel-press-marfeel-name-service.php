<?php

namespace Base\Services;

use Base\Entities\Settings\Mrf_Options_Enum;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Marfeel_Name_Service {
	const MARFEEL_NAME_QUERY_PARAM = 'marfeeln';

	/** @var array */
	public static $languages = [ 'af', 'ar', 'be', 'bg', 'ca', 'da', 'de', 'el', 'en', 'es', 'et', 'eu', 'fi', 'fr', 'ga', 'gl', 'he', 'hi', 'hr', 'hu', 'it', 'ja', 'ko', 'lt', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sq', 'sr', 'sv', 'th', 'tr', 'zh' ];

	public function __construct() {
		$this->settings_service = Marfeel_Press_App::make( 'settings_service' );
	}

	public function get_marfeel_name_param( $url ) {
		if ( $this->settings_service->get( Mrf_Options_Enum::OPTION_MULTILANGUAGE ) ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'Add multilanguage query parameter to: ' . wp_json_encode( $url ),'w' );
			return self::MARFEEL_NAME_QUERY_PARAM . '=' . $this->get_lang( $url );
		}

		return self::MARFEEL_NAME_QUERY_PARAM . '=index';
	}

	private function get_lang( $url ) {
		$multilanguage_options = explode( ',', $this->settings_service->get( Mrf_Options_Enum::OPTION_MULTILANGUAGE_OPTIONS ) );
		$lang_candidate = $this->extract_language_candidate( $url );

		if ( in_array( $lang_candidate, $multilanguage_options ) ) {
			Marfeel_Press_App::make( 'log_provider' )->write_log( 'Language detected on url is listed in settings: ' . wp_json_encode( $lang_candidate ),'w' );
			return $lang_candidate;
		}

		Marfeel_Press_App::make( 'log_provider' )->write_log( 'Language detected on url is not in settings: ' . wp_json_encode( $lang_candidate ),'w' );
		return 'index';
	}

	private function extract_language_candidate( $url ) {
		$url_parts = explode( '/', $url );

		if ( count( $url_parts ) >= 4 ) {
			return $url_parts[3];
		}

		return '';
	}

}
