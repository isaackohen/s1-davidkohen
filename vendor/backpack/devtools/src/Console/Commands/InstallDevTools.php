<?php

namespace Backpack\DevTools\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Str;

class InstallDevTools extends Command
{
    use \Backpack\CRUD\app\Console\Commands\Traits\PrettyCommandOutput;

    protected $progressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backpack:devtools:install
                            {--debug} : Show process output or not. Useful for debugging.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install DevTools requirements on dev.';

    /**
     * Execute the console command.
     *
     * @return mixed Command-line output
     */
    public function handle()
    {
        $this->progressBar = $this->output->createProgressBar(4);
        $this->progressBar->minSecondsBetweenRedraws(0);
        $this->progressBar->maxSecondsBetweenRedraws(120);
        $this->progressBar->setRedrawFrequency(1);

        $this->progressBar->start();

        $this->info(' DevTools installation started. Please wait...');
        $this->progressBar->advance();

        // Sidebar
        $this->addSidebarEntry();
        $this->progressBar->advance();

        // Config
        $this->publishConfig();
        $this->progressBar->advance();

        // Editor
        $this->setupEditor();
        $this->progressBar->advance();

        // Finish
        $this->progressBar->finish();
        $this->info(' DevTools installation finished.');

        // Notice for prod
        $this->error('');
        $this->error('┌──────────────────────────────────────────────────────────────────────────────┐');
        $this->error('│               IMPORTANT!!! DO NOT install DevTools in production.            │');
        $this->error('│                You don\'t want your admins to have access to it.              │');
        $this->error('├──────────────────────────────────────────────────────────────────────────────┤');
        $this->error('│     Make sure your build & deploy scripts use `composer install --no-dev`    │');
        $this->error('│              or uninstall DevTools after you\'re done, by running             │');
        $this->error('│                   `composer remove --dev backpack/devtools`                  │');
        $this->error('└──────────────────────────────────────────────────────────────────────────────┘');
    }

    public function addSidebarEntry()
    {
        $path = 'resources/views/vendor/backpack/base/inc/sidebar_content.blade.php';

        if (! File::exists($path)) {
            return $this->error('The sidebar_content file does not exist. Make sure Backpack\CRUD is already installed.');
        }

        $content = Str::of(File::get($path));

        if ($content->contains('devtools')) {
            return $this->comment(' Sidebar item already existed.');
        }

        $sidebarEntry = implode(PHP_EOL, [
            '',
            "@includeWhen(class_exists(\Backpack\DevTools\DevToolsServiceProvider::class), 'backpack.devtools::buttons.sidebar_item')",
        ]);

        // Add after dashboard
        if ($content->contains('dashboard')) {
            $content = preg_replace('/(dashboard.+)/', '$1'.PHP_EOL.$sidebarEntry, (string) $content);
        }
        // Add on top
        else {
            $content = $sidebarEntry.PHP_EOL.$content;
        }

        // Save file
        File::put($path, $content)
            ? $this->info(' Successfully added DevTools link to the sidebar_content file.')
            : $this->error(' Could not write to sidebar_content file.');
    }

    public function publishConfig()
    {
        if (File::exists(config_path('backpack/devtools.php'))) {
            return $this->comment(' Config is already published.');
        }

        if ($this->confirm('Do you want to publish the DevTools config file? It will allow you to define non-standard places where you store might your Models/Controllers/etc.', false)) {
            $this->executeArtisanProcess('vendor:publish', [
                '--provider' => 'Backpack\DevTools\DevToolsServiceProvider',
                '--tag' => 'config',
            ]);

            $this->info(' Successfully published config file');
        }
    }

    public function setupEditor()
    {
        if (! File::exists('.env')) {
            return $this->error(' .ENV file does not exist');
        }

        $envContent = Str::of(\File::get('.env'));

        if ($envContent->contains('DEVTOOLS_EDITOR')) {
            return $this->comment(' DevTools editor is already on .env file.');
        }

        if ($this->confirm('[Optional] DevTools can have links on filenames. Click them and they\'ll open that file in your code editor or IDE, if that app has an URL handler set up. Choose which editor to use?', true)) {
            $this->comment(' Choose between: vscode, vscode-insiders, subl, sublime, textmate, emacs, macvim, phpstorm, idea, atom, nova, netbeans, xdebug. This will only work if your editor has an URL handler. VSCode does by default, most other editors do not, but you can configure one afterwards. Please see https://backpackforlaravel.com/products/devtools/troubleshooting.md for more info on this.');

            $editor = $this->ask('Editor');

            if (! $editor) {
                return $this->error(' DevTools default editor was not added to .env file.');
            }

            File::put('.env', $envContent.PHP_EOL.'DEVTOOLS_EDITOR='.$editor);

            $this->info(' Successfully added DevTools default editor to .env file.');
        }
    }
}
