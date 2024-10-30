<?php

namespace Base\Repositories;

class Terms_Repository extends Repository {

	public function get_terms_by_post_ids( $ids ) {
		$wp_terms = $this->db->get_terms_table_name();
		$wp_term_taxonomy = $this->db->get_term_taxonomy_table_name();
		$wp_term_relationships = $this->db->get_term_relationships_table_name();

		return $this->db->get_results( "
			SELECT t.*, tt.*, tr.object_id
			FROM $wp_terms AS t
				INNER JOIN $wp_term_taxonomy AS tt ON t.term_id = tt.term_id
				INNER JOIN $wp_term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
			WHERE tt.taxonomy IN ('category', 'post_tag')
				AND tr.object_id IN (" . implode( ', ', $ids ) . ")
			ORDER BY t.name ASC
		" );
	}
}
