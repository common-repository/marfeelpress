<?php

namespace Base\Services\Modifiers;

interface Modifier {
	public function should_process( $body );

	public function process( $body );
}
