<?php

namespace Base\Descriptor;

class Marfeel_Item_Buffer {

	/** @var array */
	protected $buffer;

	/** @var array */
	protected $used_ids = array();

	public function __construct() {
		$this->buffer = array();
	}

	public function add( $key, $items, $exclude_used_articles ) {
		if ( ! isset( $this->buffer[ $key ] ) ) {
			$this->buffer[ $key ] = array();
		}

		if ( $exclude_used_articles ) {
			$this->used_ids = array_merge( $this->used_ids, array_map( function( $item ) {
				return $item->id;
			}, $items ) );
		}

		$this->buffer[ $key ] = array_merge( $this->buffer[ $key ], $items );
	}

	public function get( $key ) {
		return $this->buffer[ $key ];
	}

	public function get_used_ids() {
		return array_unique( $this->used_ids );
	}

	public function get_item( $key ) {
		if ( isset( $this->buffer[ $key ] ) ) {
			return array_shift( $this->buffer[ $key ] );
		}

		return null;
	}
}
