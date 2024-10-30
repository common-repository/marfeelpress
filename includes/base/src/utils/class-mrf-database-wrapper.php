<?php


namespace Base\Utils;

use wpdb;

class Mrf_Database_Wrapper {

	/** @var wpdb */
	private $wpdb;

	public function __construct() {
		global $wpdb;

		$this->wpdb = $wpdb;
	}

	public function query( $query ) {
		return $this->wpdb->query( $query );
	}

	public function set_base_prefix( $base_prefix ) {
		$this->wpdb->base_prefix = $base_prefix;
	}

	public function get_posts_table_name() {
		return $this->wpdb->posts;
	}

	public function get_postmeta_table_name() {
		return $this->wpdb->postmeta;
	}

	public function get_terms_table_name() {
		return $this->wpdb->terms;
	}

	public function get_term_taxonomy_table_name() {
		return $this->wpdb->term_taxonomy;
	}

	public function get_term_relationships_table_name() {
		return $this->wpdb->term_relationships;
	}

	public function get_comments_table_name() {
		return $this->wpdb->comments;
	}

	public function get_commentmeta_table_name() {
		return $this->wpdb->commentmeta;
	}

	public function get_users_table_name() {
		return $this->wpdb->users;
	}

	public function get_results( $query, $output = OBJECT ) {
		return $this->wpdb->get_results( $query, $output );
	}

}
