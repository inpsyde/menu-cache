# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

* ...

## [1.4.2] - 2019-12-10

### Fixed

* Update PHPCS related dependencies, probs @jrfnl.
* Add more tests for different php versions, probs @jrfnl.
* Switch to GPLv2+ as part of WordPress license model and our relation to the app.

## [1.4.1] - 2017-10-24

### Fixed

* Make PHP_CodeSniffer and all related packages development-only dependencies, see [#6](https://github.com/inpsyde/menu-cache/issues/6).

## [1.4.0] - 2017-09-28

### Added

* Add PHP_CodeSniffer.

### Changed

* Adapt code to PHP_CodeSniffer rules.
* Bootstrap the plugin earlier to prevent errors when referencing the constants of the not yet loaded (and loadable) class, see [#5](https://github.com/inpsyde/menu-cache/issues/5).

## [1.3.0] - 2017-07-11

### Changed

* Make menu cache lookup run quite early, props @Biont.

## [1.2.0] - 2017-05-29

### Added

* Add integration tests.

### Changed

* Make all custom filter hooks available as class constants.
* Improve unit tests.

### Fixed

* Fix both the generation and checking of the menu-specific cache key.

## [1.1.0] - 2016-12-19

### Added

* Introduce `inpsyde_menu_cache.key_argument` filter.
* Add more tests.

### Changed

* Improve handling of menu key, props dimadin.
* Improve tests.

## [1.0.0] - 2016-10-25

Initial release.

----

[Unreleased]: https://github.com/inpsyde/menu-cache/compare/v1.4.1...HEAD
[1.4.1]: https://github.com/inpsyde/menu-cache/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/inpsyde/menu-cache/compare/v1.3.0...v1.4.0
[1.3.0]: https://github.com/inpsyde/menu-cache/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/inpsyde/menu-cache/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/inpsyde/menu-cache/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/inpsyde/menu-cache/compare/v1.0.0-alpha...v1.0.0
