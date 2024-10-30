<?php

namespace Base;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Content_Type_Service {
	const ARTICLE = 'article';
	const SECTION = 'section';


	public function get_marfeelct() {
		$content_type = $this->get_current_content_type();

		if ( ! empty( $content_type ) ) {
			return 'marfeelct=' . $content_type;
		}

		return null;
	}

	public function get_data_mrf_ct() {
		$content_type = $this->get_current_content_type();

		if ( ! empty( $content_type ) ) {
			return 'data-mrf-ct="' . $content_type . '"';
		}

		return '';
	}

	private function get_current_content_type() {
		$is_home = Marfeel_Press_App::make( 'press_service' )->is_home();

		if ( is_category() || $is_home ) {
			return self::SECTION;
		} elseif ( is_single() ) {
			return self::ARTICLE;
		}

		return null;
	}
}
