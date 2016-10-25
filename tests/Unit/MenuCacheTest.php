<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MenuCache\Tests\Unit;

use Brain\Monkey;
use Inpsyde\MenuCache\Tests\TestCase;
use Inpsyde\MenuCache\MenuCache as Testee;
use Mockery;

/**
 * Test case for the Menu Cache class.
 *
 * @package Inpsyde\MenuCache\Tests\Unit
 * @since   1.0.0
 */
class MenuCacheTest extends TestCase {

	/**
	 * @var object
	 */
	private $args_mock;

	/**
	 * Prepares the test environment before each test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setUp() {

		parent::setUp();

		$this->args_mock = (object) [
			'menu'           => '',
			'theme_location' => '',
		];
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is to be cached.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\MenuCache\MenuCache::cache_menu()
	 *
	 * @return void
	 */
	public function test_cache_menu_returns_unfiltered_menu_when_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\Functions::when( 'wp_get_nav_menu_object' )->justReturn();
		Monkey\Functions::when( 'get_nav_menu_locations' )->justReturn();
		Monkey\Functions::when( 'wp_get_nav_menus' )->justReturn( [] );

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( true );

		Monkey\Functions::expect( 'set_transient' )
			->once()
			->withArgs( [
				Mockery::type( 'string' ),
				$menu,
				Mockery::type( 'int' ),
			] );

		$this->assertSame( $menu, ( new Testee() )->cache_menu( $menu, $this->args_mock ) );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is not to be cached.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\MenuCache\MenuCache::cache_menu()
	 *
	 * @return void
	 */
	public function test_cache_menu_returns_unfiltered_menu_when_not_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\Functions::when( 'wp_get_nav_menu_object' )->justReturn();
		Monkey\Functions::when( 'get_nav_menu_locations' )->justReturn();
		Monkey\Functions::when( 'wp_get_nav_menus' )->justReturn( [] );

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( false );

		$this->assertSame( $menu, ( new Testee() )->cache_menu( $menu, $this->args_mock ) );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is not to be cached.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\MenuCache\MenuCache::get_menu()
	 *
	 * @return void
	 */
	public function test_get_menu_returns_unfiltered_menu_when_not_caching() {

		$menu = 'Some markup, maybe.';

		Monkey\Functions::when( 'wp_get_nav_menu_object' )->justReturn();
		Monkey\Functions::when( 'get_nav_menu_locations' )->justReturn();
		Monkey\Functions::when( 'wp_get_nav_menus' )->justReturn( [] );

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( false );

		$this->assertSame( $menu, ( new Testee() )->get_menu( $menu, $this->args_mock ) );
	}

	/**
	 * Tests if the passed menu (string) gets returned as is in case the menu is to be cached but was not found.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\MenuCache\MenuCache::get_menu()
	 *
	 * @return void
	 */
	public function test_get_menu_returns_unfiltered_menu_when_caching_and_not_found() {

		$menu = 'Some markup, maybe.';

		Monkey\Functions::when( 'wp_get_nav_menu_object' )->justReturn();
		Monkey\Functions::when( 'get_nav_menu_locations' )->justReturn();
		Monkey\Functions::when( 'wp_get_nav_menus' )->justReturn( [] );

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( true );

		Monkey\Functions::expect( 'get_transient' )
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturn( false );

		$this->assertSame( $menu, ( new Testee() )->get_menu( $menu, $this->args_mock ) );
	}

	/**
	 * Tests if the cached menu (string) gets returned in case the menu is to be cached and was found.
	 *
	 * @since  1.0.0
	 *
	 * @covers Inpsyde\MenuCache\MenuCache::get_menu()
	 *
	 * @return void
	 */
	public function test_get_menu_returns_cached_menu_when_caching_and_found() {

		Monkey\Functions::when( 'wp_get_nav_menu_object' )->justReturn();
		Monkey\Functions::when( 'get_nav_menu_locations' )->justReturn();
		Monkey\Functions::when( 'wp_get_nav_menus' )->justReturn( [] );

		Monkey\WP\Filters::expectApplied( 'inpsyde_menu_cache.should_cache_menu' )
			->andReturn( true );

		$cached_menu = 'Some cached markup, definitely.';

		Monkey\Functions::expect( 'get_transient' )
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturn( $cached_menu );

		$this->assertSame( $cached_menu, ( new Testee() )->get_menu( 'Some markup, maybe.', $this->args_mock ) );
	}
}
