<?php # -*- coding: utf-8 -*-

namespace Inpsyde\WPRESTStarter\Tests;

use Brain\Monkey;
use Mockery;
use PHPUnit_Framework_TestCase;

/**
 * Abstract base class for all test case implementations.
 *
 * @package Inpsyde\WPRESTStarter\Tests
 * @since   1.0.0
 */
abstract class TestCase extends PHPUnit_Framework_TestCase {

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
		Mockery::close();
		parent::tearDown();
	}
}
