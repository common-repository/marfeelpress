<?php

namespace Base;
use Base\Entities\Settings\Mrf_Tenant_Type;
use Ioc\Marfeel_Press_App;
/**
 * Admin activation class
 *
 * @package marfeel-press
 */

class Marfeel_Press_Admin_Initialization {

	public function redirect_if_activation() {
		if ( ! get_transient( 'mrf_activation_redirect' ) ) {
			return;
		}

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		wp_redirect( add_query_arg( array(
			'page' => 'mrf-signup',
			'autoload' => true,
		), admin_url( 'admin.php' ) ) );

	}

}
