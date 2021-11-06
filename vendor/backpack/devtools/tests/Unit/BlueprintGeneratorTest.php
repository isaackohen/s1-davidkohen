<?php

namespace Tests\Unit;

use Backpack\DevTools\Generators\BlueprintGenerator;
use Backpack\DevTools\Http\Requests\MigrationRequest;
use Backpack\DevTools\Http\Requests\ModelRequest;
use Request;
use Schema;
use Str;
use Tests\UnitTestCase;

class BlueprintGeneratorTest extends UnitTestCase
{
    private $generator;

    public $cachePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new BlueprintGenerator();
    }

    // Defaults

    public function testGenerateYamlFileFromDefaultMigration()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/default-migration.php'));

        $model = Str::of($mockRequest->input('table'))->singular()->studly();

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertFileExists($this->generator->yamlFilePath);
        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/default.yaml'));
    }

    public function testGenerateYamlFileFromDefaultModel()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/default-model.php'));

        $model = Str::of($mockRequest->input('name'))->singular()->studly();

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertFileExists($this->generator->yamlFilePath);
        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/default.yaml'));
    }

    // Migration

    public function testMigrationFileIsGeneratedFromDefaultMigration()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/default-migration.php'));

        $this->generator->generate($mockRequest);

        $this->assertNotEmpty($this->generator->createdFiles['migrations']);

        $this->assertEqualFiles(base_path($this->generator->createdFiles['migrations'][0]), $this->getHelper('database/migrations/create_testings_table.stub'));
    }

    public function testMigrationIsRunFromDefaultMigration()
    {
        $testingHasTable = $this->requireHelper('scenarios/default-migration.php');
        $testingHasTable['table'] = 'testing_has_tables';

        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $testingHasTable);

        $this->generator->generate($mockRequest);

        $this->assertTrue(Schema::hasTable($testingHasTable['table']));
    }

    public function testMigrationsForBelongsToManyPivotTableAreCreatedAndMigrated()
    {
        $testingHasTable = $this->requireHelper('scenarios/double-migration.php');
        $testingHasTable['table'] = 'something';

        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $testingHasTable);

        $this->generator->generate($mockRequest);

        $this->assertNotEmpty($this->generator->createdFiles['models']);
        $this->assertEqualFiles(app_path('models/Something.php'), $this->getHelper('models/Something.stub'));
        $this->assertEqualFiles(base_path($this->generator->createdFiles['migrations'][0]), $this->getHelper('database/migrations/create_somethings_table.stub'));
        $this->assertEqualFiles(base_path($this->generator->createdFiles['migrations'][1]), $this->getHelper('database/migrations/create_something_user_table.stub'));

        $this->assertTrue(Schema::hasTable('somethings'));
        $this->assertTrue(Schema::hasTable('something_user'));
    }

    // Model

    public function testModelFileIsGeneratedFromDefaultModel()
    {
        $mockRequest = Request::create(backpack_url('/devtools/model'), 'POST', $this->requireHelper('scenarios/default-model.php'));

        $this->generator->generate($mockRequest);

        $this->assertNotEmpty($this->generator->createdFiles['models']);

        $this->assertEqualFiles(app_path('models/Testing.php'), $this->getHelper('models/Testing.stub'));
    }

    // Request

    public function testRequestValidatesDefaultMigration()
    {
        $request = new MigrationRequest();
        $rules = $request->rules();

        $validator = $this->app['validator']->make($this->requireHelper('scenarios/default-migration.php'), $rules);

        $this->assertEmpty($validator->errors());
    }

    public function testRequestValidatesDefaultModel()
    {
        $request = new ModelRequest();
        $rules = $request->rules();

        $validator = $this->app['validator']->make($this->requireHelper('scenarios/default-model.php'), $rules);

        $this->assertEmpty($validator->errors());
    }

    // Controller

    public function testCrudControllerIsBuiltFromDefaultModel()
    {
        $mockRequest = Request::create(backpack_url('/devtools/model'), 'POST', $this->requireHelper('scenarios/default-model.php'));

        $this->generator->generate($mockRequest);

        $this->assertNotEmpty($this->generator->createdFiles['models']);

        // Validate Crud Controller
        $controller = app_path('Http/Controllers/Admin/TestingCrudController.php');
        $this->assertFileExists($controller);
        $this->assertEqualFiles($controller, $this->getHelper('controllers/TestingCrudController.stub'));

        // Validate route
        $routes = base_path('routes/backpack/custom.php');
        $this->assertFileExists($routes);
        $this->assertStringContainsString("Route::crud('testing', 'TestingCrudController');", file_get_contents($routes));
    }

    // Request

    public function testRequestIsBuiltFromDefaultModel()
    {
        $mockRequest = Request::create(backpack_url('/devtools/model'), 'POST', $this->requireHelper('scenarios/default-model.php'));

        $this->generator->generate($mockRequest);

        $this->assertNotEmpty($this->generator->createdFiles['models']);

        // Validate Request
        $request = app_path('Http/Requests/TestingRequest.php');
        $this->assertFileExists($request);
        $this->assertEqualFiles($request, $this->getHelper('Requests/TestingRequest.stub'));
    }

    // Seeder

    public function testSeederGeneratesYaml()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/seeder.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/seeder.yaml'));
    }

    public function testSeederFileIsGenerated()
    {
        $mockRequest = Request::create(backpack_url('/devtools/model'), 'POST', $this->requireHelper('scenarios/seeder.php'));

        $this->generator->generate($mockRequest);

        $this->assertNotEmpty($this->generator->createdFiles['seeders']);

        $this->assertEqualFiles($this->generator->createdFiles['seeders'][0], $this->getHelper('database/seeders/TestingSeeder.stub'));
    }

    // Factory

    public function testFactoryGeneratesYamlFile()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/factory.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/factory.yaml'));
    }

    public function testFactoryFileIsGenerated()
    {
        $mockRequest = Request::create(backpack_url('/devtools/model'), 'POST', $this->requireHelper('scenarios/factory.php'));

        $this->generator->generate($mockRequest);

        $this->assertNotEmpty($this->generator->createdFiles['factories']);

        $this->assertEqualFiles($this->generator->createdFiles['factories'][0], $this->getHelper('database/factories/TestingFactory.stub'));
    }

    // Composite names

    public function testGenerateYamlFileFromCompositeNameMigration()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/composite-name-migration.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/composite-name.yaml'));
    }

    public function testGenerateYamlFileFromCompositeNameModel()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/composite-name-model.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/composite-name.yaml'));
    }

    // Columns

    public function testGenerateYamlFileFromColumnEnum()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/column-enum.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/column-enum.yaml'));
    }

    public function testGenerateYamlFileFromColumnForeign()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/column-foreign.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/column-foreign.yaml'));
    }

    public function testGenerateYamlFileFromColumnNoTimestamp()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/column-no-timestamp.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/column-no-timestamp.yaml'));
    }

    public function testGenerateYamlFileFromColumnFloat()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/columns-float.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/columns-float.yaml'));
    }

    // Relationships

    public function testGenerateYamlFileFromRelationships()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/relationships.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/relationships.yaml'));
    }

    // Modifiers

    public function testGenerateYamlFileFromModifiers()
    {
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $this->requireHelper('scenarios/modifiers.php'));

        $model = $this->getModelFromRequest($mockRequest);

        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/modifiers.yaml'));
    }

    // Giant

    public function testGiantMigration()
    {
        $migration = $this->requireHelper('scenarios/giant-migration.php');
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $migration);

        // Yaml file
        $model = $this->getModelFromRequest($mockRequest);
        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertFileExists($this->generator->yamlFilePath);
        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/giant.yaml'));

        // Migration
        $this->generator->generate($mockRequest);

        // Migration file created
        $this->assertNotEmpty($this->generator->createdFiles['migrations']);

        // Migration file equals stub
        $this->assertEqualFiles(base_path($this->generator->createdFiles['migrations'][0]), $this->getHelper('database/migrations/create_giants_table.stub'));
        $this->assertEqualFiles(base_path($this->generator->createdFiles['migrations'][1]), $this->getHelper('database/migrations/create_giant_tag_table.stub'));

        // Migration run
        $this->assertTrue(Schema::hasTable('giants'));
        $this->assertTrue(Schema::hasTable('giant_tag'));

        // Model
        $this->assertNotEmpty($this->generator->createdFiles['models']);
        $this->assertEqualFiles(base_path($this->generator->createdFiles['models'][0]), $this->getHelper('models/Giant.stub'));

        // Assert DB has the right columns
        $dbColumns = collect(Schema::getColumnListing('giants'));
        $expectedColumns = collect($migration['columns'])
            ->keys()
            ->except('timestamps', 'softDeletes')
            ->push('deleted_at', 'created_at', 'updated_at');

        $this->assertEmpty($dbColumns->diff($expectedColumns));

        // Validate Crud Controller
        $controller = app_path('Http/Controllers/Admin/GiantCrudController.php');
        $this->assertFileExists($controller);
        $this->assertEqualFiles($controller, $this->getHelper('controllers/GiantCrudController.stub'));

        // Validate Request
        $request = app_path('Http/Requests/GiantRequest.php');
        $this->assertFileExists($request);
        $this->assertEqualFiles($request, $this->getHelper('Requests/GiantRequest.stub'));

        // Validate route
        $routes = base_path('routes/backpack/custom.php');
        $this->assertFileExists($routes);
        $this->assertStringContainsString("Route::crud('giant', 'GiantCrudController');", file_get_contents($routes));

        // Validate Factory
        $request = database_path('factories/GiantFactory.php');
        $this->assertFileExists($request);
        $this->assertEqualFiles($request, $this->getHelper('database/factories/GiantFactory.stub'));

        // Validate Seeder
        $request = database_path('seeders/GiantSeeder.php');
        $this->assertFileExists($request);
        $this->assertEqualFiles($request, $this->getHelper('database/seeders/GiantSeeder.stub'));
    }

    /**
     * @group error
     */
    public function testNoMissingColumnsOnTypesOrder()
    {
        $migration = app(\Backpack\DevTools\Http\Livewire\MigrationSchema::class);

        $column_types_order = collect($migration->selectable_column_types_order)
            ->flatten()
            ->reject(function ($value, $key) {
                return $value === '-';
            })
            ->unique()
            ->sort()
            ->values();

        $column_types = collect($migration->selectable_column_types)
            ->keys()
            ->sort()
            ->values();

        $this->assertEquals($column_types_order, $column_types);
    }

    public function testGenerateRelationsWithCustomNames()
    {
        $migration = $this->requireHelper('scenarios/belongsto-with-custom-relation-name.php');
        $mockRequest = Request::create(backpack_url('/devtools/migration'), 'POST', $migration);

        $model = $this->getModelFromRequest($mockRequest);
        $this->generator->createYamlFile($model, $mockRequest);

        $this->assertFileExists($this->generator->yamlFilePath);
        $this->assertEqualFiles($this->generator->yamlFilePath, $this->getHelper('scenarios/belongsto-with-custom-relation-name.yaml'));

        $this->generator->generate($mockRequest);

        // Validate Crud Controller
        $controller = app_path('Http/Controllers/Admin/RelationsWithCustomNameCrudController.php');
        $this->assertFileExists($controller);
        $this->assertEqualFiles($controller, $this->getHelper('controllers/RelationsWithCustomNameCrudController.stub'));

        $this->assertNotEmpty($this->generator->createdFiles['models']);
        $this->assertEqualFiles(base_path($this->generator->createdFiles['models'][0]), $this->getHelper('models/RelationsWithCustomNames.stub'));
    }

    // Helpers

    private function getModelFromRequest($request)
    {
        return Str::of($request->input('name') ?? $request->input('table'))->singular()->studly();
    }
}
