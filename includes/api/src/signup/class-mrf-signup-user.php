<?php

namespace API\SignUp;

use API\Marfeel_API_Authentication_Service;
use API\Mrf_API;
use API\Marfeel_REST_API;
use Ioc\Marfeel_Press_App;
use WP_REST_Response;

class Mrf_Signup_User extends Mrf_API {

	public function __construct() {
		$this->resource_name = 'user';
		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);
	}

	public function get() {
		$is_admin = false;

		$email = $_REQUEST['email'];
		$user = get_user_by( 'email', $email );

		if ( $user ) {
			$is_admin = in_array( 'administrator', (array) $user->roles );
		}

		return new WP_REST_Response( array(
			'isAdmin' => $is_admin ? 'true' : 'false',
		) );
	}

	public function authenticate() {
		return Marfeel_API_Authentication_Service::authenticate();
	}
}
