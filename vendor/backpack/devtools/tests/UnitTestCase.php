<?php

namespace Tests;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\BackpackServiceProvider;
use Backpack\DevTools\DevToolsServiceProvider;
use Backpack\Generators\GeneratorsServiceProvider;
use Blueprint\BlueprintServiceProvider;
use Illuminate\Support\Facades\File;
use Livewire\LivewireServiceProvider;
use Prologue\Alerts\AlertsServiceProvider;

class UnitTestCase extends \Orchestra\Testbench\TestCase
{
    public function tearDown(): void
    {
        \Mockery::close();

        $this->cleanTestFiles();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
        $app['config']->set('devtools.paths.models', app_path());
        $app['config']->set('devtools.paths.crud_controllers', app_path());
        $app['config']->set('devtools.paths.migrations', database_path('migrations'));
        $app['config']->set('devtools.paths.factories', database_path('factories'));
        $app['config']->set('devtools.paths.seeders', database_path('seeders'));

        $app->singleton('crud', function ($app) {
            return new CrudPanel($app);
        });

        $app['config']->set('backpack.base.root_disk_name', 'public');
        $app['config']->set('app.debug', 'true');
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $this->cachePath = __DIR__.'/cache';
        config(['sushi.cache-path' => $this->cachePath]);

        $this->cleanTestFiles();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            DevToolsServiceProvider::class,
            BackpackServiceProvider::class,
            BlueprintServiceProvider::class,
            AlertsServiceProvider::class,
            GeneratorsServiceProvider::class,
        ];
    }

    protected function getHelper(string $path): string
    {
        return __DIR__."/helpers/$path";
    }

    protected function requireHelper(string $path): array
    {
        return require $this->getHelper($path);
    }

    protected function assertEqualFiles($a, $b)
    {
        return $this->assertEquals(
            str_replace("\r", '', file_get_contents($a)),
            str_replace("\r", '', file_get_contents($b))
        );
    }

    private function cleanTestFiles()
    {
        // clean cache folder
        File::ensureDirectoryExists($this->cachePath, 0777);
        File::cleanDirectory($this->cachePath);

        // delete custom route files
        File::cleanDirectory(base_path('routes/backpack'));

        // delete controller and request files
        File::cleanDirectory(app_path('Http/Controllers/Admin'));
        File::cleanDirectory(app_path('Http/Requests'));

        // delete models
        File::cleanDirectory(app_path('Models'));

        // delete database files
        File::cleanDirectory(database_path('factories'));
        File::cleanDirectory(database_path('migrations'));
        File::cleanDirectory(database_path('seeds'));

        // delete draft.yaml
        File::delete(base_path('draft.yaml'));
    }
}
