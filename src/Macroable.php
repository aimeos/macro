<?php

/**
 * @license MIT, https://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Macro;


/**
 * Common trait for objects supporting macros
 */
trait Macroable
{
	private static $macros = [];


	/**
	 * Registers a custom macro that has access to the class properties if called non-static
	 *
	 * Examples:
	 *  SomeClass::macro( 'test', function( $name ) {
	 *      return $this->getConfigValue( $name ) ? true : false;
	 *  } );
	 *
	 * @param string $name Macro name
	 * @param \Closure|null $function Anonymous function
	 * @return \Closure|null Registered function or NULL if not available
	 */
	public static function macro( string $name, \Closure $function = null ) : ?\Closure
	{
		if( $function ) {
			self::$macros[static::class][$name] = $function;
		}

		foreach( array_merge( [static::class], (array) class_parents( static::class ) ) as $class )
		{
			if( isset( self::$macros[$class][$name] ) ) {
				return self::$macros[$class][$name];
			}
		}

		return null;
	}


	/**
	 * Unsets the custom macro given by its name
	 *
	 * Example:
	 *  SomeClass::unmacro( 'test' );
	 *
	 * @param string $name Macro name
	 * @return void
	 */
	public static function unmacro( string $name ) : void
	{
		unset( self::$macros[static::class][$name] );
	}


	/**
	 * Passes method calls to the custom macros without access to class properties
	 *
	 * @param string $name Macro name
	 * @param array<int,mixed> $args Macro arguments
	 * @return mixed Result or macro call
	 */
	public function __call( string $name, array $args )
	{
		if( $fcn = static::macro( $name ) ) {
			return call_user_func_array( $fcn->bindTo( $this, static::class ), $args );
		}

		$msg = 'Called unknown macro "%1$s" on class "%2$s"';
		throw new \BadMethodCallException( sprintf( $msg, $name, static::class ) );
	}


	/**
	 * Passes method calls to the custom macros with access to class properties
	 *
	 * @param string $name Macro name
	 * @param array<int,mixed> $args Macro arguments
	 * @return mixed Result or macro call
	 */
	public static function __callStatic( string $name, array $args )
	{
		if( $fcn = static::macro( $name ) ) {
			return call_user_func_array( \Closure::bind( $fcn, null, static::class ), $args );
		}

		$msg = 'Called unknown macro "%1$s" on class "%2$s"';
		throw new \BadMethodCallException( sprintf( $msg, $name, static::class ) );
	}


	/**
	 * Passes method calls to the custom macros overwriting existing methods
	 *
	 * @param string $name Macro name
	 * @param array<int,mixed> $args Macro arguments
	 * @return mixed Result or macro call
	 */
	protected function call( string $name, ...$args )
	{
		if( $fcn = static::macro( $name ) ) {
			return call_user_func_array( $fcn->bindTo( $this, self::class ), $args );
		}

		return $this->$name( ...$args );
	}
}
