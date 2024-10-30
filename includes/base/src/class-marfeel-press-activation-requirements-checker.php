<?php

namespace Base;

use Base\Entities\Insight\Events\Plugin_Installation_Event;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Activation_Requirements_Checker {

	const MIN_REQUIRED_POSTS = 3;
	const SIGNUP_ERROR = 'signup_error';

	/** @var array */
	protected $requirements_not_met = array();

	/** @var array */
	private $incompatible_plugins_installed = array();

	public function get_wordpress_version() {
		return get_bloginfo( 'version' );
	}

	public function get_php_version() {
		return PHP_VERSION;
	}

	/**
		* Checks if the plugins requirements are satisified.
		*
		* @return bool
	*/
	public function is_requirements_met() {
		$tracker = Marfeel_Press_App::make( 'tracker' );

		if ( version_compare( $this->get_wordpress_version(), MRFP_MIN_WP_VERSION, '<' ) ) {
			$this->requirements_not_met['wp_version'] = $this->get_wordpress_version();
		}

		if ( version_compare( $this->get_php_version(), MRFP_MIN_PHP_VERSION, '<' ) ) {
			$this->requirements_not_met['php_version'] = $this->get_php_version();
		}

		$exts = array( 'xml' );

		foreach ( $exts as $ext ) {
			if ( ! extension_loaded( $ext ) ) {
				$this->requirements_not_met[ 'ext:' . $ext ] = true;
			}
		}

		if ( ! empty( $this->requirements_not_met ) ) {
			$settings_service = Marfeel_Press_App::make( 'settings_service' );

			if ( $settings_service->get( 'marfeel_press.install_error' ) < time() ) {
				$settings_service->set( 'marfeel_press.install_error', time() + DAY_IN_SECONDS );

				$tracker->track( 'plugin/installation-failed', $this->requirements_not_met );
			}

			return false;
		}

		return true;
	}

	public function check_blacklisted_plugin_active() {
		$blacklisted_plugins = array(
			'nextgen-gallery/nggallery.php',
		);

		foreach ( $blacklisted_plugins as $plugin_slug ) {
			if ( is_plugin_active( $plugin_slug ) ) {
				$this->force_disable_plugin();
				$plugin_data = get_plugin_data( plugin_dir_path( MRFP__MARFEEL_PRESS_DIR ) . $plugin_slug );
				$this->set_incompatible_plugin_installed( $plugin_data['Name'] );

				add_action( 'admin_notices', array( $this, 'show_blacklisted_plugin_notice' ) );
			}
		}
	}

	public function force_disable_plugin() {
		$plugin_name = MRFP_MARFEEL_PRESS_PLUGIN_NAME . '/' . MRFP_MARFEEL_PRESS_PLUGIN_NAME . '.php';

		if ( current_user_can( 'activate_plugins' ) && is_plugin_active( $plugin_name ) ) {
			deactivate_plugins( plugin_basename( $plugin_name ) );
			if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
			}
		}
	}

	public function mrf_deactivate_on_signup_error() {
		if ( get_transient( self::SIGNUP_ERROR ) ) {
			delete_transient( self::SIGNUP_ERROR );
			$this->show_signup_activation_error_notice();
			$this->force_disable_plugin();
		}
	}

	public function show_requirements_notice() {
		if ( isset( $this->requirements_not_met['post_type'] ) ) {
			echo '<div class="error"><p>Our plugin is suitable for news publishers and bloggers (blogs & magazines), and not with e-commerce, corporate, classifieds or custom WordPress sites. Please, if you see this message and you have a blog contact us.</p></div>';
		} else {
			echo '<div class="error"><p><strong>MarfeelPress</strong> cannot be activated due to incompatible environment.</p> <p>Check if your WordPress/PHP version met the requirements.</p></div>';
		}
	}

	public function get_requirements_not_met() {
		return $this->requirements_not_met;
	}

	public function show_blacklisted_plugin_notice() {
		echo '<div class="error"><p><strong>MarfeelPress</strong> cannot be activated due to the following plugins you have installed: ' . implode( ', ', $this->incompatible_plugins_installed ) . '</p></div>';
	}

	public function show_signup_activation_error_notice() {
		echo '<div class="error"><p><strong>MarfeelPress</strong> cannot be activated due to signup error. </p></div>';
	}

	private function set_incompatible_plugin_installed( $plugin ) {
		array_push( $this->incompatible_plugins_installed, $plugin );
	}

}
