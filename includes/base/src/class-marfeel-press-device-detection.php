<?php


namespace Base;

use Ioc\Marfeel_Press_App;
use Marfeel\Detection\MobileDetect;

class Marfeel_Press_Device_Detection {

	const VARNISH_UA_HEADER = 'HTTP_X_UA_DEVICE';
	const X_DEVICE_HEADER = 'HTTP_X_DEVICE';

	/** @var MobileDetect */
	protected $device_detector;

	public function __construct() {
		$this->device_detector = Marfeel_Press_App::make( 'mobile_detect' );
	}

	public function get_device_type() {
		if ( $this->is_from_t() ) {
			return '';
		}

		return ( $this->is_preview() || $this->is_mobile() ) ? 's' : '';
	}

	public function is_from_t() {
		return ( $this->has_from_t( $_REQUEST ) || $this->has_from_t( $_COOKIE ) );
	}

	private function has_from_t( $req ) {
		return ! ( empty( $req['fromt'] ) ) && strtoupper( $req['fromt'] ) == 'YES';
	}

	public function is_preview() {
		return false;
	}

	public function is_mobile() {
		if ( $this->is_marfeel() ) {
			return false;
		}

		if ( $this->has_varnish_header() ) {
			return $this->is_mobile_by_varnish_header();
		} elseif ( $this->has_device_header() ) {
			return $this->is_mobile_by_device_header();
		}

		return $this->device_detector->match( $this->get_user_agents() );
	}

	protected function has_varnish_header() {
		return isset( $_SERVER[ self::VARNISH_UA_HEADER ] );
	}

	protected function has_device_header() {
		return isset( $_SERVER[ self::X_DEVICE_HEADER ] );
	}

	protected function is_mobile_by_varnish_header() {
		if ( strpos( $_SERVER[ self::VARNISH_UA_HEADER ], 'mobile' ) !== false && strpos( $_SERVER[ self::VARNISH_UA_HEADER ], 'tablet' ) === false ) {
			return true;
		}
		return false;
	}

	protected function is_mobile_by_device_header() {
		return ( $_SERVER[ self::X_DEVICE_HEADER ] == 'mobile' );
	}

	private function get_user_agents() {
		// same as super cache by default
		$user_agents = Marfeel_Press_App::make( 'marfeel_user_agents' )->get_mobile_user_agents();

		$active_plugins = $this->get_active_plugins();

		if ( array_key_exists( 'litespeed-cache/litespeed-cache.php', $active_plugins ) ) {
			$litespeed_options = get_option( 'litespeed-cache-conf' );

			if ( isset( $litespeed_options['radio_select'] ) &&
				isset( $litespeed_options['mobileview_enabled'] ) &&
				isset( $litespeed_options['mobileview_rules'] ) ) {
				$user_agents = $litespeed_options['mobileview_rules'];
			}
		}

		if ( array_key_exists( 'w3-total-cache/w3-total-cache.php', $active_plugins ) && class_exists( '\W3TC\Dispatcher', false ) ) {
			$config = \W3TC\Dispatcher::config();
			$groups = $config->get_array( 'mobile.rgroups' );

			foreach ( $groups as $group => $group_config ) {
				if ( isset( $group_config['enabled'] ) && $group === 'high' ) {
					$mobile_agents = ( isset( $group_config['agents'] ) ? (array) $group_config['agents'] : '' );
					$user_agents = implode( '|', $mobile_agents );
				}
			}
		}

		if ( array_key_exists( 'wp-rocket/wp-rocket.php', $active_plugins ) ) {
			$user_agents = '^.*(2.0\ MMP|240x320|400X240|AvantGo|BlackBerry|Blazer|Cellphone|Danger|DoCoMo|Elaine/3.0|EudoraWeb|Googlebot-Mobile|hiptop|IEMobile|KYOCERA/WX310K|LG/U990|MIDP-2.|MMEF20|MOT-V|NetFront|Newt|Nintendo\ Wii|Nitro|Nokia|Opera\ Mini|Palm|PlayStation\ Portable|portalmmm|Proxinet|ProxiNet|SHARP-TQ-GX10|SHG-i900|Small|SonyEricsson|Symbian\ OS|SymbianOS|TS21i-10|UP.Browser|UP.Link|webOS|Windows\ CE|WinWAP|YahooSeeker/M1A1-R2D2|iPhone|iPod|Android|BlackBerry9530|LG-TU915\ Obigo|LGE\ VX|webOS|Nokia5800|w3c\ |w3c-|acs-|alav|alca|amoi|audi|avan|benq|bird|blac|blaz|brew|cell|cldc|cmd-|dang|doco|eric|hipt|htc_|inno|ipaq|ipod|jigs|kddi|keji|leno|lg-c|lg-d|lg-g|lge-|lg/u|maui|maxo|midp|mits|mmef|mobi|mot-|moto|mwbp|nec-|newt|noki|palm|pana|pant|phil|play|port|prox|qwap|sage|sams|sany|sch-|sec-|send|seri|sgh-|shar|sie-|siem|smal|smar|sony|sph-|symb|t-mo|teli|tim-|tosh|tsm-|upg1|upsi|vk-v|voda|wap-|wapa|wapi|wapp|wapr|webc|winw|winw|xda\ |xda-).*';
		}

		return $user_agents;
	}

	private function is_marfeel() {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) && stripos( $_SERVER['HTTP_USER_AGENT'], 'Marfeel' ) !== false;
	}

	private function get_active_plugins() {
		$active_plugins_option = get_option( 'active_plugins' );

		$active_plugins = array();
		foreach ( $active_plugins_option as $plugin ) {
			$active_plugins[ $plugin ] = true;
		}

		return $active_plugins;
	}
}
