<?php

/**
 * @license MIT, https://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Macro;


/**
 * Interface for objects supporting macros
 */
interface Iface
{
	/**
	 * Registers a custom macro that has access to the class properties if called non-static
	 *
	 * Example:
	 *  SomeClass::macro( 'test', function( $name ) {
	 *      return $this->getConfigValue( $name ) ? true : false;
	 *  } );
	 *
	 * @param string $name Macro name
	 * @param \Closure|null $function Anonymous function
	 * @return \Closure|null Registered function
	 */
	public static function macro( string $name, \Closure $function = null ) : ?\Closure;

	/**
	 * Unsets the custom macro given by its name
	 *
	 * Example:
	 *  SomeClass::unmacro( 'test' );
	 *
	 * @param string $name Macro name
	 */
	public static function unmacro( string $name ) : void;
}
