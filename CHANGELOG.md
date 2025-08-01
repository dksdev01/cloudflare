# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Changelog

### Changed
- PHPStan rule level to five - we want to prevent as much as possible without sacrificing developer experience
- The module now stores timestamps, rather than `DateTime` objects, in the Drupal state for API usage tracking purposes
- #3530455 - number of the cache tags per request to 100

### Fixed
- Tests
- #3352408 - code style issues
- #3538475 - issues highlighted by the CSpell
- #3136941 - licence identifier in the `composer.json`

### Removed
- Bleeding edge PHPStan configuration - we are not ready of it
- translation of log messages from the middleware - there is no much point in translating log messages, especially
  thought in the middleware
- URL generation calls from the middleware - too expensive
- Dedicated timestamper service - Drupal core time service now used instead
- Removed test code and hacks from the settings form code
- #3279425 - Composer dependency check service - not needed any more
