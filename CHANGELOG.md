# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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