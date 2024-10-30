<?php

namespace Base\Services;

use Base\Marfeel_Press_Plugin_Conflict_Manager;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Yoast_Configuration_Service {

	const YOAST_SEARCH_MENU_URL = '/wp-admin/admin.php?page=wpseo_titles';
	const YOAST_TITLES_CONFIGURATION_NAME = 'wpseo_titles';
	const YOAST_SITE_REPRESENTER_CONFIGURATION_NAME = 'company_or_person';
	const YOAST_COMPANY_NAME_CONFIGURATION_NAME = 'company_name';
	const YOAST_COMPANY_LOGO_CONFIGURATION_NAME = 'company_logo';
	const YOAST_PERSON_REPRESENTER_CONFIGURATION = 'person';

	public function show_alert_if_lacks_yoast_configuration() {
		if ( Marfeel_Press_Plugin_Conflict_Manager::is_yoast_seo_activated() ) {
			$has_company_name = $this->has_configuration_in_yoast( self::YOAST_COMPANY_NAME_CONFIGURATION_NAME );
			$has_company_logo = $this->has_configuration_in_yoast( self::YOAST_COMPANY_LOGO_CONFIGURATION_NAME );
			$is_person = $this->is_site_represented_by_person();

			if ( ! $this->has_site_representer_info( $is_person, $has_company_name, $has_company_logo ) ) {
				return $this->show_empty_fields_alert( $has_company_name, $has_company_logo );
			}
		}

		return null;
	}

	private function has_site_representer_info( $is_person, $has_company_name, $has_company_logo ) {
		return $is_person | ( $has_company_name && $has_company_logo );
	}

	private function is_site_represented_by_person() {
		return get_option( self::YOAST_TITLES_CONFIGURATION_NAME )[ self::YOAST_SITE_REPRESENTER_CONFIGURATION_NAME ] === self::YOAST_PERSON_REPRESENTER_CONFIGURATION;
	}

	private function has_configuration_in_yoast( $config_name ) {
		return ! empty( get_option( self::YOAST_TITLES_CONFIGURATION_NAME )[ $config_name ] );
	}

	private function show_empty_fields_alert( $has_company_name, $has_company_logo ) {
		$html_code = $this->build_alert_html( $has_company_name, $has_company_logo );
		echo $html_code;

		return $html_code;
	}

	private function build_alert_html( $has_company_name, $has_company_logo ) {
		$name = $has_company_name ? '' : ' company name';
		$and = ! $has_company_name && ! $has_company_logo ? ' and' : '';
		$logo = $has_company_logo ? '' : ' company logo';
		$s = $and ? 's' : '';
		$home_url = Marfeel_Press_App::make( 'uri_utils' )->get_home_url();
		$yoast_search_url = $home_url . self::YOAST_SEARCH_MENU_URL;

		return '<div class="error"><p>Please fill the' . $name . $and . $logo . ' field' . $s . ' in <a href="' . $yoast_search_url . '">Yoast</a> in order to have a pleasant <strong>MarfeelPress</strong> experience. </p></div>';
	}
}
