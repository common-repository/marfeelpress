<?php

namespace Amp\Services;

class Marfeel_Press_Amp_Service {

	public function is_post_amp_active( $post ) {
		if ( ! is_admin() ) {
			$meta = $post ? get_post_meta( $post->ID, 'mrf_amp_active', true ) : null;
			return ! ( is_numeric( $meta ) && $meta == 0 );
		}
		return true;
	}
}
