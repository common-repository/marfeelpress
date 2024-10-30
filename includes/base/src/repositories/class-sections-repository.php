<?php

namespace Base\Repositories;

use Ioc\Marfeel_Press_App;

class Sections_Repository extends Repository {

	public function count_sections() {
		$wp_terms = $this->db->get_terms_table_name();
		$wp_term_taxonomy = $this->db->get_term_taxonomy_table_name();

		$row = $this->db->get_results( "
			SELECT COUNT(*) as categories
			FROM $wp_terms wt
				INNER JOIN $wp_term_taxonomy wtt ON wtt.term_id = wt.term_id
			WHERE wtt.taxonomy = 'category';
		" );

		return $row[0]->categories;
	}
}
