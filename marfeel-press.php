<?php

/*
 	Plugin Name:	MarfeelPress
 	Plugin URI:		https://www.marfeel.com
 	Description:	MarfeelPress: Mobile monetization and performance optimization plugin.
 	Author:			Marfeel Team
 	Version:		2.1.338
	License:		GPL2
	License URI:	https://www.gnu.org/licenses/gpl-2.0.html
 */

use Base\Marfeel_Press;
use Base\Marfeel_Press_Activation_Requirements_Checker;
use Ioc\Marfeel_Press_App;
use Base\Marfeel_Press_Admin_Initialization;
use Base\Services\Marfeel_Press_Yoast_Configuration_Service;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'constants.php' );
require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );

spl_autoload_register(function ( $class ) {
	$dirs = explode( "\\", $class );
	array_splice( $dirs, 1, 0, 'src' );
	$dirs = array_map( 'mrfp_normalize_classname', $dirs );
	end( $dirs );
	$last_id = key( $dirs );
	$dirs[ $last_id ] = 'class-' . $dirs[ $last_id ] . '.php';
	$path = implode( '/', $dirs );
	$class_path = MRFP__MARFEEL_PRESS_DIR . 'includes/' . $path ;
	$interface_path = str_replace( 'class-', 'interface-', $class_path );

	$found = @include_once( $class_path );
	if ( ! $found && file_exists( $interface_path ) ) {
		require_once( $interface_path );
	} else {
		return false;
	}
});

function mrfp_normalize_classname( $input ) {
	$tmp = str_replace( '_','-', $input );
	return strtolower( $tmp );
}

define( 'MRFP_MIN_PHP_VERSION', '5.3' );
define( 'MRFP_MIN_WP_VERSION', '4.7' );
define( 'MRFP_PLUGIN_VERSION', '2.1.338' );
define( 'MRFP_MARFEEL_PRESS_BUILD_NUMBER', '338' );

// @codingStandardsIgnoreLine
if ( file_exists( $composer_autoload = __DIR__ . '/vendor/autoload.php' ) || file_exists( $composer_autoload = WP_CONTENT_DIR . '/vendor/autoload.php' ) ) {
	require_once( $composer_autoload );
	require_once( plugin_dir_path( __FILE__ ) . 'vendor/symfony/polyfill-mbstring/bootstrap.php' );
}

Marfeel_Press_App::initialize();
require_once( MRFP__MARFEEL_PRESS_DIR . 'includes/ioc/src/context.php' );

function mrfp_activate_marfeel_press() {
	Marfeel_Press_App::make( 'activator' )->activate();
}

function mrfp_deactivate_marfeel_press() {
	Marfeel_Press_App::make( 'deactivator' )->deactivate();
}

function mrfp_update_marfeel_press( $upgrader_object, $options ) {
	Marfeel_Press_App::make( 'updater' )->update( $upgrader_object, $options );
}

function mrfp_uninstall_marfeel_press() {
	Marfeel_Press_App::make( 'uninstaller' )->uninstall();
}

Marfeel_Press_App::make( 'modules' );

register_activation_hook( __FILE__, 'mrfp_activate_marfeel_press', 20 );
register_deactivation_hook( __FILE__, 'mrfp_deactivate_marfeel_press' );
register_uninstall_hook( __FILE__, 'mrfp_uninstall_marfeel_press' );
add_action( 'upgrader_process_complete', 'mrfp_update_marfeel_press', 10, 2 );

$plugin = new Marfeel_Press();

$activation_requirements_checker = new Marfeel_Press_Activation_Requirements_Checker();
$admin_initialization = new Marfeel_Press_Admin_Initialization();
$yoast_service = new Marfeel_Press_Yoast_Configuration_Service();

add_action( 'admin_notices', array( $activation_requirements_checker, 'mrf_deactivate_on_signup_error' ) );
add_action( 'admin_notices', array( $yoast_service, 'show_alert_if_lacks_yoast_configuration' ) );

add_action( 'admin_init', array( $activation_requirements_checker, 'check_blacklisted_plugin_active' ) );
add_action( 'admin_init', array( $admin_initialization, 'redirect_if_activation' ) );
$plugin->run();
