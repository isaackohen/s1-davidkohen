## Troubleshooting Problems

Here are a few common problems that people have run into, when installing this package:


## Installation

### Manual installation

If for some reason `php artisan backpack:devtools:install` doesn't work for you, or you want to do something different, here's how you can manually wire DevTools to your project. This would replace the Step 4 from the installation process:

**Step 4.A.** Add a menu item for it, inside your `resources/views/vendor/backpack/base/inc/sidebar_content.blade.php`:

```php
@if (app()->environment('local'))
  <li class='nav-item'><a class='nav-link' href='{{ backpack_url('devtools') }}'><i class='nav-icon la la-toolbox'></i> DevTools</a></li>
@endif
``` 
We like to add it right after "Dashboard". Alternatively, you can access the devtools at `your-app-name/admin/devtools`.

**Step 4.B.** (optional) Publish the config file (`config/backpack/devtools.php`), if you want to change where DevTools looks for migrations, factories, seeders, models, CRUDs, etc.

```
php artisan vendor:publish --provider="Backpack\DevTools\DevToolsServiceProvider" --tag="config"
```

**Step 4.C.** (optional, experimental) You can click the names of the generated files in DevTools to open them inside your preferred code editor or IDE. In order to do that, you should define the editor of your choice inside your project's `.ENV` file:

```php
DEVTOOLS_EDITOR=vscode

# Options: vscode, vscode-insiders, subl, sublime, textmate, emacs, macvim, phpstorm, idea, atom, nova, netbeans, xdebug
```

This will add a link for that editor to all file names (eg. `vscode://open?url=path-to-file`), but please take note that it is up to _your system_ to actually open that file in that editor. VSCode does that by default, most other editors do not. To add support for file links for your particular Operating System & Editor, search the internet for something like "_URL Handler for [Sublime Text] on [Mac OS X]_". For Sublime & MacOS in particular, we use [this app](https://github.com/inopinatus/sublime_url). If you can recommend another solution for an editor-OS combination, please let us know. That's why this is marked as experimental, there are just too many combinations to test.


### ERROR: Illuminate\Database\QueryException could not find driver

Some webservers come with `sqlite` extension disabled by default. All you have to do is go to your `php.ini` inside your php instalation folder and un-comment (remove the semi-colon) from `extension=pdo_sqlite`. Please check the apropriate `php.ini` file to update when using bundles like `xampp`/`wampp`/`laragon` etc., that might use different php versions for `cli` and for `web`.


### How to not install DevTools in staging and production

You must make sure your staging and production environments DO NOT include the DevTools interface and functionality:

- if you auto-deploy your project using composer & git (using Laravel Forge, Envoyer or anything else), make sure that you're running `composer install --no-dev`;
- if you deploy your project by uploading a ZIP file or FTP the files, make sure you upload builds _after_ you've run `composer install --no-dev`;

Alternatively, after you're done generating stuff using DevTools, just do `composer remove --dev backpack/devtools`, that'll get rid of it. Or include that command in your build pipeline.

### How to uninstall

You might want to remove `backpack/devtools` from your project. If that's the case, just run `composer remove --dev backpack/devtools`. 

One frequently asked question here is "_do I still need Backpack\CRUD installed afterwards_" and the answer is YES:
- `backpack/crud` should be "required"
- `backpack/devtools` should be "required-dev"

You can uninstall DevTools, but since you've probably generated Backpack CRUDs with it, you'll still need Backpack\CRUD in your `composer.json`'s require section. Make sure it's there, otherwise the `composer remove` process will throw an error.

-----

If you run into new problems when installing this package, please send an email to hello@backpackforlaravel.com to tell us about it. If we add it to this file, it'll help you the next time you install it, and others developers like you.
