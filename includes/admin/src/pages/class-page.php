<?php

namespace Admin\Pages;

use Ioc\Marfeel_Press_App;
use Admin\Marfeel_Press_Admin_Translator;

abstract class Page {

	function load_content( $context ) {}

	public function load_leroy_styles() {
		$this->load_assets();

		if ( ! Marfeel_Press_App::make( 'request_utils' )->is_dev() ) {
			wp_register_style(
				'leroy',
				MRFP_ALEXANDRIA_URI . MRFP_LEROY_BUILD_NUMBER . '/css/app.css',
				array(),
				'',
				false
			);
			wp_enqueue_style( 'leroy' );
		}
	}

	public function load_assets() {
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . MRFP__MARFEEL_PRESS_ADMIN_RESOURCES_DIR . "/dist/main-admin-mrf.css?buildnumber=" . MRFP_PLUGIN_VERSION . "\" >";
	}

	public function load_page( $title, $context ) {
		$context->title = $title;

		echo '<div class="mrf-wp">';

			require_once( MRFP__MARFEEL_PRESS_ADMIN_TEMPLATES_DIR . 'components/mrf-navbar.php' );

			echo '<div class="main-content-wrapper">';
				echo '<div class="main-content w-100 pb-5">';
					echo '<div class="main-content__container">';
						$this->load_content( $context );
					echo '</div>';
				echo '</div>';
			echo '</div>';

		echo '</div>';
	}

	protected function handle_signup_redirect( $page ) {
		if ( isset( $_GET['page'] ) && $_GET['page'] == $page ) {
			$insight_token = Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.insight_token' );

			if ( empty( $insight_token ) ) {
				Marfeel_Press_App::make( 'request_utils' )->redirect( add_query_arg( array(
					'page' => 'mrf-signup',
				), admin_url( 'admin.php' ) ) );
			}
		}
	}
}
