<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MenuCache\Tests\Integration;

use Brain\Monkey;
use Inpsyde\MenuCache\MenuCache as Testee;
use Inpsyde\MenuCache\Tests\TestCase;

/**
 * Test case for the Menu Cache class.
 *
 * @package Inpsyde\MenuCache\Tests\Integration
 * @since   1.2.0
 */
class MenuCacheTest extends TestCase {

	/**
	 * Tests if the menu (string) gets cached as expected in case the menu is to be cached.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function test_menu_key_is_generated_only_a_single_time_when_caching() {

		$menu = 'Some markup, maybe.';

		$args = $this->mock_args();

		Monkey\WP\Filters::expectApplied( Testee::FILTER_SHOULD_CACHE_MENU )
			->andReturn( true );

		$testee = new Testee();

		Monkey\WP\Filters::expectApplied( Testee::FILTER_KEY )
			->once();

		Monkey\Functions::expect( 'get_transient' );

		$testee->get_menu( $menu, $args );

		Monkey\Functions::expect( 'set_transient' );

		$testee->cache_menu( $menu, $args );
	}

	/**
	 * Tests if the menu (string) gets cached as expected in case the menu is to be cached.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function test_menu_key_is_overwritten_if_invalid_when_caching() {

		$menu = 'Some markup, maybe.';

		$args = $this->mock_args();

		$key_argument = 'menu_key_argument';
		Monkey\WP\Filters::expectApplied( Testee::FILTER_KEY_ARGUMENT )
			->andReturn( $key_argument );

		Monkey\WP\Filters::expectApplied( Testee::FILTER_SHOULD_CACHE_MENU )
			->andReturn( true );

		$testee = new Testee();

		Monkey\WP\Filters::expectApplied( Testee::FILTER_KEY )
			->twice();

		Monkey\Functions::expect( 'get_transient' );

		$testee->get_menu( $menu, $args );

		// Menu key is required to be a string, so the following is invalid and will thus be overwritten.
		$args->{$key_argument} = [ 'this', 'is', 'not', 'a', 'string' ];

		Monkey\Functions::expect( 'set_transient' );

		$testee->cache_menu( $menu, $args );

		$this->assertInternalType( 'string', $args->{$key_argument} );
	}
}
