<?php

namespace Base\Trackers;

use Ioc\Marfeel_Press_App;

// @codingStandardsIgnoreStart WordPress.NamingConventions.ValidVariableName.NotSnakeCase
class Marfeel_Press_Tracker {

	const ACCOUNT = 'CeAIeBXqVRVRYumjvBSeiO3KEB1NKaHd';
	const OPTION_TENANT_TYPE = 'marfeel_press.tenant_type';
	const OPTION_MEDIA_GROUP = 'marfeel_press.media_group';
	const OPTION_PLUGIN_STATUS = 'marfeel_press.plugin_status';
	const OPTION_TENANT_HOME = 'tenant_home';
	const OPTION_AVAILABILITY = 'mrf_availability';
	const OPTION_ADS_TXT_STATUS = 'ads.ads_txt.status';
	const EVENTS_TO_SALESFORCE = 'step/marfeel/activate|plugin/install|marfeel/deactivated|plugin/uninstall';

	/** @var string */
	private $tenant;

	public function __construct() {
		$Analytics = Marfeel_Press_App::make( 'Analytics' );
		$settings_service = Marfeel_Press_App::make( 'settings_service' );
		$definition_service = Marfeel_Press_App::make( 'definition_service' );

		$this->tenant_type = $settings_service->get( self::OPTION_TENANT_TYPE );
		$this->media_group = $settings_service->get( self::OPTION_MEDIA_GROUP );
		$this->plugin_status = $settings_service->get( self::OPTION_PLUGIN_STATUS );
		$this->tenant_home = $definition_service->get( self::OPTION_TENANT_HOME );
		$this->adstxt_status = $settings_service->get( self::OPTION_ADS_TXT_STATUS );
		$this->availability = $settings_service->get_option_data( self::OPTION_AVAILABILITY, 'OFF' );

		$this->insight_press_audits = MRFP_INSIGHT_API . '/tenants/' . $this->tenant_home . '/definitions/index/audits/marfeelpress';
		$this->tenant = $_SERVER['HTTP_HOST'];
		$Analytics::init(self::ACCOUNT);
	}

	private function track_event( $event, $data = null ) {
		Marfeel_Press_App::make( 'log_provider' )->write_log( "Start to send track_event" ,'d' );
		$client = Marfeel_Press_App::make( 'http_client' );
		$url = MRFP_INSIGHT_API . '/marfeelpress/event/logToElastic';

		unset( $event->segment_action );
		$event->tenant = $this->tenant_home;
		$event->mediaGroup = $this->media_group;

		if ( ! empty( $data ) ) {
			$event->errorMsg = wp_json_encode( $data );
		}

		Marfeel_Press_App::make( 'log_provider' )->write_log( "Track " . $event->action . " with pluginVersion: " . $event->pluginVersion . " to " . $url,'d' );
		$client->request(
			$client::METHOD_POST,
			$url,
			array(
				'headers' => array(
					'content-type' => 'application/json',
					'mrf-secret-key' => Marfeel_Press_App::make( 'insight_service' )->get_insight_key(),
				),
				'body' => wp_json_encode( get_object_vars( $event ) ),
			)
			);
	}

	public function track( $action, $data = array() ) {
		$Analytics = Marfeel_Press_App::make( 'Analytics' );

		$Analytics::track(array(
			'userId' => $this->tenant_home,
			'event' => is_object( $action ) ? $action->segment_action : $action,
			'properties' => $data,
			'integrations' => array(
				'Salesforce' => $this->is_sendeable_to_salesforce($action),
			),
		));

		if ( is_object( $action ) ) {
			$this->track_event( $action, $data );
		}
	}

	public function track_to_insight( $type, $action ) {
		$client = Marfeel_Press_App::make( 'http_client' );
		$tracking_request = $this->build_tracking_request( $type, $action );

		$client->request(
			$client::METHOD_POST,
			$this->insight_press_audits,
			array(
				'headers' => array(
					'content-type' => 'application/json',
				),
				'body' => wp_json_encode( $tracking_request ),
			)
		);
	}

	private function build_tracking_request( $type, $action ) {
		return array(
			'type' => $type,
			'event' => $action
		);
	}

	public function identify($sendSalesForce = false, $is_blog = null) {
		$Analytics = Marfeel_Press_App::make( 'Analytics' );
		$user = wp_get_current_user();

		$identification = array(
			'userId' => $this->tenant_home,
			'traits' => array(
				'email' => $user->user_email,
				'tenant_type' => $this->tenant_type,
				'plugin_version' => MRFP_MARFEEL_PRESS_BUILD_NUMBER,
				'plugin_status' => $this->plugin_status,
				'ads_txt_status' => $this->adstxt_status,
				'mrf_active' => $this->availability,
				'company' => array(
					'name' => ! empty( $this->media_group ) ? $this->media_group : $this->tenant_home,
				),
			),
			'integrations' => array(
				'Salesforce' => $sendSalesForce,
			),
		);

		if($sendSalesForce) {
			$identification['traits']['LeadSource'] = 'Segment';
		}

		if ( $is_blog !== null ) {
			$identification['traits']['mrf_blog'] = $is_blog;
		}

		$marfeel_status = $this->get_marfeel_status($this->plugin_status, $this->availability);
		if( $marfeel_status ) {
			$identification['traits']['marfeel_status'] = $marfeel_status;
		}

		$Analytics::identify( $identification );
	}

	public function get_configuration() {
		$user = wp_get_current_user();

		return array(
			'account' => self::ACCOUNT,
			'id' => $this->tenant_home,
			'user' => array(
				'email' => $user->user_email,
				'firstName' => $user->user_firstname,
				'lastName' => $user->user_lastname,
			),
		);
	}

	public function is_sendeable_to_salesforce($event) {
		return in_array($event, explode('|', self::EVENTS_TO_SALESFORCE));
	}

	public function get_marfeel_status($plugin_status, $mrf_active) {
		$marfeel_status = false;
		if($plugin_status == 'INSTALLED' && ($mrf_active == 'OFF' || $mrf_active == 'LOGGED')) {
			$marfeel_status = 'INSTALLED';
		} elseif($plugin_status == 'INSTALLED' && $mrf_active == 'ALL') {
			$marfeel_status = 'ACTIVATED';
		} elseif($plugin_status == 'DEACTIVATED' || $plugin_status == 'UNINSTALLED') {
			$marfeel_status = $plugin_status;
		}
		return $marfeel_status;
	}
}
// @codingStandardsIgnoreEnd WordPress.NamingConventions.ValidVariableName.NotSnakeCase
