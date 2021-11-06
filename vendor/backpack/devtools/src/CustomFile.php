<?php

namespace Backpack\DevTools;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Carbon\Carbon;
use File;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use JsonSerializable;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class CustomFile implements Arrayable, Jsonable, JsonSerializable
{
    public function __construct($file, $relative_path = '', $relative_path_name = '')
    {
        $this->file = is_string($file) ? new SplFileInfo($file, $relative_path, $relative_path_name) : $file;

        $this->id = $this->file->getInode() === 0 ? Str::beforeLast($this->file->getBasename(), '.') : $this->file->getInode();
        // file attributes
        $this->file_type = $this->file->getType();
        $this->file_extension = $this->file->getExtension();
        $this->file_path = $this->file->getPathname();
        $this->file_path_absolute = $this->file_path;
        $this->file_path_relative = $this->file->getRelativePathname();
        $this->file_path_from_base = (string) Str::of($this->file_path_absolute)->after(base_path().DIRECTORY_SEPARATOR)->replace('\\', '/');
        $this->file_name = (string) Str::of($this->file->getFilename())->basename('.php');
        $this->file_name_without_extension = $this->file_name;
        $this->file_name_with_extension = $this->file->getFilename();

        $this->file_last_accessed_at = new Carbon($this->file->getATime());
        $this->file_last_changed_at = new Carbon($this->file->getCTime());
        $this->file_last_modified_at = new Carbon($this->file->getMTime());
        $this->file_created_at = new Carbon(filectime($this->file_path));

        $this->file_directory = $this->file->getPath();
        $this->file_contents = $this->file->getContents();

        // class attributes
        $this->class_name = $this->file_name_without_extension;
        $this->class_namespace = static::extractNamespace($this->file_contents);
        $this->class_path = $this->inferClassPath();
        $this->class_path_with_extension = $this->inferClassPath().'.'.$this->file_extension;
    }

    public function isFile()
    {
        return $this->file_type === 'file';
    }

    public function isPhp()
    {
        return $this->file_extension === 'php';
    }

    public function isClass()
    {
        try {
            return class_exists($this->class_path);
        } catch (\ParseError $e) {
            return true;
        }
    }

    public function isValid()
    {
        return $this->getErrors() === null;
    }

    public function getErrors()
    {
        try {
            (new ReflectionClass($this->class_path));
        } catch (\ParseError $e) {
            return $e;
        }
    }

    public function isModel()
    {
        return $this->isSubClassOf(Model::class);
    }

    public function isMigration()
    {
        // We cannot use ReflectionClass to determine if a file is a migration because Laravel
        // migrations files do not use classname as the filename, they use this convention:
        // 2021_03_12_130520_create_articles_table.php, not CreateArticlesTable.php

        $migrationClass = static::getClassObjectFromFile($this->file_path);

        return is_subclass_of($migrationClass, Migration::class);
    }

    public function isController()
    {
        return $this->isSubClassOf(Controller::class);
    }

    public function isCrudController()
    {
        return $this->isSubClassOf(CrudController::class);
    }

    public function isFormRequest()
    {
        return $this->isSubClassOf(FormRequest::class);
    }

    public function isFactory()
    {
        return $this->isSubClassOf(Factory::class);
    }

    public function isSeeder()
    {
        return $this->isSubClassOf(Seeder::class);
    }

    public function inferClassPath()
    {
        if ($this->class_namespace) {
            return $this->class_namespace.'\\'.$this->class_name;
        }

        return $this->class_name;
    }

    private function isSubClassOf($parent)
    {
        try {
            return (new ReflectionClass($this->class_path))->isSubClassOf($parent);
        } catch (\ParseError $e) {
            $file = Str::of($this->file_contents);

            return $file->contains("extends \\$parent") || ($file->contains('extends '.Str::afterLast($parent, '\\')) && $file->contains("use $parent;"));
        }
    }

    /**
     * ----------------
     * Static functions
     * ----------------.
     */
    public static function allFrom($paths = null)
    {
        $paths = $paths ?? config('backpack.devtools.paths.models');

        if (is_string($paths)) {
            $paths = (array) $paths;
        }

        $files = [];

        foreach ($paths as $path) {
            // if a string was passed, look into all subdirectories (recursive)
            // if an array was passed, the second item (true/false) determines recursiveness
            $recursive = true;
            if (is_array($path)) {
                list($path, $recursive) = $path;
            }

            if (File::exists($path)) {
                $files = array_merge($files, $recursive ? File::allFiles($path) : File::files($path));
            }
        }

        $files = collect($files)
            ->map(function ($file, $key) {
                // turn the normal SplFileInfo into a CustomFile
                // in order to get all the attributes we need
                return new self($file);
            })
            ->filter(function ($file, $key) {
                return $file->isFile() && $file->isPhp();
            });

        return $files;
    }

    public static function extractNamespace($fileContents)
    {
        $tokens = token_get_all($fileContents);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }

        if (! $namespace_ok) {
            return null;
        } else {
            return $namespace;
        }
    }

    /**
     * Get the full name (name \ namespace) of a class from its file path.
     * Result example: (string) "I\Am\The\Namespace\Of\This\Class".
     *
     * @param $filePathName
     * @return  string
     */
    public static function getClassFullNameFromFile($filePathName)
    {
        if (static::getClassNamespaceFromFile($filePathName) == null) {
            return static::getClassNameFromFile($filePathName);
        }

        return static::getClassNamespaceFromFile($filePathName).'\\'.
        static::getClassNameFromFile($filePathName);
    }

    /**
     * Build and return an object of a class from its file path.
     *
     * @param $filePathName
     * @return  mixed
     */
    public static function getClassObjectFromFile($filePathName)
    {
        $classString = static::getClassFullNameFromFile($filePathName);

        // if class name is false, it's probably an anonymous migration so return it
        if (! $classString) {
            return include $filePathName;
        }

        // if we get the class string but it's not auto-loaded yet we manually include it
        if (! class_exists($classString)) {
            include $filePathName;

            return new $classString;
        }

        $object = new $classString;

        return $object;
    }

    /**
     * Get the class namespace form file path using token.
     *
     * @param $filePathName
     * @return  null|string
     */
    protected static function getClassNamespaceFromFile($filePathName)
    {
        $src = file_get_contents($filePathName);

        $tokens = token_get_all($src);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }
        if (! $namespace_ok) {
            return null;
        } else {
            return $namespace;
        }
    }

    /**
     * Get the class name form file path using token.
     *
     * @param $filePathName
     * @return  mixed
     */
    protected static function getClassNameFromFile($filePathName)
    {
        $php_code = file_get_contents($filePathName);

        $classes = [];
        $tokens = token_get_all($php_code);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING
            ) {
                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }

        return $classes[0] ?? false;
    }

    /**
     * --------------------
     * Accesibility methods
     * --------------------.
     */

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array) $this;
    }

    /**
     * Convert the object to JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \Illuminate\Database\Eloquent\JsonEncodingException
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }

        return $json;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
