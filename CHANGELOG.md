# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.3.2] - 2020-04-06

## Fixed

- Rename folder / fix namespace (PSR-4)

## [1.3.1] - 2020-03-13

## Added

- Missed config option to the file

## Changed

- Default to true enable_events config option

## [1.3.0] - 2020-03-13

## Added

- Config option for enable or disable all the package events

## Fixed

- Change trait event from Model's `dispatchesEvents` to passing callbacks methods
- Saving from a replaced Model's built-in method to another event (`saving`)

## [1.2.4] - 2020-03-10

## Fixed

- More fixes around status setter

## [1.2.3] - 2020-03-09

## Fixed

- Fixed some issues checking statuses on `setStatus(['previous' => 'new'])`

## Changed

- Minor changes to code style

## [1.2.2] - 2020-03-06

## Fixed

- Improved case sensitivity in get/set statuses
- Events names with spaces

## [1.2.1] - 2020-03-06

## Fixed

- `HasStatuses::save()` return bool

## [1.2.0] - 2020-03-05

## Added

- Compatibility with Laravel 7
- Custom model events on status `saving` and `saved` (e.g. `savingActive` & `savedActive` when save a *non active* model to active)
- EventsServiceProvider (**no need to manually add events to your app's events**)

## Changed

- **Possible breakchange!** Renamed package ServiceProvider (from _StatusServiceProvider_ to _ServiceProvider_)

## [1.1.5] - 2020-03-05

## Changed

- Minor changes and optimisations
- Change default config path for models (following Laravel's default one: `App\Model`)

## [1.1.4] - 2020-01-12

## Fixed

- Get model class in `Status::getFromEnum()` method (now using `Model::getMorphClass()`)

## [1.1.3] - 2020-01-11

## Fixed

- More trait fixes around `getMorphClass()` and the new addons

## [1.1.2] - 2020-01-11

## Added

- Custom getter in `HasStatuses` trait for Status custom model

## Fixed

- Get properly the model's morph class by using `getMorphClass()`

## [1.1.1] - 2019-12-10

## Fixed

- checkStatus array_walk to array_map
- case sensitive in setStatusAttribute

## [1.1.0] - 2019-12-10

## Added

- Use custom Status model (customisable in the config file)

## Fixed

- hasStatus didn't load relation properly

## Changed

- Changes to the config file

## [1.0.2] - 2019-12-07

## Fixed

- Variable types and names in docblocks
- Case sensitive in status attribute mutator

## [1.0.1] - 2019-12-07

### Fixed

- Missed namespace on Statusable contract

## [1.0.0] - 2019-12-07

### Added

- Package published on Packagist (composer)
