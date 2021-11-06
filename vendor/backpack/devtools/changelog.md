<a name="1.0.27"></a>
# [1.0.27 🌈 (1.0.27)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.27) - 27 Oct 2021

## Changes

- fix model generation auto selection @pxpm (#262)
- added SQLite requirement in the docs


[Changes][1.0.27]


<a name="1.0.26"></a>
# [1.0.26 🌈 (1.0.26)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.26) - 11 Oct 2021

## Changes

- Fix to allow working with files with syntax errors (and highlight the error) @promatik (#259)


[Changes][1.0.26]


<a name="1.0.25"></a>
# [1.0.25 🌈 (1.0.25)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.25) - 28 Sep 2021

## Changes

- Support for PostgreSQL @promatik (#261)
- Charset & collation as dependent selects @pxpm (#244)


[Changes][1.0.25]


<a name="1.0.24"></a>
# [1.0.24 🌈 (1.0.24)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.24) - 20 Sep 2021

## Changes

- [Feature] Allow using DevTools in production, but show a big fat warning sign that you shouldn't - @promatik  (#256)

[Changes][1.0.24]


<a name="1.0.23"></a>
# [1.0.23 🌈 (1.0.23)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.23) - 16 Sep 2021

## Features

- [Feature] Improved seeder error messages @promatik (#252)

### Bug Fixes

- [Bug] Factory and Seeder generated, but no longer recognized by DevTools @promatik (#258)


[Changes][1.0.23]


<a name="1.0.22"></a>
# [1.0.22 🌈 (1.0.22)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.22) - 31 Aug 2021

## Changes

- when modifiers have errors, open them so the error is visible @pxpm (#245)
- ability to generate factories and seeders for Models outside the app folder @pxpm (#247)
- installation command is now using box-drawing chars @promatik (#249)
- fix bug where AddCrudTrait button was not active in all cases where it was possible @promatik (#250)


[Changes][1.0.22]


<a name="1.0.21"></a>
# [1.0.21 🌈 (1.0.21)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.21) - 30 Aug 2021

## Changes

* Better readme installation instructions.


[Changes][1.0.21]


<a name="1.0.20"></a>
# [1.0.20 🌈 (1.0.20)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.20) - 24 Aug 2021

## Changes

* See if changelog date gets fixed automatically https://github.com/Laravel-Backpack/DevTools/issues/246


[Changes][1.0.20]


<a name="1.0.19"></a>
# [1.0.19 🌈 (1.0.19)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.19) - 24 Aug 2021

## Changes

* Added Github Action that automatically updates the `changelog.md` file from Github Releases, after a release is published. There's one big caveat - the release itself will NOT have the updated changelog file. So if you download 1.0.19, you'll be getting the code of 1.0.19 and the changelog for 1.0.18. To see what's new in 1.0.19 you'll have to see the changelog for 1.0.20 (and so on). This is due to the event order: the changelog automation can only run after a release has been published, and if that happens you can't add anything else to that tag/release, it's done. This is very unfortunate, but for now... a one-step-behind changelog is better than no changelog.

[Changes][1.0.19]


<a name="1.0.18"></a>
# [1.0.18 🌈 (1.0.18)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.18) - 24 Aug 2021

## Changes

* No changes - superficial release.


[Changes][1.0.18]


<a name="1.0.17"></a>
# [1.0.17 🌈 (1.0.17)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.17) - 24 Aug 2021

## Changes

* No changes again - superficial release to test changelog automation.


[Changes][1.0.17]


<a name="1.0.16"></a>
# [1.0.16 🌈 (1.0.16)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.16) - 24 Aug 2021

## Changes

* No changes - superficial version bump to test the changelog automation.


[Changes][1.0.16]


<a name="1.0.15"></a>
# [1.0.15 🌈 (1.0.15)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.15) - 23 Aug 2021

## Changes

- prevent version 2.0 update until we decide @pxpm (#241)
- Remove "See Files" from show page @pxpm (#239)

## 🚀 Features

- Added Install command @promatik (#240)

## 🐛 Bug Fixes

- Fixes on morphs @promatik (#204)
- Fix factory generation on blueprint 1.25 @promatik (#242)


[Changes][1.0.15]


<a name="1.0.14"></a>
# [1.0.14 🌈 (1.0.14)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.14) - 18 Aug 2021

## Changes

- Removed Blueprint temporary fix for blueprint/issues/490 @promatik (#225)
- remove manual sushi fix @pxpm (#235)
- add php_cs_fixer and workflow for it @tabacitu (#228)
- Fixed generate all @promatik (#226)


[Changes][1.0.14]


<a name="1.0.13"></a>
# [1.0.13 🌈 (1.0.13)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.13) - 13 Aug 2021

## Changes

- Bug fixes on Seed operation @promatik (#222)
- [Bug Fix] Fixed `buildFactory` for `App\\` namespace @promatik (#224)
- [Bug Fix] Another ugly fix ✌ @promatik (#223)
- use current modifier @pxpm (#214)


[Changes][1.0.13]


<a name="1.0.12"></a>
# [1.0.12 🌈 (1.0.12)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.12) - 05 Aug 2021

## Changes

* Readme images and small aesthetic improvements.


[Changes][1.0.12]


<a name="1.0.11"></a>
# [1.0.11 🌈 (1.0.11)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.11) - 05 Aug 2021

## Changes

- [Feature] Seed Operation 🎉 @promatik (#216)
- [Feature] Show related files as operation with tabs @tabacitu (#212)

## 🐛 Bug Fixes

- Temporary fix for laravel-shift/blueprint/issues/490 @promatik (#213)


[Changes][1.0.11]


<a name="1.0.10"></a>
# [1.0.10 🌈 (1.0.10)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.10) - 02 Aug 2021

## Changes

- Add validation to default modifier value based on column type @pxpm (#185)
- Models dropdown is not ordered alphabetically @promatik (#200)
- Minor fix on rememberToken column type @promatik (#205)
- Make useCurrent valid for dateTime and dateTimeTz @promatik (#207)
- Validate if CRUD, Seeders, Factories and CrutTrait can be generated @promatik (#199)
- Added missing validation on ModelRequest @promatik (#198)
- Fix sqlite attribute limit - too many SQL variables error @pxpm (#161)
- Order Models by creation date desc by default @promatik (#201)


[Changes][1.0.10]


<a name="1.0.9"></a>
# [1.0.9 🌈 (1.0.9)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.9) - 30 Jul 2021

## Changes

- Cache schema and models in `InteractsWithApplication` @tabacitu (#190)
- Fix duplicate alerts @pxpm (#196)
- validate duplicated column names @pxpm (#194)
- fix relationship model generation @pxpm (#193)


[Changes][1.0.9]


<a name="1.0.8"></a>
# [1.0.8 🌈 (1.0.8)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.8) - 29 Jul 2021

## Changes

- fix default false modifier @pxpm (#188)
- Use events to update model list (adds "+ Add Model" button to ForeignId and BelongsTo) @pxpm (#166)
- Fix add with reserved SQL words @tabacitu (#182)


[Changes][1.0.8]


<a name="1.0.7"></a>
# [1.0.7 🌈 (1.0.7)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.7) - 27 Jul 2021

## Changes

- Prepare README for launch @tabacitu (#168)
- Operation to add CrudTrait to models that don't have them @pxpm (#147)
- Added more doctrine type mapping to avoid loading errors @promatik (#178)
- Added validations for min and max for all the field types @promatik (#177)
- Fix wrong modifiers on request @promatik (#172)
- Fix request modifier generation @pxpm (#149)
- do not infer stuff if we get old session values @pxpm (#164)
- do not allow negative numbers in inputs @pxpm (#165)


[Changes][1.0.7]


<a name="1.0.6"></a>
# [1.0.6 🌈 (1.0.6)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.6) - 21 Jul 2021

## Changes

- remove lazy from input @pxpm (#156)
- fix #153 @pxpm (#154)
- fixing tests @pxpm (#152)
- polished markdown files

[Changes][1.0.6]


<a name="1.0.5"></a>
# [1.0.5 🌈 (1.0.5)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.5) - 14 Jul 2021

## Changes

- Column orders & button positioning - timestamps are now after the "Add Column" button @pxpm (#97)


[Changes][1.0.5]


<a name="1.0.4"></a>
# [1.0.4 🌈 (1.0.4)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.4) - 14 Jul 2021

## Changes

- Fix model relationship generation @pxpm (#118)
- When pressing enter not more modifiers shown. @pxpm (#137)
- Add install troubleshooting items @pxpm (#136)
- Added non recursive paths @promatik (#135)
- Fix model generation - limit selection to unsigned bigint @promatik (#126)
- Validate foreignId columns @pxpm (#131)
- fix arguments losing value when error in form @pxpm (#144)
- add schema manager to handle enum fields @pxpm (#145)


[Changes][1.0.4]


<a name="1.0.3"></a>
# [1.0.3 ⚠️⛔️ (1.0.3)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.3) - 28 Jun 2021

## Changes

- Model - Generate Seeders and Factories @promatik (#125)
- add unique and index @pxpm (#99)
- .id is ignored as id is the default @pxpm (#123)
- [UPDATED] Fix lazy inputs @pxpm (#109)

## BREAKING CHANGES
- fix #57 - paths in config file are all under one config variable @tabacitu (#127) - we know this is a breaking change but we're going to push this as a minor update anyway, because only 3 people have access to this package at this point and we all know about it; bumping the major version for this would be.... cheating;

[Changes][1.0.3]


<a name="1.0.2"></a>
# [1.0.2 🌈 (1.0.2)](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.2) - 22 Jun 2021

## Changes

- force generators version with the latest needed fix @tabacitu (#120)
- treat unloaded classes as anonymous funcs @pxpm (#116)
- Added proper support for multiple file editors @promatik (#103)
- Update readme.md with editor installation step @tabacitu (#115)
- automate belongs to relations @pxpm (#91)
- Anonymous migrations @tabacitu (#114)
- fix checkbox toggler @pxpm (#111)
- Fix non existing factory and seeder links @promatik (#107)
- Minor fixes plus php cs fix @promatik (#106)
- Minor fix on blueprint generator @promatik (#105)
- support nullable timestamps @pxpm (#117)
- use string ids to fix inode error on windows @pxpm (#102)


[Changes][1.0.2]


<a name="1.0.0"></a>
# [1.0.0](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.0) - 14 Jun 2021

First stable version 🥳

[Changes][1.0.0]


<a name="1.0.1"></a>
# [1.0.1](https://github.com/Laravel-Backpack/DevTools/releases/tag/1.0.1) - 14 Jun 2021

## Changes

- make string first @pxpm (#93)
- Fix case sensitive file links @promatik (#90)
- Add devtools tests & refactor @pxpm (#69)
- disable column types @pxpm (#72)
- Add lazy to inputs (like debouncing events, but uses the native change event) @pxpm (#71)
- Added validation for required values (enums and sets) @promatik (#70)
- Populated requests @promatik (#68)
- do not use facade @pxpm (#67)
- renamed app directory to src @tabacitu (#64)
- add relationship schema builder @pxpm (#49)
- fix Models CRUD search @tabacitu (#58)
- fix the search for migration names @pxpm (#55)
- Relationship schema improvements @tabacitu (#54)
- always run exec for composer dump-autoload @tabacitu (#53)
- Livewire component field @tabacitu (#51)
- change checkbox_toggler field to use underscore for consistency @tabacitu (#50)
- fix focus on first input @pxpm (#48)
- Ability to run and rollback specific migrations @promatik (#39)
- Migration and Model validations @promatik (#29)
- use crud operation instead of custom names @pxpm (#44)
- fix model @pxpm (#43)
- refactoring @pxpm (#42)
- rename schema builder stuff @pxpm (#33)
- add operation specific modifiers @pxpm (#41)
- hide fields with css @pxpm (#37)
- add publish modals with functionality @tabacitu (#40)
- Migration path in run @pxpm (#32)
- fix first modifier, add hide modifiers @pxpm (#28)
- fix seeders not being generated in the seeders directory wasn't present @tabacitu (#27)
- renamed PhpFile class to CustomFile @tabacitu (#23)
- Livewire component instead of Textarea when defining migrations @tabacitu (#15)
- Refactored how we work with PHP files @tabacitu (#20)
- [WIP] Add model screen @tabacitu (#5)

## 🚀 Features

- Ordered column types @promatik (#80)

## 🐛 Bug Fixes

- fix models CRUD not working when a directory like seeds/seeders is missing @tabacitu (#81)


[Changes][1.0.1]


[1.0.27]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.26...1.0.27
[1.0.26]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.25...1.0.26
[1.0.25]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.24...1.0.25
[1.0.24]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.23...1.0.24
[1.0.23]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.22...1.0.23
[1.0.22]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.21...1.0.22
[1.0.21]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.20...1.0.21
[1.0.20]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.19...1.0.20
[1.0.19]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.18...1.0.19
[1.0.18]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.17...1.0.18
[1.0.17]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.16...1.0.17
[1.0.16]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.15...1.0.16
[1.0.15]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.14...1.0.15
[1.0.14]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.13...1.0.14
[1.0.13]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.12...1.0.13
[1.0.12]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.11...1.0.12
[1.0.11]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.10...1.0.11
[1.0.10]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.9...1.0.10
[1.0.9]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.8...1.0.9
[1.0.8]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.7...1.0.8
[1.0.7]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.6...1.0.7
[1.0.6]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.0...1.0.2
[1.0.0]: https://github.com/Laravel-Backpack/DevTools/compare/1.0.1...1.0.0
[1.0.1]: https://github.com/Laravel-Backpack/DevTools/tree/1.0.1

 <!-- Generated by https://github.com/rhysd/changelog-from-release -->
