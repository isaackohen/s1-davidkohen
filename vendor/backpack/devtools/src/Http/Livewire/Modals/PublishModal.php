<?php

namespace Backpack\Devtools\Http\Livewire\Modals;

use Alert;
use Artisan;
use Backpack\Devtools\CustomFile;
use Livewire\Component;
use Str;

class PublishModal extends Component
{
    public $selectedFile = null;

    public $selectedFileType;

    public $selectedFileTypePath;

    public $allOptions;

    public $visibleOptions;

    public $showPublishModalAlerts = false;

    public function mount()
    {
        $this->selectedFileType = 'button';

        $this->selectedFileTypePath = $this->getPath($this->selectedFileType);
        $this->allOptions = [
            'button' => $this->getFiles('vendor/backpack/crud/src/resources/views/crud/buttons'),
            'column' => $this->getFiles('vendor/backpack/crud/src/resources/views/crud/columns'),
            'field' => $this->getFiles('vendor/backpack/crud/src/resources/views/crud/fields'),
            'filter' => $this->getFiles('vendor/backpack/crud/src/resources/views/crud/filters'),
            'widget' => $this->getFiles('vendor/backpack/crud/src/resources/views/base/widgets'),
        ];
        $this->visibleOptions = $this->allOptions[$this->selectedFileType];
    }

    public function getPath($selectedFileType)
    {
        switch ($this->selectedFileType) {
            case 'button':
                return 'resources/views/crud/buttons';
                break;

            case 'column':
                return 'resources/views/crud/columns';
                break;

            case 'field':
                return 'resources/views/crud/fields';
                break;

            case 'filter':
                return 'resources/views/crud/filters';
                break;

            case 'widget':
                return 'resources/views/base/widgets';
                break;

            default:
                return 'resources/views/crud/buttons';
                break;
        }
    }

    public function getFiles($path)
    {
        return CustomFile::allFrom(base_path($path))
                        ->values()
                        ->pluck('file_name_with_extension', 'file_path')
                        ->map(function ($item, $key) {
                            return str_replace('.blade.php', '', $item);
                        })
                        ->toArray();
    }

    public function publishFile()
    {
        if (! Str::of($this->selectedFile)->contains('vendor/backpack/crud/src/resources/views/')) {
            Alert::error('File path is malformed, sorry.')->flash();

            return false;
        }

        $subpath = explode('vendor/backpack/crud/src/resources/views/', $this->selectedFile);
        $subpath = end($subpath);
        $subpath = str_replace('.blade.php', '', $subpath);

        if (file_exists(resource_path('views/vendor/backpack/'.$subpath.'.blade.php'))) {
            Alert::error('This file has already been published. Cannot overwrite.')->flash();

            return false;
        }

        // when calling Artisan commands, run them from the base directory,
        // NOT the 'public' directory as they're run by default;
        // this is done so that files are generated in the right place,
        // since Blueprint uses relative paths
        Artisan::resolved(function () {
            chdir(base_path());
        });

        // publish the file
        Artisan::call('backpack:publish', [
            'subpath' => $subpath,
        ]);

        $output = Str::of(Artisan::output())->trim();

        // show a result message
        if ($output->startsWith('Copied to')) {
            Alert::success(str_replace('\\', '/', $output))->flash();
        } else {
            Alert::warning('Something must have gone wrong... Sorry...')->flash();
        }
    }

    public function updatedSelectedFileType($selectedFileType)
    {
        $this->selectedFileTypePath = $this->getPath($this->selectedFileType);
        $this->visibleOptions = $this->allOptions[$this->selectedFileType];
    }

    public function render()
    {
        return view('backpack.devtools::livewire.modals.publish-modal');
    }
}
