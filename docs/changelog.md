# Changelog

## v2.1.0
Ibexa 5.0 compatibility. Renamed db table names from ez to ibexa.

## v2.0.1
Minimum version is Ibexa 4.6.
* Added Matrix field Helper

## v2.0.0
Ibexa 4 support

## v1.2.2
Added field helper for
* Matrix

## v1.2.1
Added forgotten field helper for
* Selection

## v1.2.0
* Refactored some tests
* Improved the docker setup
* Switched from phpdbg to pcov for coverage
* Made the getName() method oboslete
* Support generics in `getFieldHelper()` to save type hinting in custom field helpers.
* And again more helpers:
* * Author
* * File
* * Image
* * NetgenTags

## v1.1.0
More field helpers for
* Richtext

## v1.0.1
Added field helper for
* Relation
* Url

## v1.0.0
Initial field helpers for
* Boolean
* DateTime
* Number
* Text