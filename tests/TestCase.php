<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MenuCache\Tests;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Abstract base class for all test case implementations.
 *
 * @package Inpsyde\MenuCache\Tests
 * @since   1.0.0
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase {

	use MockeryPHPUnitIntegration;

	/**
	 * Prepares the test environment before each test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setUp() {

		parent::setUp();
		Monkey::setUpWP();

		Monkey\Functions::when( 'wp_json_encode' )
			->justReturn( '"some JSON string"' );
	}

	/**
	 * Cleans up the test environment after each test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function tearDown() {

		Monkey::tearDownWP();
		parent::tearDown();
	}

	/**
	 * Returns a menu args mock object.
	 *
	 * @since 1.2.0
	 *
	 * @param array $additional_args Optional. Additional arguments. Defaults to empty array.
	 *
	 * @return object Menu args mock object.
	 */
	protected function mock_args( $additional_args = [] ) {

		return (object) array_merge( [
			'menu'           => '',
			'theme_location' => '',
		], $additional_args );
	}
}
