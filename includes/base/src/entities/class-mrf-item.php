<?php

namespace Base\Entities;

class Mrf_Item {

	/**
	 * @var int
	 */
	public $id;

	/** @var string */
	public $kicker;

	/** @var string */
	public $body;

	/**
	 * @var int
	 * @json numberOfWords
	 */
	public $number_of_words;

	/**
	 * @var int
	 * @json readingTime
	 */
	public $reading_time;

	/** @var string */
	public $summary;

	/**
	 * @var Mrf_Media[]
	 * @json detailMedia
	 * @jsonRemove
	 */
	public $detail_media;

	/** @var string */
	public $advertisement;

	/**
	 * @var string
	 * @json commentingSystem
	 */
	public $commenting_system;

	/**
	 * @var Mrf_Tag_Information
	 * @json tagInformation
	 */
	public $tag_information;

	/**
	 * @var string
	 * @json structuredData
	 */
	public $structured_data;

	/** @var string */
	public $metadatas;

	/**
	 * @var array
	 * @jsonRemoveEmpty
	 */
	public $pocket = array();

	public function __construct() {
		$this->tag_information = new Mrf_Tag_Information();
	}

	// TODO: implement it
	public function has_characteristic( $characteric ) {
		return false;
	}
}
