<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MenuCache\Tests\Unit;

use Brain\Monkey;
use Inpsyde\MenuCache\MenuCache as Testee;
use Inpsyde\MenuCache\Tests\TestCase;

/**
 * Test case for the Menu Cache class.
 *
 * @package Inpsyde\MenuCache\Tests\Unit
 * @since   1.0.0
 */
class MenuCacheTest extends TestCase {

	/**
	 * Tests if the menu (string) gets cached as expected in case the menu is to be cached.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_cache_menu_caches_menu_when_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( Testee::FILTER_SHOULD_CACHE_MENU )
			->andReturn( true );

		$key = 'menu_key';
		Monkey\WP\Filters::expectApplied( Testee::FILTER_KEY )
			->andReturn( $key );

		$expiration = 123;
		Monkey\WP\Filters::expectApplied( Testee::FILTER_EXPIRATION )
			->andReturn( $expiration );

		Monkey\Functions::expect( 'set_transient' )
			->withArgs( [
				$key,
				$menu,
				$expiration,
			] );

		( new Testee() )->cache_menu( $menu, $this->mock_args() );
	}

	/**
	 * Tests if the menu key gets added to the menu arguments in case the menu is to be cached.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_cache_menu_adds_menu_key_to_arguments_menu_when_caching() {

		$key_argument = 'menu_key_argument';
		Monkey\WP\Filters::expectApplied( Testee::FILTER_KEY_ARGUMENT )
			->andReturn( $key_argument );

		$args = $this->mock_args();

		Monkey\WP\Filters::expectApplied( Testee::FILTER_SHOULD_CACHE_MENU )
			->andReturn( true );

		Monkey\Functions::expect( 'set_transient' );

		$key = 'menu_key';
		Monkey\WP\Filters::expectApplied( Testee::FILTER_KEY )
			->andReturn( $key );

		( new Testee() )->cache_menu( null, $args );

		$this->assertSame( $key, $args->{$key_argument} );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is to be cached.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_cache_menu_returns_unfiltered_menu_when_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( Testee::FILTER_SHOULD_CACHE_MENU )
			->andReturn( true );

		Monkey\Functions::expect( 'set_transient' );

		$this->assertSame( $menu, ( new Testee() )->cache_menu( $menu, $this->mock_args() ) );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is not to be cached.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_cache_menu_returns_unfiltered_menu_when_not_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( Testee::FILTER_SHOULD_CACHE_MENU )
			->andReturn( false );

		Monkey\Functions::expect( 'set_transient' )
			->never();

		$this->assertSame( $menu, ( new Testee() )->cache_menu( $menu, $this->mock_args() ) );
	}

	/**
	 * Tests if the cached menu (string) gets returned in case the menu is to be cached and was found.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_menu_returns_cached_menu_when_caching_and_found() {

		$key_argument = 'menu_key_argument';
		Monkey\WP\Filters::expectApplied( Testee::FILTER_KEY_ARGUMENT )
			->andReturn( $key_argument );

		$args = $this->mock_args( [
			$key_argument => 'some_key_here',
		] );

		Monkey\WP\Filters::expectApplied( Testee::FILTER_SHOULD_CACHE_MENU )
			->andReturn( true );

		$cached_menu = 'Some cached markup, definitely.';
		Monkey\Functions::expect( 'get_transient' )
			->with( $args->{$key_argument} )
			->andReturn( $cached_menu );

		$this->assertSame( $cached_menu, ( new Testee() )->get_menu( null, $args ) );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is to be cached but was not found.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_menu_returns_unfiltered_menu_when_caching_and_not_found() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( Testee::FILTER_SHOULD_CACHE_MENU )
			->andReturn( true );

		Monkey\Functions::expect( 'get_transient' )
			->andReturn( false );

		$this->assertSame( $menu, ( new Testee() )->get_menu( $menu, $this->mock_args() ) );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is not to be cached.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_menu_returns_unfiltered_menu_when_not_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\WP\Filters::expectApplied( Testee::FILTER_SHOULD_CACHE_MENU )
			->andReturn( false );

		Monkey\Functions::expect( 'get_transient' )
			->never();

		$this->assertSame( $menu, ( new Testee() )->get_menu( $menu, $this->mock_args() ) );
	}
}
