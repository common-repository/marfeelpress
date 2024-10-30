<?php

namespace Ioc;

use Illuminate\Container\Container;

// @codingStandardsIgnoreStart WordPress.NamingConventions.ValidVariableName.NotSnakeCase
class Marfeel_Press_App {

	/** @var Container */
	private static $container;

	public static function initialize() {
		self::$container = new Container();
	}

	/** @see \Illuminate\Container\Container::isAlias */
	public static function bound( $abstract ) {
		return self::$container->bound( $abstract );
	}

	/** @see \Illuminate\Container\Container::isAlias */
	public static function isAlias( $name ) {
		return self::$container->isAlias( $name );
	}

	/** @see \Illuminate\Container\Container::bind */
	public static function bind( $abstract, $concrete = null, $shared = false ) {
		self::$container->bind( $abstract, $concrete, $shared );
	}

	/** @see \Illuminate\Container\Container::bindIf */
	public static function bindIf( $abstract, $concrete = null, $shared = false ) {
		self::$container->bindIf( $abstract, $concrete, $shared );
	}

	/** @see \Illuminate\Container\Container::singleton */
	public static function singleton( $abstract, $concrete = null ) {
		self::$container->singleton( $abstract, $concrete );
	}

	/** @see \Illuminate\Container\Container::share */
	public static function share( Closure $closure ) {
		return self::$container->share( $closure );
	}

	/** @see \Illuminate\Container\Container::bindShared */
	public static function bindShared( $abstract, Closure $closure ) {
		self::$container->bindShared( $abstract, $closure );
	}

	/** @see \Illuminate\Container\Container::extend */
	public static function extend( $abstract, Closure $closure ) {
		self::$container->extend( $abstract, $closure );
	}

	/** @see \Illuminate\Container\Container::instance */
	public static function instance( $abstract, $instance ) {
		self::$container->instance( $abstract, $instance );
	}

	/** @see \Illuminate\Container\Container::alias */
	public static function alias( $abstract, $alias ) {
		self::$container->alias( $abstract, $alias );
	}

	/** @see \Illuminate\Container\Container::rebinding */
	public static function rebinding( $abstract, Closure $callback ) {
		return self::$container->rebinding( $abstract, $callback );
	}

	/** @see \Illuminate\Container\Container::refresh */
	public static function refresh( $abstract, $target, $method ) {
		return self::$container->refresh( $abstract, $target, $method );
	}

	/** @see \Illuminate\Container\Container::make */
	public static function make( $abstract, $parameters = array() ) {
		return self::$container->make( $abstract, $parameters );
	}

	/** @see \Illuminate\Container\Container::build */
	public static function build( $concrete, $parameters = array() ) {
		return self::$container->build( $concrete, $parameters );
	}

	/** @see \Illuminate\Container\Container::resolving */
	public static function resolving( $abstract, Closure $callback ) {
		self::$container->resolving( $abstract, $callback );
	}

	/** @see \Illuminate\Container\Container::resolvingAny */
	public static function resolvingAny( Closure $callback ) {
		self::$container->resolvingAny( $callback );
	}

	/** @see \Illuminate\Container\Container::isShared */
	public static function isShared( $abstract ) {
		return self::$container->isShared( $abstract );
	}

	/** @see \Illuminate\Container\Container::getBindings */
	public static function getBindings() {
		return self::$container->getBindings();
	}

	/** @see \Illuminate\Container\Container::forgetInstance */
	public static function forgetInstance( $abstract ) {
		self::$container->forgetInstance( $abstract );
	}

	/** @see \Illuminate\Container\Container::forgetInstances */
	public static function forgetInstances() {
		self::$container->forgetInstances();
	}

	/** @see \Illuminate\Container\Container::offsetExists */
	public static function offsetExists( $key ) {
		return self::$container->offsetExists( $key );
	}

	/** @see \Illuminate\Container\Container::offsetGet */
	public static function offsetGet( $key ) {
		return self::$container->offsetGet( $key );
	}

	/** @see \Illuminate\Container\Container::offsetSet */
	public static function offsetSet( $key, $value ) {
		self::$container->offsetSet( $key, $value );
	}

	/** @see \Illuminate\Container\Container::offsetUnset */
	public static function offsetUnset( $key ) {
		self::$container->offsetUnset( $key );
	}

	public static function getContainer() {
		return self::$container;
	}

	public static function setContainer( $container ) {
		self::$container = $container;
	}
}
// @codingStandardsIgnoreEnd WordPress.NamingConventions.ValidVariableName.NotSnakeCase

