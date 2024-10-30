<?php

namespace Base\Repositories;

class Posts_Meta_Repository extends Repository {

	public function get_marfeelizable( $url ) {
		$wp_postmeta = $this->db->get_postmeta_table_name();

		$result = $this->db->get_results( "
			SELECT pm2.meta_value
			FROM $wp_postmeta pm1
				INNER JOIN $wp_postmeta pm2 ON pm2.post_id = pm1.post_id
			WHERE pm1.meta_key = '_menu_item_url'
				and pm1.meta_value = '$url'
				and pm2.meta_key = 'mrf_marfeelizable'
				and pm2.meta_value = 0
		" );

		return empty( $result );
	}
}
