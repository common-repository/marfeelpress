<?php

namespace Base\Entities;

class Mrf_Section {

	/** @var int */
	public $id;

	/** @var string */
	public $class_name = 'section';

	/** @var string */
	public $name;

	/**
	 * @var string
	 * @jsonRemove
	 */
	public $menu_name;

	/** @var string */
	public $type;

	/** @var string */
	public $title;

	/** @var string */
	public $page_title;

	/** @var string */
	public $styles;

	/** @var string */
	public $path;

	/** @var string */
	public $uri;

	/** @var string */
	public $img;

	/** @var string */
	public $icon_name;

	/** @var string */
	public $tag_information;

	/** @var string */
	public $metadata;

	/** @var string */
	public $term;

	/** @var string */
	public $state = 'DEFAULT';

	/** @var int */
	public $parent_id;

	/** @var boolean */
	public $link_new_tab;

	public function __construct() {
		$this->tag_information = new Mrf_Tag_Information();
	}

}
