<?php

namespace API\Menu;

use API\Marfeel_REST_API;
use API\Mrf_API;
use WP_REST_Response;

class Mrf_Menu_Categories_Api extends Mrf_API {
	const DEFAULT_PER_PAGE = 10;
	const DEFAULT_PAGE = 1;

	public function __construct() {
		$this->resource_name = '/menus/categories';

		$this->allowed_methods = array(
			Marfeel_REST_API::METHOD_READABLE,
		);

		$this->unsecure_endpoint();
	}

	public function get() {
		$per_page = isset( $_GET['per_page'] ) ? $_GET['per_page'] : self::DEFAULT_PER_PAGE;
		$page = isset( $_GET['page'] ) ? $_GET['page'] : self::DEFAULT_PAGE;

		$get_cat_args = array(
			'hide_empty' => false,
		);

		$all_categories = get_categories( $get_cat_args );

		$response = new WP_REST_Response( $this->get_page_categories( array_values( $all_categories ), $per_page, $page ), 200 );
		$response->header( 'X-WP-Total', sizeof( $all_categories ) );
		$response->header( 'X-WP-TotalPages', ceil( sizeof( $all_categories ) / $per_page ) );

		return $response;
	}

	private function get_page_categories( $categories, $per_page = 10, $page = 1 ) {
		$start_index = $per_page * ($page - 1);

		if ( $start_index > sizeof( $categories ) ) {
			return [];
		}

		$end_index = $per_page * $page - 1 > sizeof( $categories ) ? sizeof( $categories ) - 1 : $per_page * $page - 1;

		for ( $i = $start_index ; $i <= $end_index ; $i++ ) {
			$object = new \stdClass();
			$object->id = $categories[ $i ]->term_id;
			$object->link = get_category_link( $categories[ $i ]->term_id );
			$object->slug = $categories[ $i ]->slug;
			$object->name = $categories[ $i ]->name;
			$result[] = $object;
		}

		return $result;
	}
}
