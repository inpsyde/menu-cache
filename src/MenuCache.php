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
	 * Filter name.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	const FILTER_EXPIRATION = 'inpsyde_menu_cache.expiration';

	/**
	 * Filter name.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	const FILTER_KEY = 'inpsyde_menu_cache.key';

	/**
	 * Filter name.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	const FILTER_KEY_ARGUMENT = 'inpsyde_menu_cache.key_argument';

	/**
	 * Filter name.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	const FILTER_SHOULD_CACHE_MENU = 'inpsyde_menu_cache.should_cache_menu';

	/**
	 * Filter name.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	const FILTER_THEME_LOCATIONS = 'inpsyde_menu_cache.theme_locations';

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
		$this->key_argument = (string) apply_filters( self::FILTER_KEY_ARGUMENT, 'menu_key' );
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
			set_transient( $this->menu_key( $args ), $menu, $this->expiration() );
		}

		return $menu;
	}

	/**
	 * Returns a cached menu, if found.
	 *
	 * @since   1.0.0
	 * @wp-hook pre_wp_nav_menu
	 *
	 * @param string|null $menu Menu HTML, or null.
	 * @param object      $args Menu args.
	 *
	 * @return string|null Cached menu HTML, if found, or original value.
	 */
	public function get_menu( $menu, $args ) {

		if ( $this->should_cache_menu( $args ) ) {
			$cached_menu = get_transient( $this->menu_key( $args ) );
			if ( is_string( $cached_menu ) ) {
				// We have found what we need, so we don't want any code to run after us (since any changes made here would be impossible to restore)
				remove_all_filters( 'pre_wp_nav_menu' );
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
		$this->expiration = (int) apply_filters( self::FILTER_EXPIRATION, 300 );

		return $this->expiration;
	}

	/**
	 * Returns a unique key for a menu (represented by its args).
	 *
	 * @param object $args Menu args.
	 *
	 * @return string Menu key.
	 */
	private function menu_key( $args ) {

		$key_argument = $this->key_argument;

		if ( ! isset( $args->{$key_argument} ) || ! is_string( $args->{$key_argument} ) ) {
			$key = 'cached_menu_' . md5( serialize( $args ) );

			/**
			 * Filters the key of a single menu.
			 *
			 * @since 1.0.0
			 *
			 * @param string $key  Current key.
			 * @param object $args Menu args.
			 */
			$args->{$key_argument} = (string) apply_filters( self::FILTER_KEY, $key, $args );
		}

		return $args->{$key_argument};
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
		return (bool) apply_filters( self::FILTER_SHOULD_CACHE_MENU, $should_cache_menu, $args );
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
		$theme_locations = (array) apply_filters( self::FILTER_THEME_LOCATIONS, [] );

		$this->theme_locations = array_map( 'strval', $theme_locations );

		return $this->theme_locations;
	}
}
