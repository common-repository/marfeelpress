<?php

namespace Base\Trackers;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Tracker_Test {

	const ACCOUNT = 'CeAIeBXqVRVRYumjvBSeiO3KEB1NKaHd';
	const OPTION_TENANT_HOME = 'tenant_home';

	public function __construct() {
		$definition_service = Marfeel_Press_App::make( 'definition_service' );
		$this->tenant_home = $definition_service->get( self::OPTION_TENANT_HOME );
	}

	public function track( $action, $data = array() ) {
		return true;
	}

	public function track_to_insight( $type, $action ) {
		return true;
	}

	public function identify( $send_sales_force = false, $is_blog = null ) {
		return true;
	}

	public function get_configuration() {
		$user = wp_get_current_user();

		return array(
			'account' => self::ACCOUNT,
			'id' => $this->tenant_home,
			'user' => array(
				'email' => $user->user_email,
				'firstName' => $user->user_firstname,
				'lastName' => $user->user_lastname
			),
		);
	}

}
