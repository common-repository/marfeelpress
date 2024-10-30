<?php

namespace Base\Entities;

use Base\Entities\Settings\Mrf_Setting;
use Base\Entities\Settings\Mrf_Press_Setting;
use Base\Entities\Settings\Mrf_Ads_Setting;
use Base\Entities\Settings\Mrf_Ads_Txt_Setting;
use Base\Entities\Mrf_Cherokee;
use Base\Entities\Mrf_User_Interface;
use Base\Entities\Mrf_Push_Notifications;

class Mrf_Marfeel_Definition extends Mrf_Setting {

	/** @var string */
	public $tenant_home;

	/** @var string */
	public $name;

	/** @var string */
	public $uri;

	/** @var string */
	public $disclaimer;

	/** @var string */
	public $cookies_policy;

	/** @var string */
	public $title;

	/** @var string */
	public $configuration;

	/** @var string */
	public $publisher;

	/** @var array */
	public $post_type = array( 'post' );

	/** @var Base\Entities\Mrf_Cherokee */
	public $cherokee;

	/** @var Base\Entities\Mrf_User_Interface */
	public $user_interface;

	/** @var Base\Entities\Settings\Mrf_Press_Setting */
	public $marfeel_press;

	/** @var Base\Entities\Settings\Mrf_Ads_Setting */
	public $ads;

	/** @var Base\Entities\Settings\Mrf_Ads_Txt_Setting */
	public $adstxt;

	/** @var Base\Entities\Mrf_Push_Notifications */
	public $active_push_notifications;

	/** @var Base\Entities\Mrf_Section */
	public $sections;

	public function __construct() {
		$this->user_interface = new Mrf_User_Interface();
		$this->marfeel_press = new Mrf_Press_Setting();
		$this->ads = new Mrf_Ads_Setting();
		$this->adstxt = new Mrf_Ads_Txt_Setting();
		$this->cherokee = new Mrf_Cherokee();
		$this->active_push_notifications = new Mrf_Push_Notifications();
	}

}
