<?php

namespace Admin\Pages\Settings\Utils;

class Mrf_Plugin_Settings_Utils {

	public function has_been_submitted( $prop ) {
		return isset( $_POST[ $prop ] ) && $_POST[ $prop ] == 1;
	}

	public function set_success_msg( $context, $message = 'Options saved!' ) {
		$context->variant       = 'mrf-icon__check-green';
		$context->message_txt   = $message;
		$context->toast_display = 'flex';
		$context->toast_type    = 'success';
	}

	public function set_error_msg( $context, $text ) {
		$context->variant       = 'mrf-icon__alert';
		$context->message_txt   = $text;
		$context->toast_display = 'flex';
		$context->toast_type    = 'danger';
	}
}
