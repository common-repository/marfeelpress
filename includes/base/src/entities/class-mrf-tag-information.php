<?php

namespace Base\Entities;

class Mrf_Tag_Information {

	/** @var string */
	public $html;

	/**
	 * @var string
	 * @json relevantTags
	 * @jsonRemoveEmpty
	 */
	public $relevant_tags;

	/**
	 * @var array
	 * @json tagList
	 * @jsonRemoveEmpty
	 */
	public $tag_list = array();

	public function add_tag( $name, $content, $attributes = array() ) {
		$tag = array(
			'name' => $name,
		);

		if ( $content !== null ) {
			$tag['content'] = $content;
		}

		if ( ! empty( $attributes ) ) {
			$tag['attributes'] = $attributes;
		}

		$this->tag_list[] = $tag;
	}
}
