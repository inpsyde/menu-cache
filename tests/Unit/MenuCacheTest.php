<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MenuCache\Tests\Unit;

use Brain\Monkey;
use Inpsyde\MenuCache\Tests\TestCase;
use Inpsyde\MenuCache\MenuCache as Testee;

/**
 * Test case for the Menu Cache class.
 *
 * @package Inpsyde\MenuCache\Tests\Unit
 * @since   1.0.0
 */
class MenuCacheTest extends TestCase {

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is to be cached.
	 *
	 * @since  1.0.0
	 *
	 * @covers \Inpsyde\MenuCache\MenuCache::cache_menu()
	 *
	 * @return void
	 */
	public function test_cache_menu_returns_unfiltered_menu_when_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( true );

		Monkey\Functions::expect( 'set_transient' );

		$this->assertSame( $menu, ( new Testee() )->cache_menu( $menu, $this->args_mock() ) );
	}

	/**
	 * Tests if the menu key gets adde to the menu arguments in case the menu is to be cached.
	 *
	 * @since  1.1.0
	 *
	 * @covers \Inpsyde\MenuCache\MenuCache::cache_menu()
	 *
	 * @return void
	 */
	public function test_cache_menu_adds_menu_key_to_arguments_menu_when_caching() {

		$key_argument = 'menu_key';
		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.key_argument' )
			->andReturn( $key_argument );

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( true );

		Monkey\Functions::expect( 'set_transient' );

		$args = $this->args_mock();

		( new Testee() )->cache_menu( null, $args );

		$this->assertTrue( isset( $args->{$key_argument} ) );
	}

	/**
	 * Tests if the menu (string) gets cached as expected in case the menu is to be cached.
	 *
	 * @since  1.1.0
	 *
	 * @covers \Inpsyde\MenuCache\MenuCache::cache_menu()
	 *
	 * @return void
	 */
	public function test_cache_menu_caches_menu_when_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( true );

		$key = 'menu_key';
		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.key' )
			->andReturn( $key );

		$expiration = 123;
		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.expiration' )
			->andReturn( $expiration );

		Monkey\Functions::expect( 'set_transient' )
			->withArgs( [
				$key,
				$menu,
				$expiration,
			] );

		( new Testee() )->cache_menu( $menu, $this->args_mock() );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is not to be cached.
	 *
	 * @since  1.0.0
	 *
	 * @covers \Inpsyde\MenuCache\MenuCache::cache_menu()
	 *
	 * @return void
	 */
	public function test_cache_menu_returns_unfiltered_menu_when_not_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( false );

		$this->assertSame( $menu, ( new Testee() )->cache_menu( $menu, $this->args_mock() ) );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is not to be cached.
	 *
	 * @since  1.0.0
	 *
	 * @covers \Inpsyde\MenuCache\MenuCache::get_menu()
	 *
	 * @return void
	 */
	public function test_get_menu_returns_unfiltered_menu_when_not_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( false );

		$this->assertSame( $menu, ( new Testee() )->get_menu( $menu, $this->args_mock() ) );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is to be cached but was not found.
	 *
	 * @since  1.0.0
	 *
	 * @covers \Inpsyde\MenuCache\MenuCache::get_menu()
	 *
	 * @return void
	 */
	public function test_get_menu_returns_unfiltered_menu_when_caching_and_not_found() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( true );

		Monkey\Functions::expect( 'get_transient' )
			->andReturn( false );

		$this->assertSame( $menu, ( new Testee() )->get_menu( $menu, $this->args_mock() ) );
	}

	/**
	 * Tests if the cached menu (string) gets returned in case the menu is to be cached and was found.
	 *
	 * @since  1.0.0
	 *
	 * @covers \Inpsyde\MenuCache\MenuCache::get_menu()
	 *
	 * @return void
	 */
	public function test_get_menu_returns_cached_menu_when_caching_and_found() {

		$key_argument = 'menu_key';
		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.key_argument' )
			->andReturn( $key_argument );

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( true );

		$cached_menu = 'Some cached markup, definitely.';
		Monkey\Functions::expect( 'get_transient' )
			->andReturn( $cached_menu );

		$this->assertSame( $cached_menu, ( new Testee() )->get_menu( null, $this->args_mock( [
			$key_argument => 'some_key_here',
		] ) ) );
	}

	/**
	 * Returns a menu args mock object.
	 *
	 * @param array $additional_args Optional. Additional arguments. Defaults to empty array.
	 *
	 * @return object Menu args mock object.
	 */
	private function args_mock( $additional_args = [] ) {

		return (object) array_merge( [
			'menu'           => '',
			'theme_location' => '',
		], $additional_args );
	}
}
