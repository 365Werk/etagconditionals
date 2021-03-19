# Changelog

All notable changes to `EtagConditionals` will be documented in this file.

## Version 1.1.0
Added support for weak comparisons, so W/ can be stripped from etags
- Added support for weak comparisons within IfMatch
- Added support for weak comparisons within IfNoneMatch
- Added configurations options for both so strong comparisons can be enabled optionally
- Added tests for both IfMatch and IfNoneMatch

## Version 1.0.3
Rework of IfMatch internals + tests updated


## Version 1.0.0
Initial release 
### Added
- IfMatch Middleware 
- IfNoneMatch Middleware 
- SetEtag Middleware 
- Tests
