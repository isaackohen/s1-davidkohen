<?php

namespace Backpack\Generators\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BuildBackpackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backpack:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUDs for all Models that don\'t already have one.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // make a list of all models
        $models = $this->getModels(base_path().'/app');

        if (! count($models)) {
            $this->error('No models found.');

            return false;
        }

        foreach ($models as $key => $model) {
            $this->info("--- $model ---");
            // Create the CrudController & Request
            // Attach CrudTrait to Model
            // Add sidebar item
            // Add routes
            $this->call('backpack:crud', ['name' => $model]);
        }
    }

    private function getModels($path)
    {
        $out = [];
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' or $result === '..') {
                continue;
            }
            $filename = $path.'/'.$result;

            if (is_dir($filename)) {
                $out = array_merge($out, $this->getModels($filename));
            } else {
                $file_content = file_get_contents($filename);
                if (Str::contains($file_content, 'Illuminate\Database\Eloquent\Model') &&
                    Str::contains($file_content, 'extends Model')) {
                    $out[] = Arr::last(explode('/', substr($filename, 0, -4)));
                }
            }
        }

        return $out;
    }
}
