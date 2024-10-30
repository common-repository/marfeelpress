<?php

namespace API\Widgets;

use stdClass;

class Mrf_Widgets_Service {

	public function get() {
		global $wp_registered_widget_controls, $sidebars_widgets, $wp_registered_sidebars;

		return $this->compose( $sidebars_widgets, $wp_registered_sidebars, $wp_registered_widget_controls );
	}

	public function get_all_different_used_widgets() {
		$widgets_array = $this->get_used_widgets_count_array();
		return $this->transform_to_objects_array( $widgets_array );
	}

	private function get_used_widgets_count_array() {
		$widgets = array();
		$sidebars = $this->get();
		foreach ( $sidebars as $sidebar ) {
			foreach ( $sidebar->widgets as $widget ) {
				$widgets[] = $widget['class'];
			}
		}

		return array_count_values( $widgets );
	}

	private function transform_to_objects_array( $widgets_array ) {
		$widgets_objects_array = array();

		foreach ( $widgets_array as $name => $count ) {
			$widgets_objects_array[] = new Mrf_Widget_Count( $name, $count );
		}

		return $widgets_objects_array;
	}

	private function normalize_widget( $widget ) {
		$id = $widget["id"];
		$name = $widget["name"];
		$class_name = get_class( $widget["callback"][0] );
		$option_name = $widget["callback"][0]->option_name;
		$widget_number = $widget["params"][0]["number"];

		$normalized_widget = array(
			"id"          => $id,
			"name"        => $name,
			"class"       => $class_name,
			"option_name" => $option_name,
			"html"        => $this->get_widget_html( $class_name, $option_name, $widget_number ),
		);

		return $normalized_widget;
	}

	public function get_widget_html_from_id( $id ) {
		global $wp_registered_widget_controls;

		$widget = $wp_registered_widget_controls[ $id ];

		if ( isset( $widget ) ) {
			return $this->get_widget_html(
				get_class( $widget['callback'][0] ),
				'widget_' . $widget['id_base'],
				$widget['params'][0]['number']
			);
		}

		return '';
	}

	public function get_widget_html( $class_name, $option_name, $widget_number ) {
		ob_start();
		the_widget( $class_name, get_option( $option_name )[ $widget_number ] );

		return ob_get_clean();
	}

	private function compose( $widgets_per_sidebar, $sidebars_info, $widgets_info ) {
		$output = array();

		foreach ( $widgets_per_sidebar as $sidebar => $widgets ) {
			if ( array_key_exists( $sidebar, $sidebars_info ) ) {
				$sidebar_output = new stdClass();

				$output[ $sidebar ] = $sidebar_output;
				$sidebar_output->widgets = array();

				foreach ( $widgets as $widget ) {
					if ( isset( $widgets_info[ $widget ] ) ) {
						array_push( $sidebar_output->widgets, $this->normalize_widget( $widgets_info[ $widget ] ) );
					}
				}

				$sidebar_output->name = $sidebars_info[ $sidebar ]["name"];
			}
		}

		return $output;
	}
}
