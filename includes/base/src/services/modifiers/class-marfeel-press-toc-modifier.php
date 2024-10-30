<?php

namespace Base\Services\Modifiers;

use Wa72\HtmlPageDom\HtmlPage;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class Marfeel_Press_Toc_Modifier implements Modifier {
	const MRF_TOC = 'mrf-toc';
	const TOC_ID = '#toc_container';

	public function should_process( $body ) {
		return strpos( $body, 'id="toc_container"' ) !== false;
	}

	public function process( $body ) {
		$parser = new HtmlPage( $body );
		$toc_container = $parser->filter( self::TOC_ID );

		if ( $toc_container->count() ) {

			$this->set_common_attr( $toc_container );
			$this->process_toc( $toc_container );
		}

		return $parser->filter( 'BODY' )->html();
	}

	private function set_common_attr( $toc_container ) {
		$toc_container
			->setAttribute( 'id', self::MRF_TOC )
			->setAttribute( 'class', '' )
			->addClass( self::MRF_TOC );

		$toc_container->filter( 'ul' )->setAttribute( 'class', 'mrf-toc-list' );
		$toc_container->filter( 'li' )->setAttribute( 'class', 'mrf-toc__entry' );
		$toc_container->filter( 'a' )
			->setAttribute( 'target', '_self' )
			->setAttribute( 'class', 'mrf-toc__link' );
	}

	private function process_toc( $toc_container ) {
		$toc_container->filter( '.toc_number' )->setAttribute( 'class', 'mrf-toc__number' );
		$toc_container->filter( '.mrf-toc__link' )->each( function( $node ) {
			$number_part = explode( '</span>', $node->html() );

			$html = $number_part[0] . '</span><span class="mrf-toc__content">' . $number_part[1] . '</span>';
			$node->setInnerHtml( $html );
		} );

		$this->build_title( $toc_container, '.toc_title' );
	}

	private function build_title( $toc_container, $title_selector ) {
		$title = $toc_container->filter( $title_selector )->text();

		$new = HtmlPageCrawler::create( '<h3 class="mrf-toc__title">' . $title . '</h3>' );

		$new->replaceAll( $toc_container->filter( $title_selector ) );
	}
}
