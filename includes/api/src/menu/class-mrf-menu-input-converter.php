<?php

namespace API\Menu;

use Ioc\Marfeel_Press_App;

class Mrf_Menu_Input_Converter {

	public static function convert( $menu ) {
		$output = '';

		if ( isset( $menu ) && property_exists( $menu, 'section_definitions' ) ) {
			foreach ( $menu->section_definitions as $key => $value ) {
				$has_feed_definitions = count( $value->feed_definitions ) > 0 && ! empty( $value->feed_definitions[0]->uri );
				$is_home = Mrf_Default_Menu_Service::is_home_url( $value->feed_definitions[0]->uri );

				$output .= '<input type="hidden" name="definition.sectionDefinitions[' . $key . '].name" value="' . $value->name . '" />' . PHP_EOL;
				$output .= '<input type="hidden" name="definition.sectionDefinitions[' . $key . '].title" value="' . $value->title . '" />' . PHP_EOL;

				if ( $has_feed_definitions ) {
					$output .= '<input type="hidden" name="definition.sectionDefinitions[' . $key . '].feedDefinitions[0].uri" value="' . $value->feed_definitions[0]->uri . '" />' . PHP_EOL;
				}

				if ( $value->type === 'GROUP' || ! $is_home ) {
					$output .= '<input type="hidden" name="definition.sectionDefinitions[' . $key . '].type" value="' . $value->type . '" />' . PHP_EOL;

					if ( property_exists( $value, 'level' ) ) {
						$output .= '<input type="hidden" name="definition.sectionDefinitions[' . $key . '].level" value="' . $value->level . '" />' . PHP_EOL;
					}
				}

				$output .= PHP_EOL;
			}
		}

		return $output;
	}
}
