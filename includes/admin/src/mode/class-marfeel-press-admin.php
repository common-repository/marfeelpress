<?php

namespace Admin\Mode;

use Base\Entities\Mrf_Model;
use Base\Entities\Settings\Mrf_Tenant_Type;
use Ioc\Marfeel_Press_App;
use Admin\Pages\Settings\Mrf_Settings;
use Base\Services\Marfeel_Press_Checks_Service;

class Marfeel_Press_Admin {

	const MENU_ID = 'mrf-onboarding';

	/** @var string */
	protected $_admin_templates;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_mrf_admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'create_mrf_admin_submenu' ) );
		add_action( 'admin_bar_menu', array( $this, 'create_mrf_admin_bar_menu' ), 100 );

		add_action( 'add_meta_boxes', array( $this, 'add_marfeel_options' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		$invalidator = Marfeel_Press_App::make( 'press_admin_invalidator' );
		add_action( 'pre_post_update', array( $invalidator, 'save_post_data_pre_update' ), 10, 2 );

		add_action( 'edit_category_form_fields', array( $this, 'add_category_marfeel_options' ) );
		add_action( 'edited_category', array( $this, 'update_category_marfeelize_flag' ), 10 );

		add_action( 'wp_ajax_marfeel', array( Marfeel_Press_App::make( 'admin_ajax' ), 'handle' ) );

		add_action( 'admin_notices', array( $this, 'show_checks_alerts' ) );
		add_action( 'admin_notices', array( $this, 'show_insight_token_error' ) );

		Marfeel_Press_App::make( 'versions_service' )->init();
		Marfeel_Press_App::make( 'log_provider' )->debug_if_dev( 'marfeelPressWatcher: Marfeel_Press_Admin created' );

		Marfeel_Press_App::make( 'deactivator' )->add_deactivation_popup();
	}

	protected function add_alert( $message, $level ) {
		echo '<div class="notice notice-' . $level . ' is-dismissible">';
			echo '<p>' . trans( 'alerts.' . $message ) . '</p>';
		echo '</div>';
	}

	public function show_checks_alerts(){
		$checks = Marfeel_Press_App::make( 'settings_service' )->get_option_data( Marfeel_Press_Checks_Service::OPTION_SOFTCHECKS, null );
		$levels = array( 'error', 'warning' );

		if ( $checks && is_object( $checks ) ) {
			foreach ( $levels as $level ) {
				if ( $checks->$level ) {
					$msg_field = $level . 'Msg';
					foreach ( $checks->$msg_field as $key => $value ) {
						$this->add_alert( $key, $level );
					}
				}
			}
		}
	}

	public function show_insight_token_error() {
		$token = Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.insight_token' );

		if ( ! isset( $token ) || empty( $token ) ) {
			$class = 'notice notice-warning';

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ),  trans( 'insight-token.not-signedin' ) );
		}
	}

	public function add_category_marfeel_options( $term ) {
		$meta = get_term_meta( $term->term_id, 'no_marfeelize', true );
		$no_marfeelize = is_numeric( $meta ) && $meta == 1;

		include __DIR__ . '/../templates/post/marfeel-category-meta.php';
	}

	public function update_category_marfeelize_flag( $term_id ) {
		if ( isset( $_POST['no_marfeelize'] ) ) {
			update_term_meta( $term_id, 'no_marfeelize', 1 );
		} else {
			delete_term_meta( $term_id, 'no_marfeelize' );
		}

		Marfeel_Press_App::make( 'mrf_insight_invalidator_service' )->invalidate_all();
	}

	public function add_marfeel_options( $post_type ) {
		if ( in_array( $post_type, Marfeel_Press_App::make( 'definition_service' )->get( 'post_type' ) ) ) {
			$this->marfeel_options_post( $post_type );
		} elseif ( $post_type === 'page' ) {
			$this->marfeel_options_page();
		}
	}

	public function marfeel_options_post( $post_type ) {
		add_meta_box(
			'mrf_post_options',
			'Marfeel',
			array( $this, 'marfeel_post_meta' ),
			$post_type,
			'side'
		);
	}

	public function marfeel_options_page() {
		add_meta_box(
			'mrf_page_options',
			'Marfeel',
			array( $this, 'marfeel_post_meta' ),
			'page',
			'side'
		);
	}

	private function get_meta( $post_id, $key ) {
		$meta = get_post_meta( $post_id, $key, true );
		return is_numeric( $meta ) && $meta == 0;
	}

	private function save_meta( $post, $key, $meta_name ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post->ID, $meta_name, 0 );
		} elseif ( in_array( $post->post_type, Marfeel_Press_App::make( 'definition_service' )->get( 'post_type' ) ) || $post->post_type == 'page' ) {
			delete_post_meta( $post->ID, $meta_name );
		}
	}

	public function marfeel_post_meta( $post ) {
		$no_marfeelize = $this->get_meta( $post->ID, 'mrf_marfeelizable' );
		$amp_deactive = $this->get_meta( $post->ID, 'mrf_amp_active' );
		$no_topmedia = $this->get_meta( $post->ID, 'mrf_top_media' );

		include __DIR__ . '/../templates/post/marfeel-meta.php';
	}

	public function save_post( $post_id, $post ) {
		$this->save_meta( $post, 'mrf_no_marfeelizable', 'mrf_marfeelizable' );
		$this->save_meta( $post, 'mrf_amp_deactive', 'mrf_amp_active' );
		$this->save_meta( $post, 'mrf_top_media', 'mrf_top_media' );
	}

	public function get_admin_context() {
		$context = new Mrf_Model();
		$context->version = MRFP_LEROY_BUILD_NUMBER;
		$context->mrf_logo_src = MRFP__MARFEEL_PRESS_ADMIN_RESOURCES_DIR . 'images/marfeel_logo_rgb.svg';

		return $context;
	}

	public function create_mrf_admin_menu() {
		add_menu_page(
			null,
			'MarfeelPress',
			'manage_options',
			static::MENU_ID,
			null,
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NiA3MSIgaGVpZ2h0PSIxOHB4IiB3aWR0aD0iMTdweCI+CiAgPGRlZnM+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImEiIHgxPSI5OC44NzUlIiB4Mj0iNTAlIiB5MT0iMTMuMzczJSIgeTI9IjEwMCUiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjN2Q4Mjg3Ii8+CiAgICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzlFQTNBOCIvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICA8L2RlZnM+CiAgPGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgIDxwYXRoIGZpbGw9IiM5RUEzQTgiIGQ9Ik0zMC4zNiAyNS4zNDJsNy4wNzQgMjYuMzk4YTUuMjA2IDUuMjA2IDAgMCAxLTMuNjggNi4zNzVsLTIyLjYyOCA2LjA2M2E1LjIwNiA1LjIwNiAwIDAgMC0zLjY4IDYuMzc2TC4zNzEgNDQuMTU2YTUuMjA2IDUuMjA2IDAgMCAxIDMuNjgxLTYuMzc2bDIyLjYyNy02LjA2M2E1LjIwNiA1LjIwNiAwIDAgMCAzLjY4LTYuMzc1eiIgb3BhY2l0eT0iLjQiLz4KICAgIDxwYXRoIGZpbGw9InVybCgjYSkiIGQ9Ik01OC42NjYuMjQybDcuMDczIDI2LjM5OWE1LjIwNiA1LjIwNiAwIDAgMS0zLjY4IDYuMzc1TDM5LjQzIDM5LjA4YTUuMjA2IDUuMjA2IDAgMCAwLTMuNjggNi4zNzZsLTcuMDc0LTI2LjM5OWE1LjIwNiA1LjIwNiAwIDAgMSAzLjY4LTYuMzc1bDIyLjYyOC02LjA2M2E1LjIwNiA1LjIwNiAwIDAgMCAzLjY4LTYuMzc2eiIvPgogIDwvZz4KPC9zdmc+'
		);
	}

	public function create_mrf_admin_bar_menu( $admin_bar ) {
		$tenant_type = Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.tenant_type' );
		$menu_href = ( $tenant_type == Mrf_Tenant_Type::LONGTAIL || static::MENU_ID == 'mrf-onboarding' ) ? 'mrf-onboarding' : Mrf_Settings::PAGE_ID . '&action=' . static::MENU_ID;

		$icon_class = 'mrf-logo';
		$icon = '<style>
			#wp-admin-bar-marfeel-menu a.ab-item { display: flex; align-items: center; }
			#wp-admin-bar-marfeel-menu .mrf-logo { width: 20px; height: 20px; background-repeat: no-repeat }
			.mrf-logo { background-image: url(\'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NiA3MSIgaGVpZ2h0PSIxOHB4IiB3aWR0aD0iMTdweCI+CiAgPGRlZnM+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImEiIHgxPSI5OC44NzUlIiB4Mj0iNTAlIiB5MT0iMTMuMzczJSIgeTI9IjEwMCUiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjN2Q4Mjg3Ii8+CiAgICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzlFQTNBOCIvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICA8L2RlZnM+CiAgPGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgIDxwYXRoIGZpbGw9IiM5RUEzQTgiIGQ9Ik0zMC4zNiAyNS4zNDJsNy4wNzQgMjYuMzk4YTUuMjA2IDUuMjA2IDAgMCAxLTMuNjggNi4zNzVsLTIyLjYyOCA2LjA2M2E1LjIwNiA1LjIwNiAwIDAgMC0zLjY4IDYuMzc2TC4zNzEgNDQuMTU2YTUuMjA2IDUuMjA2IDAgMCAxIDMuNjgxLTYuMzc2bDIyLjYyNy02LjA2M2E1LjIwNiA1LjIwNiAwIDAgMCAzLjY4LTYuMzc1eiIgb3BhY2l0eT0iLjQiLz4KICAgIDxwYXRoIGZpbGw9InVybCgjYSkiIGQ9Ik01OC42NjYuMjQybDcuMDczIDI2LjM5OWE1LjIwNiA1LjIwNiAwIDAgMS0zLjY4IDYuMzc1TDM5LjQzIDM5LjA4YTUuMjA2IDUuMjA2IDAgMCAwLTMuNjggNi4zNzZsLTcuMDc0LTI2LjM5OWE1LjIwNiA1LjIwNiAwIDAgMSAzLjY4LTYuMzc1bDIyLjYyOC02LjA2M2E1LjIwNiA1LjIwNiAwIDAgMCAzLjY4LTYuMzc2eiIvPgogIDwvZz4KPC9zdmc+\') }
			.hover .mrf-logo { background-image: url(\'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NiA3MSIgaGVpZ2h0PSIxOHB4IiB3aWR0aD0iMTdweCI+CiAgPGRlZnM+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImEiIHgxPSI5OC44NzUlIiB4Mj0iNTAlIiB5MT0iMTMuMzczJSIgeTI9IjEwMCUiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjMGVhN2Q2Ii8+CiAgICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzFGQkFFOSIvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICA8L2RlZnM+CiAgPGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgIDxwYXRoIGZpbGw9IiMxRkJBRTkiIGQ9Ik0zMC4zNiAyNS4zNDJsNy4wNzQgMjYuMzk4YTUuMjA2IDUuMjA2IDAgMCAxLTMuNjggNi4zNzVsLTIyLjYyOCA2LjA2M2E1LjIwNiA1LjIwNiAwIDAgMC0zLjY4IDYuMzc2TC4zNzEgNDQuMTU2YTUuMjA2IDUuMjA2IDAgMCAxIDMuNjgxLTYuMzc2bDIyLjYyNy02LjA2M2E1LjIwNiA1LjIwNiAwIDAgMCAzLjY4LTYuMzc1eiIgb3BhY2l0eT0iLjQiLz4KICAgIDxwYXRoIGZpbGw9InVybCgjYSkiIGQ9Ik01OC42NjYuMjQybDcuMDczIDI2LjM5OWE1LjIwNiA1LjIwNiAwIDAgMS0zLjY4IDYuMzc1TDM5LjQzIDM5LjA4YTUuMjA2IDUuMjA2IDAgMCAwLTMuNjggNi4zNzZsLTcuMDc0LTI2LjM5OWE1LjIwNiA1LjIwNiAwIDAgMSAzLjY4LTYuMzc1bDIyLjYyOC02LjA2M2E1LjIwNiA1LjIwNiAwIDAgMCAzLjY4LTYuMzc2eiIvPgogIDwvZz4KPC9zdmc+\') }
			</style><span class="' . $icon_class . '"></span>';

		$admin_bar->add_menu( array(
			'id'    => 'marfeel-menu',
			'title' => $icon . 'MarfeelPress',
			'href'  => 'admin.php?page=' . $menu_href,
			'meta'  => array(
				'class' => 'menupop',
			),
		));
	}

	public function create_mrf_admin_submenu() {
		if ( Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.activated_once' ) ) {
			Marfeel_Press_App::make( 'page_start' )->add_page( $this->get_admin_context() );
			Marfeel_Press_App::make( 'page_settings' )->add_page( $this->get_admin_context() );

			if ( Mrf_Tenant_Type::is_longtail() ) {
				Marfeel_Press_App::make( 'page_account' )->add_page( $this->get_admin_context() );
			}
		} else {
			Marfeel_Press_App::make( 'page_start' )->add_page( $this->get_admin_context() );
			Marfeel_Press_App::make( 'page_settings' )->add_page( $this->get_admin_context(), false );
		}

		Marfeel_Press_App::make( 'page_signup' )->add_page( $this->get_admin_context() );
	}
}
