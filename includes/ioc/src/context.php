<?php

use Admin\Mode\Marfeel_Press_Admin;
use Admin\Pages\Settings\Mrf_Activation_Checker;
use Admin\Pages\Settings\Mrf_Ads;
use Admin\Pages\Settings\Mrf_Ads_Txt;
use Admin\Pages\Settings\Mrf_Analytics;
use Admin\Pages\Settings\Mrf_Comments;
use Admin\Pages\Settings\Mrf_Pwa;
use Admin\Pages\Settings\Mrf_Plugin_Settings;
use Admin\Pages\Settings\Mrf_Look_N_Feel;
use Admin\Pages\Settings\Mrf_Onboarding;
use Admin\Pages\Settings\Mrf_Sections;
use Admin\Pages\Settings\Mrf_Social;
use Admin\Pages\Settings\Mrf_Notifications;
use Admin\Pages\Settings\Mrf_Account;
use Admin\Pages\Settings\Mrf_Signup;
use Admin\Pages\Settings\Mrf_Advertising;
use Admin\Pages\Settings\Services\Mrf_Page_Settings_Service;
use Admin\Pages\Settings\Utils\Mrf_Plugin_Settings_Utils;
use Admin\Pages\Page_Settings;
use Admin\Pages\Page_Settings_Lite;
use Admin\Pages\Page_Start;
use Admin\Pages\Page_Account;
use Admin\Pages\Page_Signup;
use Admin\Pages\Page_Utils;
use Admin\Pages\Page_Notifications;
use Ads_Txt\Controllers\Marfeel_Press_Ads_Txt_Controller;
use Ads_Txt\Services\Marfeel_Press_Ads_Txt_Service;
use Ads_Txt\Marfeel_Ads_Txt_Plugin_Support;
use Ads_Txt\Routers\Marfeel_Press_Ads_Txt_Router;
use Ads_Txt\Managers\Marfeel_Ads_Txt_Manager_Factory;
use Ads_Txt\Managers\Marfeel_Ads_Txt_File_Manager;
use Amp\Routers\Marfeel_Press_AMP_Router;
use Amp\Services\Marfeel_Press_Amp_Service;
use API\availability\Mrf_Availability_Api;
use API\Definition\Mrf_Definition_API;
use API\Definition\Mrf_Ads_Txt_API;
use API\Definition\Mrf_Press_Settings_API;
use API\Menu\Mrf_Default_Menu_Service;
use API\Menu\Mrf_Menu_Api;
use API\Menu\Mrf_Menu_Categories_Api;
use API\Proxy\Mrf_Proxy_Api;
use API\Proxy\Mrf_Proxy_Utils;
use API\Twister\Mrf_Twister_API;
use API\Log\Mrf_Log_Api;
use API\WP\Mrf_Logo_Api;
use API\Widgets\Mrf_Widgets_Api;
use API\Widgets\Mrf_Widgets_Service;
use API\Plugins\Mrf_Plugins_Api;
use API\Plugins\Mrf_Compatible_Plugins_Api;
use API\Plugins\Mrf_Plugins_Service;
use API\Extract\Extractors\Api_Section_Extractor;
use API\Extract\Extractors\Api_Post_Extractor;
use API\Extract\Extractors\Api_Static_Extractor;
use API\SignUp\Services\Mrf_Insight_Invalidator_Service;
use API\Export\Exporters\Mrf_Ai1wpm_Exporter_Factory;
use API\Export\Preparators\Mrf_Export_Tmp_Posts_Related_Preparator;
use API\Export\Preparators\Mrf_Export_Tmp_Tables_Preparator;
use API\Extract\Marfeel_Press_Ripper_API;
use API\Extract\Marfeel_Press_Extractor_API;
use API\Test\Mrf_Test_Api;
use API\Marfeel_REST_API;
use Base\Marfeel_Press_Content_Type_Service;
use Base\Marfeel_Press_Plugin_Conflict_Manager;
use Base\Entities\Mrf_Marfeel_Definition;
use Base\Entities\Settings\Mrf_Tenant_Type;
use Base\Marfeel_Press_Activator;
use Base\Marfeel_Press_Deactivator;
use Base\Marfeel_Press_Uninstaller;
use Base\Marfeel_Press_Updater;
use Base\Marfeel_Press_Device_Detection;
use Base\Marfeel_Press_Proxy;
use Base\Repositories\Posts_Repository;
use Base\Repositories\Posts_Meta_Repository;
use Base\Repositories\Terms_Repository;
use Base\Services\Marfeel_Press_Yoast_Configuration_Service;
use Base\Services\Definition\Marfeel_Press_Definition_Default_Builder;
use Base\Services\Definition\Marfeel_Press_Definition_Settings_Builder;
use Base\Services\Definition\Marfeel_Press_Definition_WP_Builder;
use Base\Services\Insight\Marfeel_Press_Insight_Service;
use Base\Services\Insight\Marfeel_Press_Insight_Service_Test;
use Base\Services\Marfeel_Press_Availability_Service;
use Base\Services\Marfeel_Press_Custom_Headers_Service;
use Base\Services\Marfeel_Press_Head_Service;
use Base\Services\Marfeel_Press_Marfeel_Definition_Service;
use Base\Services\Marfeel_Press_Sections_Service;
use Base\Services\Marfeel_Press_Post_Service;
use Base\Services\Marfeel_Press_Service;
use Base\Services\Marfeel_Press_Log_Service;
use Base\Services\Marfeel_Press_Marfeel_Name_Service;
use Base\Services\Marfeel_Press_Custom_Service;
use Base\Services\Marfeel_Press_Settings_Service;
use Base\Services\Marfeel_Press_Versions_Service;
use Base\Services\Marfeel_Press_Warda_Service;
use Base\Services\Marfeel_Press_Terms_Service;
use Base\Services\Marfeel_Press_Top_Media_Service;
use Base\Services\Marfeel_Press_WP_Service;
use Base\Services\Marfeel_Press_WP_Features_Service;
use Base\Services\Modifiers\Marfeel_Press_Body_Modifier;
use Base\Services\Modifiers\Marfeel_Press_Toc_Modifier;
use Base\Utils\Http_Client;
use Base\Utils\Image_Utils;
use Base\Utils\Error_Utils;
use Base\Utils\Array_Utils;
use Base\Utils\Json_Serializer;
use Base\Utils\Mrf_Database_Wrapper;
use Base\Utils\Mrf_Filesystem_Wrapper;
use Base\Utils\Reflection_Utils;
use Base\Utils\Uri_Utils;
use Base\Utils\Request_Utils;
use Base\Utils\Rewrite_Rules_Utils;
use Base\Utils\Marfeel_User_Agents;
use Base\Descriptor\Marfeel_Item_Buffer;
use Base\Descriptor\Marfeel_Press_Article_Loader;
use Base\Descriptor\Filter\Ripper_Filter;
use Base\Descriptor\Parser\Json_Parser;
use Base\Descriptor\Reader\Post_Body_Reader;
use Base\Descriptor\Layout\Tags_Slider_Layout_Composer;
use Base\Descriptor\Layout\Slider_Layout_Composer;
use Base\Descriptor\Layout\Balcon_Layout_Composer;
use Base\Descriptor\Layout\Raw_Html_Layout_Composer;
use Base\Descriptor\Layout\Ads_Layout_Composer;
use Base\Descriptor\Layout\Article_Layout_Composer;
use Base\Descriptor\Layout\Photo2_Layout_Composer;
use Base\Descriptor\Layout\Decorator\Tags_Slider\Tags_Slider_Menu_Decorator;
use Base\Inventory\Inventory_Filler;
use Base\Inventory\Inventory_Finder;
use Base\Inventory\Inventory_Renderer;
use Base\Inventory\Loader\Inventory_Loader;
use Base\Inventory\Loader\Marfeel_Inventory_Loader;
use Base\Inventory\Loader\File_Inventory_Loader;
use Base\Plugins\W3_Total_Cache_Plugin_Manager;
use Base\Plugins\Wp_Super_Cache_Plugin_Manager;
use Ads_Txt\Marfeel_Ads_Txt_Loader;
use Base\Trackers\Marfeel_Press_Tracker;
use Base\Trackers\Marfeel_Press_Tracker_Test;
use Pwa\Routers\Marfeel_Press_SW_Router;
use Mrf\Routers\Marfeel_Press_MRF_Router;
use Admin\Marfeel_Press_Admin_Invalidator;
use Admin\Utils\Marfeel_Press_Admin_Utils;
use Admin\Marfeel_Press_Admin_Ajax;
use API\Checks\Mrf_Softchecks_Results_Api;
use API\Checks\Mrf_Softchecks_Metrics_Api;
use API\Definition\Mrf_Ads_Txt_Update_API;
use Error_Handling\Marfeel_Press_Error_Handler;
use Error_Handling\Writers\Marfeel_Press_Monolog_File_Writer;
use Error_Handling\Providers\Marfeel_Press_Text_File_Log_Provider;
use Marfeel\Monolog\Logger;
use Ioc\Marfeel_Press_App;
use Base\Marfeel_Press_Activation_Requirements_Checker;
use API\SignUp\Mrf_Signup_User;
use Base\Plugins\Wp_Rocket_Plugin_Manager;
use Base\Plugins\Breeze_Plugin_Manager;
use Base\Repositories\Sections_Repository;
use Base\Services\Marfeel_Press_Checks_Service;
use Base\Services\Marfeel_Press_Content_Service;
use Base\Services\Metadata\Marfeel_Press_Metadata_Extractor;
use Base\Services\Metadata\Marfeel_Press_Metatags_Extractor;
use Base\Services\Metadata\Marfeel_Press_Rel_Extractor;
use Base\Services\Metadata\Marfeel_Press_Title_Extractor;
use Base\Utils\Html_Utils;
use W3TC\Mobile_UserAgent;

Marfeel_Press_App::singleton( 'modules', function() {
	$modules = array(
		'\Ads_Txt\Marfeel_Ads_Txt_Module',
		'\Base\Modules\Marfeel_Press_Garda_Module',
	);

	foreach ( $modules as $module ) {
		new $module();
	}
} );

Marfeel_Press_App::singleton( 'metadata_extractor', function() {
	return new Marfeel_Press_Metadata_Extractor();
} );

Marfeel_Press_App::singleton( 'metadata_extractors', function() {
	return array(
		new Marfeel_Press_Metatags_Extractor(),
		new Marfeel_Press_Title_Extractor(),
		new Marfeel_Press_Rel_Extractor(),
	);
} );

Marfeel_Press_App::singleton( 'mobile_useragent', function () {
	return new Mobile_UserAgent();
} );

Marfeel_Press_App::singleton( 'w3_total_cache_plugin_manager', function () {
	return new W3_Total_Cache_Plugin_Manager();
} );

Marfeel_Press_App::singleton( 'known_cache_plugins', function () {
	return array(
		new W3_Total_Cache_Plugin_Manager(),
		new Wp_Super_Cache_Plugin_Manager(),
		new Wp_Rocket_Plugin_Manager(),
		new Breeze_Plugin_Manager(),
	);
} );

Marfeel_Press_App::singleton( 'insight_service', function () {
	if ( Marfeel_Press_App::make( 'request_utils' )->is_local_env() || defined( 'PHPUNIT_TEST' ) ) {
		return new Marfeel_Press_Insight_Service_Test();
	} else {
		return new Marfeel_Press_Insight_Service();
	}
} );

Marfeel_Press_App::singleton( 'proxy', function () {
	return new Marfeel_Press_Proxy();
} );

Marfeel_Press_App::singleton( 'requirements_checker', function () {
	return new Marfeel_Press_Activation_Requirements_Checker();
} );

Marfeel_Press_App::bind( 'wp_service', function () {
	return new Marfeel_Press_WP_Service();
});

Marfeel_Press_App::bind( 'wp_features_service', function () {
	return new Marfeel_Press_WP_Features_Service();
});

Marfeel_Press_App::singleton( 'posts_repository', function() {
	return new Posts_Repository();
} );

Marfeel_Press_App::singleton( 'sections_repository', function() {
	return new Sections_Repository();
} );

Marfeel_Press_App::singleton( 'posts_meta_repository', function() {
	return new Posts_Meta_Repository();
} );

Marfeel_Press_App::singleton( 'terms_repository', function() {
	return new Terms_Repository();
} );

Marfeel_Press_App::singleton( 'yoast_configuration_service', function() {
	return new Marfeel_Press_Yoast_Configuration_Service();
} );

Marfeel_Press_App::singleton( 'definition_builders', function () {
	return array(
		new Marfeel_Press_Definition_Default_Builder(),
		new Marfeel_Press_Definition_WP_Builder(),
		new Marfeel_Press_Definition_Settings_Builder(),
	);
} );

Marfeel_Press_App::singleton( 'image_utils', function () {
	return new Image_Utils();
} );

Marfeel_Press_App::singleton( 'error_utils', function () {
	return new Error_Utils();
} );

Marfeel_Press_App::singleton( 'array_utils', function () {
	return new Array_Utils();
} );

Marfeel_Press_App::singleton( 'post_service', function () {
	return new Marfeel_Press_Post_Service();
} );

Marfeel_Press_App::singleton( 'press_service', function () {
	return new Marfeel_Press_Service();
} );

Marfeel_Press_App::singleton( 'content_service', function () {
	return new Marfeel_Press_Content_Service();
} );

Marfeel_Press_App::singleton( 'custom_service', function () {
	return new Marfeel_Press_Custom_Service();
} );

Marfeel_Press_App::singleton( 'log_service', function () {
	return new Marfeel_Press_Log_Service();
} );

Marfeel_Press_App::singleton( 'marfeel_name_service', function () {
	return new Marfeel_Press_Marfeel_Name_Service();
} );

Marfeel_Press_App::singleton( 'head_service', function () {
	return new Marfeel_Press_Head_Service();
} );

Marfeel_Press_App::singleton( 'definition_service', function () {
	return new Marfeel_Press_Marfeel_Definition_Service();
} );

Marfeel_Press_App::singleton( 'definition_default_builder', function () {
	return new Marfeel_Press_Definition_Default_Builder();
} );

Marfeel_Press_App::singleton( 'definition_settings_builder', function () {
	return new Marfeel_Press_Definition_Settings_Builder();
} );

Marfeel_Press_App::singleton( 'section_service', function () {
	return new Marfeel_Press_Sections_Service();
} );

Marfeel_Press_App::singleton( 'top_media_service', function () {
	return new Marfeel_Press_Top_Media_Service();
} );

Marfeel_Press_App::singleton( 'settings_service', function () {
	return new Marfeel_Press_Settings_Service();
} );

Marfeel_Press_App::singleton( 'versions_service', function () {
	return new Marfeel_Press_Versions_Service();
} );

Marfeel_Press_App::singleton( 'warda_service', function () {
	return new Marfeel_Press_Warda_Service();
} );

Marfeel_Press_App::singleton( 'terms_service', function () {
	return new Marfeel_Press_Terms_Service();
} );

Marfeel_Press_App::singleton( 'plugin_conflict_manager', function () {
	return new Marfeel_Press_Plugin_Conflict_Manager();
} );

Marfeel_Press_App::singleton( 'definition_entity', function () {
	return new Mrf_Marfeel_Definition();
} );

Marfeel_Press_App::singleton( 'mobile_detect', function () {
	return new Marfeel_Mobile_Detect();
} );

Marfeel_Press_App::singleton( 'device_detection', function () {
	return new Marfeel_Press_Device_Detection();
} );

Marfeel_Press_App::singleton( 'filesystem_wrapper', function () {
	return new Mrf_Filesystem_Wrapper();
} );

Marfeel_Press_App::singleton( 'reflection_utils', function () {
	return new Reflection_Utils();
} );

Marfeel_Press_App::singleton( 'json_serializer', function () {
	return new Json_Serializer();
} );

Marfeel_Press_App::singleton( 'http_client', function () {
	return new Http_Client();
} );

Marfeel_Press_App::singleton( 'uri_utils', function () {
	return new Uri_Utils();
} );

Marfeel_Press_App::singleton( 'html_utils', function () {
	return new Html_Utils();
} );

Marfeel_Press_App::singleton( 'request_utils', function () {
	return new Request_Utils();
} );

Marfeel_Press_App::singleton( 'rewrite_rules_utils', function () {
	return new Rewrite_Rules_Utils();
} );

Marfeel_Press_App::singleton( 'marfeel_user_agents', function () {
	return new Marfeel_User_Agents();
} );

Marfeel_Press_App::bind( 'plugin_upgrader', function() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-includes/pluggable.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	return new Plugin_Upgrader();
} );

Marfeel_Press_App::singleton( 'ads_txt_file_manager', function () {
	return new Marfeel_Ads_Txt_File_Manager();
});

Marfeel_Press_App::singleton( 'ads_txt_manager', function () {
	$factory = new Marfeel_Ads_Txt_Manager_Factory();

	return $factory->get_manager();
} );

Marfeel_Press_App::singleton( 'marfeel_press_ads_txt_controller', function () {
	return new Marfeel_Press_Ads_Txt_Controller();
} );

Marfeel_Press_App::singleton( 'marfeel_press_ads_txt_service', function () {
	return new Marfeel_Press_Ads_Txt_Service();
} );

Marfeel_Press_App::singleton( 'text_file_log_provider', function () {
	return new Marfeel_Press_Text_File_Log_Provider();
} );

Marfeel_Press_App::singleton( 'error_handler', function () {
	return new Marfeel_Press_Error_Handler();
} );

Marfeel_Press_App::singleton( 'log_file_writer', function () {
	return new Marfeel_Press_Monolog_File_Writer();
} );

Marfeel_Press_App::singleton( 'log_file_path', function () {
	return MRFP__MARFEEL_PRESS_DIR . '/mrf-errors';
} );

Marfeel_Press_App::singleton( 'log_level', function () {
	if ( isset( $_REQUEST['marfeelDev'] ) && $_REQUEST['marfeelDev'] == 1 ) {
		return Logger::DEBUG;
	}
	return Logger::WARNING;
} );

Marfeel_Press_App::singleton( 'logger', function () {
	return new Logger( 'mrfLogger' );
} );

Marfeel_Press_App::singleton( 'custom_headers_service', function () {
	return new Marfeel_Press_Custom_Headers_Service();
} );

Marfeel_Press_App::singleton( 'api_extractor_section', function () {
	return new Api_Section_Extractor();
} );

Marfeel_Press_App::singleton( 'api_extractor_wp_post', function () {
	return new Api_Post_Extractor();
} );

Marfeel_Press_App::singleton( 'api_extractor_static', function () {
	return new Api_Static_Extractor();
} );

Marfeel_Press_App::singleton( 'ripper_api', function () {
	return new Marfeel_Press_Ripper_API();
} );

Marfeel_Press_App::singleton( 'extractor_api', function () {
	return new Marfeel_Press_Extractor_API();
} );

Marfeel_Press_App::singleton( 'rest_api', function () {
	return new Marfeel_REST_API();
} );

Marfeel_Press_App::singleton( 'definition_api', function () {
	return new Mrf_Definition_API();
} );

Marfeel_Press_App::singleton( 'ads_txt_api', function () {
	return new Mrf_Ads_Txt_API();
} );

Marfeel_Press_App::singleton( 'ads_txt_update_api', function () {
	return new Mrf_Ads_Txt_Update_API();
} );

Marfeel_Press_App::singleton( 'twister_api', function () {
	return new Mrf_Twister_API();
} );

Marfeel_Press_App::singleton( 'user_api', function () {
	return new Mrf_Signup_User();
} );

Marfeel_Press_App::singleton( 'log_api', function () {
	return new Mrf_Log_Api();
} );

Marfeel_Press_App::singleton( 'logo_api', function () {
	return new Mrf_Logo_Api();
} );

Marfeel_Press_App::singleton( 'test_api', function () {
	return new Mrf_Test_Api();
} );

Marfeel_Press_App::singleton( 'checks_service', function () {
	return new Marfeel_Press_Checks_Service();
} );

Marfeel_Press_App::singleton( 'softchecks_results_api', function () {
	return new Mrf_Softchecks_Results_Api();
} );

Marfeel_Press_App::singleton( 'softchecks_metrics_api', function () {
	return new Mrf_Softchecks_Metrics_Api();
} );

Marfeel_Press_App::singleton( 'log_provider', function() {
	return new Marfeel_Press_Text_File_Log_Provider();
} );

Marfeel_Press_App::bind( 'exporter_factory', 'ai1wpm_exporter_factory' );

Marfeel_Press_App::singleton( 'activator', function () {
	return new Marfeel_Press_Activator();
} );

Marfeel_Press_App::singleton( 'deactivator', function () {
	return new Marfeel_Press_Deactivator();
} );

Marfeel_Press_App::singleton( 'updater', function () {
	return new Marfeel_Press_Updater();
} );

Marfeel_Press_App::singleton( 'uninstaller', function () {
	return new Marfeel_Press_Uninstaller();
} );

Marfeel_Press_App::singleton( 'admin_settings', function () {
	if ( Mrf_Tenant_Type::is_longtail() ) {
		return array(
			new Mrf_Look_N_Feel(),
			new Mrf_Sections(),
			new Mrf_Analytics(),
			new Mrf_Comments(),
			new Mrf_Social(),
			new Mrf_Pwa(),
			new Mrf_Advertising(),
			new Mrf_Ads_Txt(),
			new Mrf_Plugin_Settings(),
		);
	} else {
		return array(
			new Mrf_Look_N_Feel(),
			new Mrf_Sections(),
			new Mrf_Analytics(),
			new Mrf_Comments(),
			new Mrf_Social(),
			new Mrf_Pwa(),
			new Mrf_Ads_Txt(),
			new Mrf_Plugin_Settings(),
		);
	}
} );

Marfeel_Press_App::singleton( 'onboarding_settings', function () {
	return array(
		new Mrf_Onboarding(),
		new Mrf_Analytics(),
		new Mrf_Comments(),
		new Mrf_Pwa(),
		new Mrf_Social(),
		new Mrf_Look_N_Feel(),
		new Mrf_Ads(),
		new Mrf_Advertising(),
		new Mrf_Ads_Txt(),
		new Mrf_Plugin_Settings(),
		new Mrf_Sections(),
	);
} );

Marfeel_Press_App::singleton( 'notifications_setting', function () {
	return new Mrf_Notifications();
} );

Marfeel_Press_App::singleton( 'activation_checker', function () {
	return new Mrf_Activation_Checker();
} );

Marfeel_Press_App::singleton( 'account_setting', function () {
	return new Mrf_Account();
} );

Marfeel_Press_App::singleton( 'signup_setting', function () {
	return new Mrf_Signup();
} );

Marfeel_Press_App::singleton( 'onboarding_setting', function () {
	return new Mrf_Onboarding();
} );

Marfeel_Press_App::singleton( 'general_setting', function () {
	return new Mrf_Analytics();
} );

Marfeel_Press_App::singleton( 'page_settings_service', function () {
	return new Mrf_Page_Settings_Service();
} );

Marfeel_Press_App::singleton( 'plugin_settings_utils', function () {
	return new Mrf_Plugin_Settings_Utils();
} );

Marfeel_Press_App::singleton( 'page_settings', function () {
	return new Page_Settings();
} );

Marfeel_Press_App::singleton( 'page_settings_lite', function () {
	return new Page_Settings_Lite();
} );

Marfeel_Press_App::singleton( 'page_start', function () {
	return new Page_Start();
} );

Marfeel_Press_App::singleton( 'page_account', function () {
	return new Page_Account();
} );

Marfeel_Press_App::singleton( 'page_signup', function () {
	return new Page_Signup();
} );


Marfeel_Press_App::singleton( 'page_notifications', function () {
	return new Page_Notifications();
} );

Marfeel_Press_App::singleton( 'page_utils', function () {
	return new Page_Utils();
} );

Marfeel_Press_App::singleton( 'mrf_router', function () {
	return new Marfeel_Press_MRF_Router();
} );

Marfeel_Press_App::singleton( 'amp_router', function () {
	return new Marfeel_Press_AMP_Router();
} );

Marfeel_Press_App::singleton( 'amp_service', function () {
	return new Marfeel_Press_Amp_Service();
} );

Marfeel_Press_App::singleton( 'sw_router', function () {
	return new Marfeel_Press_SW_Router();
} );

Marfeel_Press_App::singleton( 'ads_txt_router', function () {
	return new Marfeel_Press_Ads_Txt_Router();
} );

Marfeel_Press_App::bind( 'ai1wm_directory', 'Ai1wm_Directory' );

Marfeel_Press_App::singleton( 'descriptor_body_reader', function ( $container, $section ) {
	return new Post_Body_Reader( $section );
} );

Marfeel_Press_App::bind( 'descriptor_json_parser', function ( $container, $section ) {
	return new Json_Parser( $section );
} );

Marfeel_Press_App::bind( 'descriptor_layout_tagsSlider', function ( $container, $layout ) {
	return new Tags_Slider_Layout_Composer( $layout );
} );

Marfeel_Press_App::bind( 'descriptor_layout_slider', function ( $container, $layout ) {
	return new Slider_Layout_Composer( $layout );
} );

Marfeel_Press_App::bind( 'descriptor_layout_balcon', function ( $container, $layout ) {
	return new Balcon_Layout_Composer( $layout );
} );

Marfeel_Press_App::bind( 'descriptor_layout_rawHTML', function ( $container, $layout ) {
	return new Raw_Html_Layout_Composer( $layout );
} );

Marfeel_Press_App::bind( 'descriptor_layout_layoutRoba', function ( $container, $layout ) {
	return new Ads_Layout_Composer( $layout );
} );

Marfeel_Press_App::bind( 'descriptor_layout_photo_grid_2', function ( $container, $layout ) {
	return new Photo2_Layout_Composer( $layout );
} );

Marfeel_Press_App::bind( 'descriptor_layout', function ( $container, $layout ) {
	return new Article_Layout_Composer( $layout );
} );

Marfeel_Press_App::singleton( 'descriptor_ripper_filter', function() {
	return new Ripper_Filter();
} );

Marfeel_Press_App::bind( 'tags_slider_decorator_menu', function ( $container, $attributes ) {
	return new Tags_Slider_Menu_Decorator( $attributes );
} );

Marfeel_Press_App::singleton( 'descriptor_article_loader', function () {
	return new Marfeel_Press_Article_Loader();
} );

Marfeel_Press_App::singleton( 'item_buffer', function () {
	return new Marfeel_Item_Buffer();
} );

Marfeel_Press_App::singleton( 'inventory_loader', function () {
	return new Inventory_Loader();
} );

Marfeel_Press_App::singleton( 'marfeel_inventory_loader', function ( $container, $type ) {
	return new Marfeel_Inventory_Loader( $type );
} );

Marfeel_Press_App::singleton( 'marfeel_ads_txt_loader', function () {
	return new Marfeel_Ads_Txt_Loader();
} );

Marfeel_Press_App::singleton( 'tenant_inventory_loader', function () {
	return new File_Inventory_Loader();
} );

Marfeel_Press_App::singleton( 'inventory_finder', function () {
	return new Inventory_Finder();
} );

Marfeel_Press_App::singleton( 'inventory_renderer', function () {
	return new Inventory_Renderer();
} );

Marfeel_Press_App::singleton( 'inventory_filler', function () {
	return new Inventory_Filler();
} );

Marfeel_Press_App::singleton( 'translator', function () {
	return '\Admin\Marfeel_Press_Admin_Translator';
} );

Marfeel_Press_App::singleton( 'ai1wm_mysql', function () {
	global $wpdb;

	if ( empty( $wpdb->use_mysqli ) ) {
		return new Ai1wm_Database_Mysql( $wpdb );
	}

	return new Ai1wm_Database_Mysqli( $wpdb );
} );

Marfeel_Press_App::bind( 'ai1wm_controller', function () {
	return new Ai1wm_Main_Controller();
} );

Marfeel_Press_App::bind( 'ai1wpm_exporter_factory', function () {
	return new Mrf_Ai1wpm_Exporter_Factory();
} );

Marfeel_Press_App::singleton( 'export_preparator_tmp_posts', function () {
	return new Mrf_Export_Tmp_Posts_Related_Preparator();
} );

Marfeel_Press_App::singleton( 'export_preparator_tmp_tables', function () {
	return new Mrf_Export_Tmp_Tables_Preparator();
} );

Marfeel_Press_App::bind( 'ai1wm_export_controller', function () {
	return new Ai1wm_Export_Controller();
} );

Marfeel_Press_App::bind( 'database_wrapper', function () {
	return new Mrf_Database_Wrapper();
} );

Marfeel_Press_App::bind( 'Analytics', 'Segment' );

Marfeel_Press_App::singleton( 'tracker', function () {
	if ( Marfeel_Press_App::make( 'request_utils' )->is_local_env() ) {
		return new Marfeel_Press_Tracker_Test();
	} else {
		return new Marfeel_Press_Tracker();
	}
} );

Marfeel_Press_App::singleton( 'admin', function() {
	new Marfeel_Press_Admin();
} );

Marfeel_Press_App::singleton( 'admin_ajax', function() {
	return new Marfeel_Press_Admin_Ajax();
});

Marfeel_Press_App::singleton( 'press_admin_invalidator', function () {
	return new Marfeel_Press_Admin_Invalidator();
} );

Marfeel_Press_App::singleton( 'press_admin_utils', function () {
	return new Marfeel_Press_Admin_Utils();
} );

Marfeel_Press_App::singleton( 'mrf_insight_invalidator_service', function () {
	return new Mrf_Insight_Invalidator_Service();
} );

Marfeel_Press_App::singleton( 'availability_api', function () {
	return new Mrf_Availability_Api();
} );

Marfeel_Press_App::singleton( 'menu_api', function () {
	return new Mrf_Menu_Api();
} );

Marfeel_Press_App::singleton( 'menu_categories_api', function () {
	return new Mrf_Menu_Categories_Api();
} );

Marfeel_Press_App::singleton( 'proxy_api', function () {
	return new Mrf_Proxy_Api();
} );

Marfeel_Press_App::singleton( 'widgets_api', function () {
	return new Mrf_Widgets_Api();
} );

Marfeel_Press_App::singleton( 'plugins_api', function () {
	return new Mrf_Plugins_Api();
} );

Marfeel_Press_App::singleton( 'compatible_plugins_api', function () {
	return new Mrf_Compatible_Plugins_Api();
} );

Marfeel_Press_App::singleton( 'proxy_utils', function () {
	return new Mrf_Proxy_Utils();
} );

Marfeel_Press_App::singleton( 'default_menu_service', function () {
	return new Mrf_Default_Menu_Service();
} );

Marfeel_Press_App::singleton( 'press_settings_api', function () {
	return new Mrf_Press_Settings_API();
} );

Marfeel_Press_App::singleton( 'ads_txt_plugin_support', function () {
	return new Marfeel_Ads_Txt_Plugin_Support();
} );

Marfeel_Press_App::singleton( 'widgets_service', function () {
	return new Mrf_Widgets_Service();
} );

Marfeel_Press_App::singleton( 'plugins_service', function () {
	return new Mrf_Plugins_Service();
} );

Marfeel_Press_App::singleton( 'body_modifier', function () {
	return new Marfeel_Press_Body_Modifier();
} );

Marfeel_Press_App::singleton( 'toc_modifier', function () {
	return new Marfeel_Press_Toc_Modifier();
} );

Marfeel_Press_App::singleton( 'ct_service', function () {
	return new Marfeel_Press_Content_Type_Service();
} );

Marfeel_Press_App::singleton( 'availability_service', function () {
	return new Marfeel_Press_Availability_Service();
} );
