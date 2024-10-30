<?php


namespace Admin;

use Marfeel\Symfony\Component\Translation\Loader\YamlFileLoader;
use Marfeel\Symfony\Component\Translation\MessageSelector;
use Marfeel\Symfony\Component\Translation\Translator;

class Marfeel_Press_Admin_Translator {

	/** @var Translator */
	private static $translator;

	public static function add_files( $translator, $locale ) {
		$translator->addResource( 'yaml', MRFP__MARFEEL_PRESS_DIR . 'includes/admin/src/i18n/messages_admin_en.yml', 'default' );
		$translator->addResource( 'yaml', MRFP__MARFEEL_PRESS_DIR . 'includes/admin/src/i18n/messages_admin_' . $locale . '.yml', $locale );
	}

	public static function initialize() {
		$locale = explode( "_", get_locale() );
		$locale = $locale[0];

		$translator = new Translator( $locale, new MessageSelector() );

		$translator->setFallbackLocales( array( 'default' ) );
		$translator->addLoader( 'yaml', new YamlFileLoader() );

		self::add_files( $translator, $locale );

		self::$translator = $translator;
	}

	public static function trans( $message ) {
		return self::$translator->trans( $message );
	}
}

Marfeel_Press_Admin_Translator::initialize();
