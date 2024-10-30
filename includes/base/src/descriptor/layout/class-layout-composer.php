<?php

namespace Base\Descriptor\Layout;

use \stdClass;
use Base\Entities\Layouts\Mrf_Layout;
use Ioc\Marfeel_Press_App;

abstract class Layout_Composer {

	/** @var Mrf_Layout */
	protected $layout;

	/** @var int */
	protected $consumed_articles = 1;

	/** @var int */
	protected static $articles_rendered = 0;

	public function __construct( Mrf_Layout $layout ) {
		$this->layout = $layout;

		Marfeel_Press_App::make( 'descriptor_article_loader' )->add_requirements(
			$this->get_required_articles(),
			$layout->section,
			$layout->params['filter'],
			$layout->attr['pocket']['exclude_used_articles']
		);
	}

	protected function get_context( $context ) {
		$context->section = $this->layout->section;
		$context->layout_options = $this->layout->options;
		$context->articles_rendered = self::$articles_rendered;
		$context->items = $this->get_items();

		if ( isset( $this->layout->attr ) ) {
			foreach ( $this->layout->attr as $key => $value ) {
				$context->$key = $value;
			}
		}

		if ( empty( $context->items ) ) {
			return null;
		}

		return $context;
	}

	public function get_section() {
		return $this->layout->section;
	}

	public function add_to_ripper() {
		return true;
	}

	public function get_items() {
		return Marfeel_Press_App::make( 'descriptor_article_loader' )->get_items( $this->get_required_articles(), $this->layout->section, $this->layout->params['filter'] );
	}

	public function get_consumed_articles() {
		return $this->consumed_articles;
	}

	public abstract function get_required_articles();
}
