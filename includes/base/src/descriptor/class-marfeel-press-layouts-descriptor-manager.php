<?php

namespace Base\Descriptor;

use Base\Entities\Mrf_Model;
use Base\Entities\Layouts\Mrf_Theme_Descriptor;
use Base\Descriptor\Layout\Marfeel_Press_Layout;
use Base\Descriptor\Filter\Filter;
use Base\Descriptor\Reader\Reader;
use Ioc\Marfeel_Press_App;

class Marfeel_Press_Layouts_Descriptor_Manager {

	const MAX_ARTICLES_LIMIT = 200;

	/** @var Marfeel_Press_Layout[] */
	protected $layouts = array();

	/** @var Mrf_Theme_Descriptor */
	protected $descriptor;

	/** @var Marfeel_Press_Article_Loader */
	protected $article_loader;

	/** @var Mrf_Model */
	protected $context;

	/** @var Reader */
	protected $reader;

	/** @var Filter */
	protected $filter;

	/** @var int */
	protected $page;

	/** @var int */
	protected $max_section_articles;

	public function __construct( $context, $reader, $filter = null, $max_section_articles = 0 ) {
		$this->context = $context;
		$this->reader = $reader;
		$this->filter = $filter;
		$this->max_section_articles = $max_section_articles;
		$this->article_loader = Marfeel_Press_App::make( 'descriptor_article_loader' );
		$this->page = get_query_var( 'paged' ) ?: 1;
	}

	protected function init() {
		$this->descriptor = $this->reader->read();

		if ( $this->max_section_articles ) {
			$this->descriptor->max_articles = $this->max_section_articles;
		} elseif ( $this->descriptor->max_articles == null ) {
			$this->descriptor->max_articles = self::MAX_ARTICLES_LIMIT;
		}

		$this->build_layouts();

		$this->article_loader->load();
	}

	protected function allow_in_repetition( $layout_descriptor, $repetition ) {
		return ( $repetition == 0 || $layout_descriptor->repetition === null || $repetition <= $layout_descriptor->repetition );
	}

	protected function allow_in_page( $layout_descriptor ) {
		return ( $this->page == 1 || $layout_descriptor->page === null || $this->page <= $layout_descriptor->page );
	}

	protected function is_limit_reached( $shown_articles, $shown_section_articles ) {
		return $shown_articles >= $this->descriptor->max_articles || ( $this->max_section_articles && $shown_section_articles >= $this->max_section_articles );
	}

	protected function get_descriptor_composer( $layout_descriptor ) {
		if ( Marfeel_Press_App::offsetExists( 'descriptor_layout_' . $layout_descriptor->name ) ) {
			$layout_composer = Marfeel_Press_App::make( 'descriptor_layout_' . $layout_descriptor->name, $layout_descriptor );
		} else {
			$layout_composer = Marfeel_Press_App::make( 'descriptor_layout', $layout_descriptor );
		}

		return $layout_composer;
	}

	protected function build_layouts() {
		$current_repetition = 0;
		$total_items = 0;
		$shown_articles = 0;
		$shown_section_articles = 0;

		do {
			$added = 0;
			foreach ( $this->descriptor->layouts as $layout_descriptor ) {
				if ( $this->allow_in_page( $layout_descriptor ) && $this->allow_in_repetition( $layout_descriptor, $current_repetition ) ) {
					for ( $i = 0; $i < $layout_descriptor->count; $i++ ) {
						$layout_composer = $this->get_descriptor_composer( $layout_descriptor );
						$this->layouts[] = $layout_composer;

						$consumed_articles = $layout_composer->get_consumed_articles();
						$added += $consumed_articles;
						$shown_articles += $consumed_articles;
						$total_items += $layout_composer->get_required_articles();

						if ( $this->max_section_articles && $layout_descriptor->is_main_section ) {
							$shown_section_articles += $consumed_articles;
						}

						if ( $this->is_limit_reached( $shown_articles, $shown_section_articles ) ) {
							return;
						}
					}
				}
			}

			$current_repetition++;

			if ( $this->descriptor->max_articles === null ) {
				return;
			}
		} while ( $added > 0 );
	}

	public function get_items() {
		$this->init();

		$items = array();
		foreach ( $this->layouts as $layout ) {
			if ( $this->filter === null || $this->filter->should_add( $layout ) ) {
				$items = array_merge( $items, $layout->get_items() );
			}
		}

		return $items;
	}
}
