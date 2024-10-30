<?php

namespace Base\Utils;

class Image_Utils {
	const IMAGES_REVERSE_PROXY_PATH = "/statics/i/p";
	const SECURE_FLAG = "s/";
	const SLASH = "/";
	const DOUBLE_SLASH = "//";
	const HTTPS = "https://";
	const HTTP = "http";
	const PROTOCOL_REGEX = "/:\\/\\//";

	public function calculate_image_ratio( $width, $height ) {
		$image_ratio = 0;

		if ( $width > 0 && $height >= 0 ) {
			$image_ratio = (float) $height / $width;
		}

		return $image_ratio;
	}

	public function get_img_media_style( $src, $background_style = null, $preload_image = false ) {

		$gradients = array(
			'vertical'   => '-webkit-linear-gradient(bottom, rgba(0,0,0,0.6) 1%, rgba(0,0,0,0.001) 60%, rgba(0,0,0,0) 100%);',
			'horizontal' => '-webkit-linear-gradient(right, rgba(0,0,0,0.12) 1%, rgba(0,0,0,0.001) 60%, rgba(0,0,0,0) 100%);',
			'full'       => '-webkit-linear-gradient(right, rgba(0,0,0,0.6) 100%, transparent 100%);',
		);

		$media_style = '';

		if ( array_key_exists( $background_style, $gradients ) ) {
			$media_style = $gradients[ $background_style ];
		}

		if ( $preload_image ) {
			$media_style = $media_style . "background-image: url(" . $src . ");";
		}

		return $media_style;
	}

	public function is_valid_image( $image_sizes, $comp_sizes, $operator, $strict = true ) {
		$width = $image_sizes['width'];
		$height = $image_sizes['height'];
		$comp_width = $comp_sizes['width'];
		$comp_height = $comp_sizes['height'];

		switch ( $operator ) {
			case "=":
				if ( ! $strict ) {
					return $width == $comp_width || $height == $comp_height;
				}
				return $width == $comp_width && $height == $comp_height;
			case "!=":
				if ( ! $strict ) {
					return $width != $comp_width || $height != $comp_height;
				}
				return $width != $comp_width && $height != $comp_height;
			case "<":
				if ( ! $strict ) {
					return $width < $comp_width || $height < $comp_height;
				}
				return $width < $comp_width && $height < $comp_height;
			case "<=":
				if ( ! $strict ) {
					return $width <= $comp_width || $height <= $comp_height;
				}
				return $width <= $comp_width && $height <= $comp_height;
			case ">":
				if ( ! $strict ) {
					return $width > $comp_width || $height > $comp_height;
				}
				return $width > $comp_width && $height > $comp_height;
			case ">=":
				if ( ! $strict ) {
					return $width >= $comp_width || $height >= $comp_height;
				}
				return $width >= $comp_width && $height >= $comp_height;
			default:
				return false;
		}
	}

	public function get_image_sizes( $value ) {
		$img = getimagesize( $value );

		return array(
			'width' => $img[0],
			'height' => $img[1],
		);
	}

	private function starts_with( $base, $wanted ) {
		$length = strlen( $wanted );

		return ( substr( $base, 0, $length ) === $wanted );
	}
}
