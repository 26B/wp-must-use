# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 0.6.0

## Added

- Adds new ACF plugin for custom improvements used by 26B.

## 0.5.0

### Changed

- Add more locks to WP update so we don't get links on live websites.
- Add more removals in `tsb-head` and `tsb-emojis` plugins.

## 0.4.0

### Changed

- Update all licenses to GPL 3.

### Added

- `tsb-revisions` plugin to handle revision related actions. Right now it contains the revisions max as a default of 10 and includes a constante to change this.

## 0.3.0

### Fixed

- Attempt to create the `mu-plugins` folder when it doesn't exist (#3).

## 0.2.0

### Added

- `tsb-disable-comments` plugin to disable comments across the site.
- `tsb-composer-autoload` support for vendor in the `wp-content` folder.

## [0.1.0] - 2025-07-31

### Added

- On install/update/remove of the plugin, the mu-plugins will be copied to, or deleted from, the `wp-content/mu-plugins` directory.
- Composer class to handle copying/deleting mu-plugins.

## [0.0.1] - 2025-07-30

### Added

- Initial release with the existing collection of mu-plugins.
