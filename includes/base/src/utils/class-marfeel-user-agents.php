<?php

namespace Base\Utils;

class Marfeel_User_Agents {

	const MOBILE_USER_AGENTS = '2.0 MMP|240x320|400X240|AvantGo|BlackBerry|Blazer|Cellphone|Danger|DoCoMo|Elaine\/3.0|EudoraWeb|Googlebot-Mobile|hiptop|IEMobile|KYOCERA\/WX310K|LG\/U990|MIDP-2.|MMEF20|MOT-V|NetFront|Newt|Nintendo Wii|Nitro|Nokia|Opera Mini|Palm|PlayStation Portable|portalmmm|Proxinet|ProxiNet|SHARP-TQ-GX10|SHG-i900|Small|SonyEricsson|Symbian OS|SymbianOS|TS21i-10|UP.Browser|UP.Link|webOS|Windows CE|WinWAP|YahooSeeker\/M1A1-R2D2|iPhone|iPod|Android|BlackBerry9530|LG-TU915 Obigo|LGE VX|webOS|Nokia5800|^(w3c |w3c-|acs-|alav|alca|amoi|audi|avan|benq|bird|blac|blaz|brew|cell|cldc|cmd-|dang|doco|eric|hipt|htc_|inno|ipaq|ipod|jigs|kddi|keji|leno|lg-c|lg-d|lg-g|lge-|lg\/u|maui|maxo|midp|mits|mmef|mobi|mot-|moto|mwbp|nec-|newt|noki|palm|pana|pant|phil|play|port|prox|qwap|sage|sams|sany|sch-|sec-|send|seri|sgh-|shar|sie-|siem|smal|smar|sony|sph-|symb|t-mo|teli|tim-|tosh|tsm-|upg1|upsi|vk-v|voda|wap-|wapa|wapi|wapp|wapr|webc|winw|winw|xda|xda-)';

	public function get_mobile_user_agents() {
		return self::MOBILE_USER_AGENTS;
	}

	public function get_escaped_mobile_user_agents() {
		return str_replace( '.', '\\.', str_replace( ' ', '\\ ', self::MOBILE_USER_AGENTS ) );
	}
}
