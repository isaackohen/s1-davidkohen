<?php

namespace Backpack\DevTools;

use Illuminate\Support\ServiceProvider;

class DevToolsServiceProvider extends ServiceProvider
{
    use AutomaticServiceProvider;

    protected $vendorName = 'backpack';

    protected $packageName = 'devtools';

    protected $commands = [
        \Backpack\DevTools\Console\Commands\InstallDevTools::class,
    ];

    protected $livewireComponents = [
        'migration-schema' => \Backpack\DevTools\Http\Livewire\MigrationSchema::class,
        'relationship-schema' => \Backpack\DevTools\Http\Livewire\RelationshipSchema::class,
        'publish-modal' => \Backpack\DevTools\Http\Livewire\Modals\PublishModal::class,
    ];

    protected $binds = [
         \Backpack\DevTools\GeneratorInterface::class => \Backpack\DevTools\Generators\BlueprintGenerator::class,
    ];

    protected $singleton = [
        \Backpack\DevTools\SchemaManager::class,
    ];
}
