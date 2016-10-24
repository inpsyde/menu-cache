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
	private $key_prefix = 'cached_menu_';

	/**
	 * @var string[]
	 */
	private $theme_locations;

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

		$args = $this->normalize_args( $args );

		if ( $this->should_cache_menu( $args ) ) {
			set_transient( $this->get_key( $args ), $menu, $this->expiration() );
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

		$args = $this->normalize_args( $args );

		if ( $this->should_cache_menu( $args ) ) {
			$cached_menu = get_transient( $this->get_key( $args ) );
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
		 * @param string $key  Current key.
		 * @param object $args Menu args.
		 */
		return (string) apply_filters( 'inpsyde_menu_cache.key', $this->key_prefix . md5( serialize( $args ) ), $args );
	}

	/**
	 * Returns a normalized version of the passed menu args object.
	 *
	 * @param object $args Menu args.
	 *
	 * @return object Normalized menu args.
	 */
	private function normalize_args( $args ) {

		$menu = wp_get_nav_menu_object( $args->menu );

		if ( ! $menu && $args->theme_location ) {
			$locations = get_nav_menu_locations();
			if ( isset( $locations[ $args->theme_location ] ) ) {
				$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
			}
		}

		if ( ! $menu && ! $args->theme_location ) {
			foreach ( wp_get_nav_menus() as $menu_maybe ) {
				if ( wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
					$menu = $menu_maybe;
					break;
				}
			}
		}

		if ( empty( $args->menu ) ) {
			$args->menu = $menu;
		}

		return $args;
	}

	/**
	 * Returns a unique key for a menu (represented by its args).
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
		 * @param bool   $should_cache_menu Whether or not the menu shold be cached.
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
		 * @param string|string[] $theme_locations One or more theme locations.
		 */
		$this->theme_locations = (array) apply_filters( 'inpsyde_menu_cache.theme_locations', [] );
		$this->theme_locations = array_map( 'strval', $this->theme_locations );

		return $this->theme_locations;
	}
}
