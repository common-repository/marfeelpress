<?php

namespace Base\Entities;

class Mrf_Item_Hint {

	/**
	 * @var string
	 * @jsonRemove
	 */
	public $class_name = 'item_hint';

	/** @var int */
	public $id;

	/** @var string */
	public $uri;

	/**
	 * @var string
	 * @jsonRemove
	 */
	public $path;

	/** @var string */
	public $title;

	/** @var string */
	public $subtitle;

	/** @var string */
	public $excerpt;

	/** @var string */
	public $author;

	/** @var \Base\Entities\Mrf_Media */
	public $media;

	/** @var string */
	public $date;

	/**
	 * @var string
	 * @jsonRemove
	 */
	public $updated_at;

	/**
	 * @var int
	 * @json firstPage
	 */
	public $first_page;

	/** @var int */
	public $pages;

	/** @var Mrf_Category[] */
	public $categories;

	/**
	 * @var Mrf_Item
	 * @json detailItem
	 */
	public $detail_item;

	/**
	 * @var string
	 * @jsonRemove
	 */
	public $headline;

	/**
	 * @var array
	 * @jsonRemove
	 */
	public $custom_extensions = array();

	/**
	 * @var boolean
	 * @json isExtractable
	 */
	public $is_extractable;

	/**
	 * @var array
	 * @jsonRemoveEmpty
	 */
	public $pocket = array();

	public function __construct() {
		$this->detail_item = new Mrf_Item();
	}
}
