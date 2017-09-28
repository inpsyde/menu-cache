# Inpsyde Menu Cache

[![Version](https://img.shields.io/packagist/v/inpsyde/menu-cache.svg)](https://packagist.org/packages/inpsyde/menu-cache)
[![Status](https://img.shields.io/badge/status-active-brightgreen.svg)](https://github.com/inpsyde/menu-cache)
[![Build](https://img.shields.io/travis/inpsyde/menu-cache.svg)](http://travis-ci.org/inpsyde/menu-cache)
[![Downloads](https://img.shields.io/packagist/dt/inpsyde/menu-cache.svg)](https://packagist.org/packages/inpsyde/menu-cache)
[![License](https://img.shields.io/packagist/l/inpsyde/menu-cache.svg)](https://packagist.org/packages/inpsyde/menu-cache)

> Easily cache rendered menus using the Transients API.

## Introduction

The `wp_nav_menu()` function calls `_wp_menu_item_classes_by_context()`, which again, depending on the context, calls `wp_get_object_terms()`, which is **not** cached, multiple times.
With lots of taxonomies, terms and menu items, this can lead to a fair amount of (totally redundant) database queries.

This plugin lets you cache rendered menus (assuming they don't have dynamic components) for re-use.

## Installation

Install with [Composer](https://getcomposer.org):

```sh
$ composer require inpsyde/menu-cache
```

### Requirements

This package requires PHP 5.4 or higher.

## Usage

Once activated, the plugin caches **all** menus, by default for **five minutes**.
The menus to be cached, as well as the expiration, can be customized by using the appropriate filter.

### Filters

Need to customize anything?
Just use the provided filters.

**Please note:** when you use the below class constants for the filters, make sure that the class is actually available.
This can be as easy as _guarding_ your customization with `if ( class_exists( 'Inpsyde\MenuCache\MenuCache' ) )`.

#### `Inpsyde\MenuCache\MenuCache::FILTER_EXPIRATION`

The `Inpsyde\MenuCache\MenuCache::FILTER_EXPIRATION` filter allows you to define the expiration for all cached menus.
The default value is 300, which is 5 minutes.

**Arguments:**

- `int` `$expiration`: Expiration in seconds.

**Usage Example:**

```php
<?php

add_filter( \Inpsyde\MenuCache\MenuCache::FILTER_EXPIRATION, function () {

	// Cache menus for 10 minutes.
	return 600;
} );
```

#### `Inpsyde\MenuCache\MenuCache::FILTER_KEY`

The `Inpsyde\MenuCache\MenuCache::FILTER_KEY` filter allows you to customize the cache key on a per-menu basis.
The default value is constructed using a predfined prefix and the MD5 hash of the serialized args object.

**Arguments:**

- `string` `$key`: Current key.
- `object` `$args`: Menu args.

**Usage Example:**

```php
<?php

add_filter( \Inpsyde\MenuCache\MenuCache::FILTER_KEY, function ( $key, $args ) {

	// Construct the key based on the theme location only.
	return "cached_menu_{$args->theme_location}";
}, 10, 2 );
```

#### `Inpsyde\MenuCache\MenuCache::FILTER_KEY_ARGUMENT`

The `Inpsyde\MenuCache\MenuCache::FILTER_KEY_ARGUMENT` filter allows you to customize the menu argument name that is used to store the menu key (for later look-up).

**Arguments:**

- `string` `$key_argument`: Current key argument name.

**Usage Example:**

```php
<?php

add_filter( \Inpsyde\MenuCache\MenuCache::FILTER_KEY_ARGUMENT, function () {

	// Use argument name with a leading underscore.
	return '_menu_key';
} );
```

#### `Inpsyde\MenuCache\MenuCache::FILTER_SHOULD_CACHE_MENU`

The `Inpsyde\MenuCache\MenuCache::FILTER_SHOULD_CACHE_MENU` filter allows you to customize caching on a per-menu basis.

**Arguments:**

- `bool` `$key`: Whether or not the menu should be cached.
- `object` `$args`: Menu args.

**Usage Example:**

```php
<?php

add_filter( \Inpsyde\MenuCache\MenuCache::FILTER_SHOULD_CACHE_MENU, function ( $should_cache_menu, $args ) {

	// Cache all menus for a bunch of dynamically created theme locations.
	return 0 === strpos( $args->theme_location, 'some_prefix_here_' );
}, 10, 2 );
```

#### `Inpsyde\MenuCache\MenuCache::FILTER_THEME_LOCATIONS`

The `Inpsyde\MenuCache\MenuCache::FILTER_THEME_LOCATIONS` filter allows you to define theme locations to restrict caching menus to.

**Arguments:**

- `string|string[]` `$theme_locations`: One or more theme locations.

**Usage Example:**

```php
<?php

add_filter( \Inpsyde\MenuCache\MenuCache::FILTER_THEME_LOCATIONS, function () {

	// Cache the menus for the "primary" theme location only.
	return 'primary';
} );
```

## License

Copyright (c) 2017 Inpsyde GmbH, Thorsten Frommen, David Naber

This code is licensed under the [MIT License](LICENSE).
