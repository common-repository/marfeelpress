<?php

namespace Ads_Txt\Managers;

class Marfeel_Ads_Txt_Manager_Factory {

	/** @var Marfeel_Ads_Txt_Manager */
	public $manager;

	/** @var string[] */
	public $managers = array();

	public function __construct() {
		$this->managers[] = '\Ads_Txt\Managers\Marfeel_Ads_Txt_File_Manager';
		$this->managers[] = '\Ads_Txt\Managers\Marfeel_Ads_Txt_Router_Manager';
	}

	private function load() {
		foreach ( $this->managers as $manager ) {
			$manager = new $manager();

			if ( $manager->is_valid() ) {
				$this->manager = $manager;
				break;
			}
		}
	}

	public function get_manager() {
		if ( $this->manager === null ) {
			$this->load();
		}

		return $this->manager;
	}
}
