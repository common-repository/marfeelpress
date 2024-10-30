<?php


namespace Amp\Services;

use Ioc\Marfeel_Press_App;

class Marfeel_Press_Amp_Head_Service {

	/** @var bool */
	protected $is_active;

	public function __construct( $is_active ) {
		$this->is_active = $is_active;

		add_action( 'wp_head', array( $this, 'add_amplink' ) );
	}

	public function add_amplink() {
		$amp_service = Marfeel_Press_App::make( 'amp_service' );

		$post = get_post();
		if ( $this->is_active && $amp_service->is_post_amp_active( $post ) && is_single() && ! empty( $post->post_content ) && Marfeel_Press_App::make( 'post_service' )->is_marfeelizable( $post ) ) {
			$uri_utils = Marfeel_Press_App::make( 'uri_utils' );
			$current_uri = get_permalink( $post );

			if ( ! $uri_utils->is_amp_uri( $current_uri ) ) {
				echo '<link rel="amphtml" href="' . $uri_utils->get_amp_uri( $current_uri ) . '">';
			}
		}
	}
}
