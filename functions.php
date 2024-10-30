<?php

use Ioc\Marfeel_Press_App;

function trans( $key ) {
	$translator = Marfeel_Press_App::make( 'translator' );
	return $translator::trans( $key );
}
