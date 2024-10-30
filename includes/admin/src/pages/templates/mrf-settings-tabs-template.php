<?php

use Ioc\Marfeel_Press_App;

$settings_service = Marfeel_Press_App::make( 'settings_service' );

if ( $settings_service->get( 'marfeel_press.activated_once' ) ) {
	echo '<nav class="nav settings-nav pt-4 pl-2">';

	foreach ( $context->settings as $setting ) {
		$active = $setting->id == $context->page ? 'active' : '';
		echo '<a class="nav-link ' . $active . '" href="' . $setting->get_setting_url() . '">' . $setting->title . '</a>';
	}

	echo '</nav>';
}

require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . $context->template );
