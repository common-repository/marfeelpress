<?php

namespace Ads_Txt\Routers;

use Base\Routers\Marfeel_Press_Rewrite_Router;
use Ads_Txt\Controllers\Marfeel_Press_Ads_Txt_Controller;

class Marfeel_Press_Ads_Txt_Router extends Marfeel_Press_Rewrite_Router {

	public function route() {
		$ads_txt_controller = new Marfeel_Press_Ads_Txt_Controller();
		$ads_txt_controller->render_ads_txt();
		exit;
	}

	public function is_marfeelizable() {
		return true;
	}

	public function get_query_var() {
		if ( ! defined( 'MRFP_ADS_TXT_QUERY_VAR' ) ) {
			define( 'MRFP_ADS_TXT_QUERY_VAR', apply_filters( $this->get_query_var_value(), $this->get_path_value() ) );
		}

		return MRFP_ADS_TXT_QUERY_VAR;
	}

	public function get_path_value() {
		return 'ads(?:\.txt)?$';
	}

	public function get_query_var_value() {
		return 'ads_txt_query_var';
	}
}
