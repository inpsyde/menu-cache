<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MenuCache;

/**
 * Main plugin class.
 *
 * @package Inpsyde\MenuCache
 * @since   1.0.0
 */
class MenuCache {

	/**
	 * @var int
	 */
	private $expiration;

	/**
	 * @var string
	 */
	private $key_argument;

	/**
	 * @var string[]
	 */
	private $theme_locations;

	/**
	 * Constructor. Sets up the properties.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		/**
		 * Filters the menu argument name used to store the menu key.
		 *
		 * @since 1.1.0
		 *
		 * @param string $key_argument The menu argument name used to store the menu key.
		 */
		$this->key_argument = (string) apply_filters( 'inpsyde_menu_cache.key_argument', 'menu_key' );
	}

	/**
	 * Stores the menu HTML in a transient.
	 *
	 * @since   1.0.0
	 * @wp-hook wp_nav_menu
	 *
	 * @param string $menu Menu HTML.
	 * @param object $args Menu args.
	 *
	 * @return string Unfiltered menu HTML.
	 */
	public function cache_menu( $menu, $args ) {

		if ( $this->should_cache_menu( $args ) ) {
			if ( ! isset( $args->{$this->key_argument} ) ) {
				$args->{$this->key_argument} = $this->get_key( $args );
			}

			set_transient( $args->{$this->key_argument}, $menu, $this->expiration() );
		}

		return $menu;
	}

	/**
	 * Returns a cached menu, if found.
	 *
	 * @since   1.0.0
	 * @wp-hook pre_wp_nav_menu
	 *
	 * @param null   $menu Unused.
	 * @param object $args Menu args.
	 *
	 * @return string|null Cached menu HTML, if found, or null.
	 */
	public function get_menu( $menu, $args ) {

		if ( $this->should_cache_menu( $args ) && isset( $args->{$this->key_argument} ) ) {
			$cached_menu = get_transient( $args->{$this->key_argument} );
			if ( is_string( $cached_menu ) ) {
				return $cached_menu;
			}
		}

		return $menu;
	}

	/**
	 * Returns the expiration.
	 *
	 * @return int Expiration.
	 */
	private function expiration() {

		if ( isset( $this->expiration ) ) {
			return $this->expiration;
		}

		/**
		 * Filters the expiration.
		 *
		 * @since 1.0.0
		 *
		 * @param int $expiration Expiration in seconds.
		 */
		$this->expiration = (int) apply_filters( 'inpsyde_menu_cache.expiration', 300 );

		return $this->expiration;
	}

	/**
	 * Returns a unique key for a menu (represented by its args).
	 *
	 * @param object $args Menu args.
	 *
	 * @return string Menu key.
	 */
	private function get_key( $args ) {

		/**
		 * Filters the key of a single menu.
		 *
		 * @since 1.0.0
		 *
		 * @param string $key  Current key.
		 * @param object $args Menu args.
		 */
		return (string) apply_filters( 'inpsyde_menu_cache.key', 'cached_menu_' . md5( serialize( $args ) ), $args );
	}

	/**
	 * Checks if a menu should be cached.
	 *
	 * @param object $args Menu args.
	 *
	 * @return bool Whether or not the menu should be cached.
	 */
	private function should_cache_menu( $args ) {

		$should_cache_menu = true;

		if ( ! empty( $args->theme_location ) ) {
			$theme_locations = $this->theme_locations();
			if ( $theme_locations ) {
				$should_cache_menu = in_array( $args->theme_location, $theme_locations, true );
			}
		}

		/**
		 * Filters the caching condition of a single menu.
		 *
		 * @since 1.0.0
		 *
		 * @param bool   $should_cache_menu Whether or not the menu should be cached.
		 * @param object $args              Menu args.
		 */
		return (bool) apply_filters( 'inpsyde_menu_cache.should_cache_menu', $should_cache_menu, $args );
	}

	/**
	 * Returns the theme locations.
	 *
	 * @return string[] Theme locations.
	 */
	private function theme_locations() {

		if ( isset( $this->theme_locations ) ) {
			return $this->theme_locations;
		}

		/**
		 * Filters the theme locations.
		 *
		 * @since 1.0.0
		 *
		 * @param string|string[] $theme_locations One or more theme locations.
		 */
		$this->theme_locations = (array) apply_filters( 'inpsyde_menu_cache.theme_locations', [] );
		$this->theme_locations = array_map( 'strval', $this->theme_locations );

		return $this->theme_locations;
	}
}
