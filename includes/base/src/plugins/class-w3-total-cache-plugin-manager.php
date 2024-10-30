<?php

namespace Base\Plugins;

use Ioc\Marfeel_Press_App;

class W3_Total_Cache_Plugin_Manager extends Abstract_Cache_Plugins_Manager {

	public function adapt_to_press() {
		$this->create_user_agent_group();
	}

	public function flush_cache() {
		w3tc_flush_all();
	}

	public function is_installed(){
		return function_exists( 'w3tc_save_user_agent_group' );
	}

	protected function has_device_detection() {
		$mobile = Marfeel_Press_App::make( 'mobile_useragent' );
		return count( $mobile->get_groups() ) > 0;
	}

	private function create_user_agent_group() {
		$user_agents = Marfeel_Press_App::make( 'marfeel_user_agents' )->get_escaped_mobile_user_agents();
		w3tc_save_user_agent_group( 'marfeel', 'default', '', array( $user_agents ), true );
	}

}
