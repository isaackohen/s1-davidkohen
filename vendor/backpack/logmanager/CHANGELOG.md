# Changelog

All Notable changes to `LogManager` will be documented in this file

---
IMPORTANT
---

After version 4.0.0 we no longer use this file to track changes. Please see the Releases tab:
https://github.com/Laravel-Backpack/LogManager/releases

---

## [4.0.0] - 2020-05-06

### Added
- support for Backpack 4.1;

### Removed
- support for Backpack 4.0 (just because we have ```la la-icon``` instead of ```fa fa-icon```);

--------------


## [3.0.5] - 2020-04-20

### Fixed
- added support for Russian language @shbov (#38)
- added names to routes @iMokhles (#37)


## [3.0.4] - 2020-03-05

### Fixed
- upgraded PHPUnit;


## [3.0.3] - 2020-01-14

### Added
- merged #35 - Indonesian language file;


## [3.0.2] - 2020-01-03

### Fixed
- merged #34 - notification bubbles were using Backpack v3 syntax so they didn't show;


## [3.0.1] - 2019-12-19

### Added
- Farsi (Persian) language file;


## [3.0.0] - 2019-09-24

### Added
- Backpack v4 support;

### Removed
- Backpack v3 support;


--------------

## [2.3.27] - 2019-09-04

### Added
- Laravel 6 support;


## [2.3.26] - 2019-04-11

- changed filename encryption & decryption to Laravel's default AES-256-CBC encryption instead of Base64, so that malicious admins cannot possibly download private files;


## [2.3.25] - 2019-02-27

- added support for Base 1.1.x and dev-upgrade, so that it can be installed on Laravel 5.8;

## [2.3.24] - 2018-11-22

- added support for Base 1.0.x

## [2.3.23] - 2018-10-15

### Fixed
- #27 - only load custom views if the directory exists - this makes the ```php artisan view:cache``` command work again;


## [2.3.22] - 2018-10-15

### Added
- log files are now ordered by date modified, descending;
- mentioned the better logging package in the readme file;


## [2.3.21] - 2018-09-05

### Fixed
- in routes, replaced ```backpack_middleware()``` call with its result, to prevent errors when helpers are NOT loaded before routes, and ```php artisan package:discover``` can't execute;


## [2.3.20] - 2018-05-02

### Removed
- custom filesystem disk for this package; since we're using Backpack\Base 0.9.x we can use the root filesystem disk that Base is using; this way, installation has a lot fewer steps;


## [2.3.19] - 2018-05-02

### Fixed
- ```backpack_middleware()``` is automatically added to the routes;

### Removed
- support for Backpack\Base version below 0.9;


## [2.3.18] - 2018-03-13

## Added
- better preview, thanks to [Shamar Kellman](https://github.com/ShamarKellman)'s PR #21;


## [2.3.17] - 2018-03-13

## Added
- spanish translations;


## [2.3.16] - 2017-08-30

## Added
- package auto-discovery for Laravel 5.5 apps;


## [2.3.15] - 2017-08-11

## Added
- Danish (da_DK) language files, thanks to [Frederik Rabøl](https://github.com/Xayer);


## [2.3.14] - 2017-07-10

### Added
- Romanian language file;


## [2.3.13] - 2017-07-06

### Added
- overwritable routes file;


## [2.3.12] - 2017-07-05

### Added
- Portugese translation (thanks to [Toni Almeida](https://github.com/promatik));
- Portugese (Brasilian) translation (thanks to [Guilherme Augusto Henschel](https://github.com/cenoura));


## [2.3.11] - 2017-04-21

### Removed
- Backpack\CRUD no longer loads translations, as Backpack\Base does it for him.


## [2.3.10] - 2017-04-05

### Added
- Dutch translation (thanks to [Mark van Beek](https://github.com/chancezeus));

### Fixed
- App namespace issue;


## [2.3.9] - 2017-01-21

### Added
- Ability to publish views;
- Ability to overwrite views the same way you overwrite views in CRUD;


## [2.3.8] - 2016-09-23

### Fixed
- Breadcrumbs and routes now follow the route_prefix set in the config/backpack/base.php file- thanks to [Twaambo Haamucenje](https://github.com/twoSeats);
- Breadcrumb first item now shows the project name correctly;


## [2.3.7] - 2016-09-22

### Added
- French translations - thanks to [7ute](https://github.com/7ute);



## [2.3.6] - 2016-07-31

### Added
- Bogus unit tests. At least we'be able to use travis-ci for requirements errors, until full unit tests are done.


## [2.3.5] - 2016-06-02

### Fixed
- Using the Admin middleware instead of Auth, as of Backpack\Base v0.6.0;
- Moved routes definition to LogManagerServiceProvider instead of routes.php file;


## [2.3.4] - 2016-03-16

### Fixed
- Added page title.


## [2.3.3] - 2016-03-12

### Fixed
- Lang files are pushed in the correct folder now. For realsies.


## 2.3.2 - 2016-03-12

### Fixed
- language files are published in the correct folder, no /vendor/ subfolder


## 2.3.1 - 2016-03-10

### Fixed
- In views, switched to one header section instead of separate sections for page title and breadcrumbs.


## 2.3.0 - 2016-03-10

### Added
- Working notification bubbles (alerts).

## 2.2.1 - 2016-03-10

### Fixed
- Changed BackPack\Base requirement in composer.json to ^0.2


## 2.2.0 - 2016-03-04

### Fixed
- Changed base layout usage to work with BackPack\Base 0.4.x


## 2.1.1 - 2016-03-04

### Fixed
- "The log file doesn't exist." error message wasn't in the lang files. Now it is.


## 2.1.0 - STABLE - 2016-03-04

### Security
- Log pages now need the user to be authenticated.

### Fixed
- Changed directory structure to resemble Laravel: Http folder is inside an app folder now.


## 2.0.1 - 2016-03-03

### Fixed
- Removed screenshots from readme file.
- Added backpack/base dependency.


## 2.0.0 - 2016-03-03

### Fixed
- Made it work on Backpack instead of Dick.
