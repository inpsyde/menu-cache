# Inpsyde Menu Cache

[![Version](https://img.shields.io/packagist/v/inpsyde/menu-cache.svg)](https://packagist.org/packages/inpsyde/menu-cache)
[![Status](https://img.shields.io/badge/status-active-brightgreen.svg)](https://github.com/inpsyde/menu-cache)
[![Build](https://img.shields.io/travis/inpsyde/menu-cache.svg)](http://travis-ci.org/inpsyde/menu-cache)
[![Downloads](https://img.shields.io/packagist/dt/inpsyde/menu-cache.svg)](https://packagist.org/packages/inpsyde/menu-cache)
[![License](https://img.shields.io/packagist/l/inpsyde/menu-cache.svg)](https://packagist.org/packages/inpsyde/menu-cache)

> Easily cache rendered menus using the Transients API.

## Introduction

The `wp_nav_menu()` function calls `_wp_menu_item_classes_by_context()` that again, depending on the context, calls `wp_get_object_terms()`, which is **not** cached, multiple times. With lots of taxonomies, terms and menu items, this can lead to a fair amount of (totally redundant) DB queries.

This plugin let's you cache rendered menus (assuming they don't have dynamic components) for re-use.

## Installation

Install with [Composer](https://getcomposer.org):

```sh
$ composer require inpsyde/menu-cache
```

### Requirements

This package requires PHP 5.4 or higher.

## Usage

Once activated, the plugin caches **all** menus, by default for **five minutes**. The menus to be cached as well as the expiration can be customized by using the appropriate filter.

### Filters

Need to customize anything? Just use the provided filters.

#### `inpsyde_menu_cache.expiration`

The `inpsyde_menu_cache.expiration` filter allows you to define the expiration for all cached menus. The default value is 300, which is 5 minutes.

**Arguments:**

- `int` `$expiration`: Expiration in seconds.

**Usage Example:**

```php
<?php

add_filter( 'inpsyde_menu_cache.expiration', function () {

	// Cache menus for 10 minutes.
	return 600;
} );
```

#### `inpsyde_menu_cache.key`

The `inpsyde_menu_cache.key` filter allows you to customize the cache key on a per-menu basis. The default value is constrcuted using a predfined prefix and the MD5 hash of the serialized (normalized) args object.

**Arguments:**

- `string` `$key`: Current key.
- `object` `$args`: Menu args.

**Usage Example:**

```php
<?php

add_filter( 'inpsyde_menu_cache.key', function ( $key, $args ) {

	// Construct the key based on the theme location only.
	return "cached_menu_{$args->theme_location}";
}, 10, 2 );
```

#### `inpsyde_menu_cache.should_cache_menu`

The `inpsyde_menu_cache.should_cache_menu` filter allows you to customize caching on a per-menu basis.

**Arguments:**

- `bool` `$key`: Whether or not the menu should be cached.
- `object` `$args`: Menu args.

**Usage Example:**

```php
<?php

add_filter( 'inpsyde_menu_cache.should_cache_menu', function ( $should_cache_menu, $args ) {

	// Cache all menus for a bunch of dynamically created theme locations.
	return 0 === strpos( $args->theme_location, 'some_perfix_here_' );
}, 10, 2 );
```

#### `inpsyde_menu_cache.theme_locations`

The `inpsyde_menu_cache.theme_locations` filter allows you to define theme locations to restrict caching menus to.

**Arguments:**

- `string|string[]` `$theme_locations`: One or more theme locations.

**Usage Example:**

```php
<?php

add_filter( 'inpsyde_menu_cache.theme_locations', function () {

	// Cache the menus for the "primary"  theme location only.
	return 'primary';
} );
```

## License

Copyright (c) 2016 Inpsyde GmbH, Thorsten Frommen, David Naber

This code is licensed under the [MIT License](LICENSE).
