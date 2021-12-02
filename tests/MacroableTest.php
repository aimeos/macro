<?php

/**
 * @license MIT, https://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Macro;


class MacroableTest extends \PHPUnit\Framework\TestCase
{
	protected function tearDown() : void
	{
		TestA::unmacro( 'test' );
		TestB::unmacro( 'test' );
		TestC::unmacro( 'test' );
	}


	public function testCall()
	{
		$this->assertEquals( 'B', ( new TestC() )->where() );
	}


	public function testProperty()
	{
		TestA::macro( 'test', function() {
			return $this->name;
		} );

		$this->assertEquals( 'A', ( new TestC() )->where() );
	}


	public function testMacro()
	{
		TestC::macro( 'test', function() {
			return 'C';
		} );

		$this->assertEquals( 'C', ( new TestC() )->where() );
	}


	public function testMacroParent()
	{
		TestB::macro( 'test', function() {
			return 'B';
		} );

		$this->assertEquals( 'B', ( new TestC() )->where() );
	}


	public function testMacroStatic()
	{
		TestC::macro( 'test', function() {
			return 'C';
		} );

		$this->assertEquals( 'C', TestC::test() );
	}


	public function testMacroParentStatic()
	{
		TestB::macro( 'test', function() {
			return 'B';
		} );

		$this->assertEquals( 'B', TestC::test() );
	}


	public function testUnmacro()
	{
		TestC::macro( 'test', function() {
			return 'B';
		} );

		TestC::unmacro( 'test' );

		$this->assertNull( TestC::macro( 'test' ) );
	}


	public function testUnmacroParent()
	{
		TestB::macro( 'test', function() {
			return 'B';
		} );

		TestC::unmacro( 'test' );

		$this->assertNotNull( TestC::macro( 'test' ) );
	}
}


class TestA implements Iface
{
	use Macroable;

	private $name = 'A';
}


class TestB extends TestA
{
	protected function test( $arg )
	{
		return $arg;
	}
}


class TestC extends TestB
{
	public function where()
	{
		return $this->call( 'test', 'B' );
	}
}
