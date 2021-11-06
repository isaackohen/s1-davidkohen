# Backpack DevTools


A web interface to generate migrations, models and CRUDs. Forget about the command line. Forget about migration types. Go from idea to full CRUD in seconds.


![2021-08-05 18 37 50](https://user-images.githubusercontent.com/1032474/128379216-72ae55fa-fcff-4747-8c35-42c733923c94.gif)

## Why?

What's your current process, when creating an entity in your project? We bet you need:
- a `migration`;
- a `model`;
- maybe a `factory` and a `seeder`;
- then you need an admin panel for it, so you create a `CrudController`, `CrudRequest`, `route` and `sidebar item`;

Haven't you gotten tired of doing that, for each new entity? We have. Not only is it boring and repetitive... it takes quite a bit of time to get _just right_. We've all tried to speed this up using _existing tools_. Hell, we've even contributed or taken over their maintenance:
- we've used `laracasts/generators` for `migrations` and `models`, but it's difficult to generate a _final_ migration in one command line; we always forget stuff... and it doesn't generate relationships;
- we've used `laravel-shift/blueprint` for `migrations` and `models` _with_ relationships, but then you have to learn&remember the Blueprint YAML syntax; we forget it, so we get into a back-and-forth between the Blueprint docs, the Laravel migration docs, the YAML file and the generated files;
- then for CRUDs, we always use `backpack/generators`, but... it doesn't fill in the `CrudRequest` or the `fields` & `columns`... it's a technical limitation... so we have to punch in every column/field we want afterwards;

We're super-grateful to the creators of those tools. We've been using them _for years_ and contributed to their projects as much as possible. But... we've always felt we can do better. So we did. 

We're _web developers_, so we've created a _web interface_ (😱 ), which uses those exact tools behind the scenes, connects them and polishes the generated files, automatically. We _thought_ it would take us a few days, maybe weeks, but after quite a few _months_ of working at this, we can finally say we've got something _better_. Something much _much_ better.

Thanks to this package, creating new entities is no longer boring, or tedious. It's actually... fun 😀 We think you'll _absolutely love_ this new way of starting your Laravel projects... In fact, we think you won't be able to go back to writing them by hand. We know we can't 😀

## Features

### Generate full entities in seconds

Thanks to Backpack\Devtools, you can just fill in one form:

![Screenshot 2021-08-05 at 18 20 43](https://user-images.githubusercontent.com/1032474/128375953-72b637c5-75cd-4626-a36b-e49f9f4d9952.png)

And you can get:
- a complete `migration`;
- a `model` with `fillable` and relationships already written;
- a `seeder` and `factory` with the columns defined;
- a `CrudController` with the fields & columns already defined;
- a `CrudRequest` with the validation rules already inferred from the database restrictions;
- a `route` and `sidebar item` for the admin panel;

That's a full working CRUD, you can go ahead and add items from the admin interface. 

No, really. Here's what the form above generated:

![2021-08-05 18 24 30](https://user-images.githubusercontent.com/1032474/128376790-8bd06869-6a7c-45c1-8328-14aacd9d178a.gif)

### Manage your database migrations in a web interface

No more dumpster-diving into the `database/migrations` directory. Easily see your migrations, which ones are run, run them, roll them back and even open them in your editor to slighly polish them.

![2021-08-05 13 00 51](https://user-images.githubusercontent.com/1032474/128332124-acd2e3d9-c79e-42d0-9c7b-dfc0c8bbd6d3.gif)

### Manage your models in a web interface

See the state of your Models, which ones have CRUDs, which ones have factories and seeders and insert dummy data right then&there.

![2021-08-05 18 45 15](https://user-images.githubusercontent.com/1032474/128380633-1897f33f-0224-410c-b63e-b1d4fea9202f.gif)

### Generate custom admin panel components

[UNDER DEVELOPMENT] Soon enough, this package will also help you:
- use templates to create a custom Backpack blade files - `columns`, `fields`, `filters`, `buttons`, `widgets`;
- use templates to create custom Backpack `Operations`;
- create completely custom Backpack pages (like dashboards);

You can _forget_ about using the command line for those things. You can forget about looking inside the `vendor/backpack/crud` folder to see what you want to overwrite or get inspiration from. You think it, you click it, you have it.


----

## Requirements

Backpack DevTools assumes that you already have:
- Laravel 8+
- PHP 7.3+
- MySQL 5.7.x / 8.x
- SQLite (eg. `pdo_sqlite` extension enabled)
- `backpack/crud` v4.1+ properly installed

## Installation

**Step 1.** [Buy access to this package](https://backpackforlaravel.com/cart/add-unique-product/1) and you'll get an [access token](https://backpackforlaravel.com/user/tokens). With that token in hand, you should instruct your project to pull in DevTools from our private repository, instead of Packagist:
- add [your token](https://backpackforlaravel.com/user/tokens) to your project's `auth.json` file by running `composer config http-basic.backpackforlaravel.com [your-token-username] [your-token-password]`
- add the Backpack private repo to your `composer.json`:

```json
    "repositories": [
        {
            "type": "composer",
            "url": "https://repo.backpackforlaravel.com/"
        }
    ],
```

**Step 2.** Install the package using Composer:

``` bash
# Recommended - get latest DevTools version and update dependencies (backpack, livewire, sushi, blueprint)
composer require --dev --with-all-dependencies backpack/devtools

# Alternatively - get the version of DevTools you can install without updating anything
composer require --dev backpack/devtools
```

Common errors:
- composer require conflict - run the recommended method above or `composer update`, to get the latest version;
- `Error 500 Class X does not seem to be auto-loaded` - run the recommended method above or `composer update`, to get the latest version;

**Step 3.** Run the installation command and follow the instructions:

```bash
php artisan backpack:devtools:install
```

That's it. You can now visit `your-app-name/admin/devtools` to use DevTools. The rest is point&click.

> SUPER-IMPORTANT!!! You must make sure your `staging` and `production` environments DO NOT include the DevTools interface and functionality:
> - if you auto-deploy your project using `composer` & `git` (using Laravel Forge, Envoyer or anything else), make sure that you're running `composer install --no-dev`;
> - if you deploy your project by uploading a ZIP file or FTP the files, make sure you upload builds _after_ you've run `composer install --no-dev`;
> - alternatively, after you're done generating stuff using DevTools, just do `composer remove --dev backpack/devtools`, that'll get rid of it; or include that command in your build pipeline;

## Support

To submit issues, bugs and feature requests, please see our [laravel-backpack/devtools-issues](https://github.com/laravel-backpack/devtools-issues) repo on Github.

## Security

If you discover any security related issues, please email cristian.tabacitu@backpackforlaravel.com instead of using the issue tracker.

## License

This software is proprietary & closed-source, released under the [End-User License Agreement (EULA) for Private Backpack Addons](https://backpackforlaravel.com/eula). A copy of that license is also provided inside the source code - you can read that file by using the tabs at the beginning of this page.


[link-packagist]: https://packagist.org/packages/backpack/devtools
[link-downloads]: https://packagist.org/packages/backpack/devtools
[link-travis]: https://travis-ci.org/backpack/devtools
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/tabacitu
[link-contributors]: ../../contributors
