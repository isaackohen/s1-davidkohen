# Backpack\LogManager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/backpack/logmanager.svg?style=flat-square)](https://packagist.org/packages/backpack/logmanager)
[![Software License](https://img.shields.io/badge/license-dual-blue?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-backpack/logmanager/master.svg?style=flat-square)](https://travis-ci.org/laravel-backpack/logmanager)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/laravel-backpack/logmanager.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-backpack/logmanager/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-backpack/logmanager.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-backpack/logmanager)
[![Style CI](https://styleci.io/repos/52886512/shield)](https://styleci.io/repos/52886512)
[![Total Downloads](https://img.shields.io/packagist/dt/backpack/logmanager.svg?style=flat-square)](https://packagist.org/packages/backpack/crud)

A simple interface to preview, download and delete Laravel log files.


## Install

``` bash
# install the package with composer
composer require backpack/logmanager

# [optional] Add a sidebar_content item for it
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('log') }}'><i class='nav-icon la la-terminal'></i> Logs</a></li>"
```

**For a better user experience, make sure Laravel is configured to create a new log file for each day.** That way, you can browse log entries by day too. You can do that in your ```config/logging.php``` file. 

From a default Laravel configuration, make sure the ```daily``` channel is inside the ```stack``` channel, which is used by default:

```php
    'channels' => [
        'stack' => [
            'driver'   => 'stack',
            'channels' => ['daily'],
        ],
        'single' => [
            'driver' => 'single',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
        ],
        'daily' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],
```

You can change the number of days, or path, level, etc inside this the ```daily``` channel.


## Usage

Add a menu element for it or just try at **your-project-domain/admin/log**

![LogManager interface](https://user-images.githubusercontent.com/1032474/131472960-be77e37c-313d-4049-ae54-95248cf3ad48.gif)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Overwriting Functionality

If you need to modify how this works in a project: 
- create a ```routes/backpack/logmanager.php``` file; the package will see that, and load _your_ routes file, instead of the one in the package; 
- create controllers/models that extend the ones in the package, and use those in your new routes file;
- modify anything you'd like in the new controllers/models;

## Security

If you discover any security related issues, please email tabacitu@backpackforlaravel.com instead of using the issue tracker.

Please **[subscribe to the Backpack Newsletter](http://backpackforlaravel.com/newsletter)** so you can find out about any security updates, breaking changes or major features. We send an email every 1-2 months.

## Credits

- [Cristian Tabacitu](https://tabacitu.ro)
- [All Contributors](../../contributors)

## License

Backpack is free for non-commercial use and 49 EUR/project for commercial use. Please see [License File](LICENSE.md) and [backpackforlaravel.com](https://backpackforlaravel.com/#pricing) for more information.

## Hire us

We've spend more than 10.000 hours creating, polishing and maintaining administration panels on Laravel. We've developed e-Commerce, e-Learning, ERPs, social networks, payment gateways and much more. We've worked on admin panels _so much_, that we've created one of the most popular software in its niche - just from making public what was repetitive in our projects.

If you are looking for a developer/team to help you build an admin panel on Laravel, look no further. You'll have a difficult time finding someone with more experience & enthusiasm for this. This is _what we do_. [Contact us](https://backpackforlaravel.com/need-freelancer-or-development-team). Let's see if we can work together.

