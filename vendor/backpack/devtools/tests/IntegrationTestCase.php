<?php

namespace Tests;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Illuminate\Foundation\Testing\TestCase;

class IntegrationTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app()->singleton('crud', function ($app) {
            return new CrudPanel($app);
        });
    }

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://bpdemo.local';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
