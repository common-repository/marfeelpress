<?php

namespace Base\Entities\Settings;

use Base\Entities\Settings\Mrf_Setting;
use Base\Entities\Settings\Mrf_Amp_Setting;
use Base\Entities\Settings\Mrf_Tenant_Type;

class Mrf_Press_Setting extends Mrf_Setting {

	/** @var int */
	public $mode;

	/** @var string */
	public $api_token;

	/** @var string */
	public $insight_token;

	/** @var string */
	public $tenant_type = Mrf_Tenant_Type::LONGTAIL;

	/** @var bool */
	public $mrf_router_active = false;

	/** @var bool */
	public $custom_garda = false;

	/** @var bool */
	public $multilanguage = false;

	/** @var array */
	public $multilanguage_options = [];

	/** @var bool */
	public $cache_active;

	/**
	 * @var int
	 * @jsonRemove
	 */
	public $install_error = 0;

	/** @var bool */
	public $activated_once = false;

	/** @var string */
	public $media_group;

	/** @var string */
	public $availability;

	/** @var Base\Entities\Settings\Mrf_Amp_Setting */
	public $amp;

	/** @var string */
	public $home_name;

	/** @var bool */
	public $avoid_query_params;

	/** @var string */
	public $plugin_status;

	/** @var bool */
	public $disable_multipage;

	/** @var bool */
	public $sticky_posts_on_top = true;

	/**
	 * @var array
	 * @jsonRemove
	 */
	public $versions = array(
		'MRFP_LEROY_BUILD_NUMBER' => 777,
	);

	/** @var bool */
	public $token_handshake = false;

	public function __construct() {
		$this->amp = new Mrf_Amp_Setting();
	}
}
