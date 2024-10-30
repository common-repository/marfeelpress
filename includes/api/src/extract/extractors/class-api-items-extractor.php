<?php

namespace API\Extract\Extractors;

use Base\Entities\Mrf_Ripper_Execution_Result;
use Ioc\Marfeel_Press_App;

abstract class Api_Items_Extractor implements Api_Extractor {

	protected abstract function get_item_hints();

	public function extract() {
		$ripper_result = new Mrf_Ripper_Execution_Result();
		$ripper_result->items = $this->get_item_hints();

		Marfeel_Press_App::make( 'head_service' )->extract_metadata( $ripper_result );

		return $ripper_result;
	}
}
