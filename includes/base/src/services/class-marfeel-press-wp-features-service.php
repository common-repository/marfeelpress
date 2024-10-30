<?php

namespace Base\Services;

class Marfeel_Press_WP_Features_Service {

	public function disable_multipage() {

		remove_shortcode( 'nextpage', array( 'ACP_Core', 'nextpage_shortcode' ) );
		add_filter( 'the_content', array( $this, 'remove_unused_shortcodes' ) );

		add_action( 'the_post', function( $post ) {
			if ( $post != null && false !== strpos( $post->post_content, '<!--nextpage-->' ) ) {
				$GLOBALS['pages']     = [ $post->post_content ];
				$GLOBALS['numpages']  = 0;
				$GLOBALS['multipage'] = false;
			}
		}, 99 );
	}

	function remove_unused_shortcodes( $content ) {
		$pattern = $this->remove_unused_shortcodes_regex();
		$content = preg_replace_callback( '/' . $pattern . '/s', 'strip_shortcode_tag', $content );
		return $content;
	}

	function remove_unused_shortcodes_regex() {
		global $shortcode_tags;
		$tagnames = array_keys( $shortcode_tags );
		$tagregexp = join( '|', array_map( 'preg_quote', $tagnames ) );
		$regex = '\[(\[?)';
		$regex .= "(?!$tagregexp)";
		$regex .= '([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
		return $regex;
	}
}
