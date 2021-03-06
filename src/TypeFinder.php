<?php

namespace Luischavez\Livewire\Extensions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Luischavez\Livewire\Extensions\Exceptions\TypeFinderException;
use SplFileInfo;

/**
 * Class finder.
 */
class TypeFinder
{
    /**
     * Memory cache of classes found on the application.
     *
     * @var array
     */
    protected static array $cache = [];

    /**
     * Defined classes.
     *
     * @var array
     */
    protected static array $defined = [];

    /**
     * Search classes.
     *
     * @param string $type type
     * @return void
     */
    protected static function lookup(string $type): void
    {
        $paths = config("livewire-ext.paths.$type", []);

        static::$cache[$type] = [];

        foreach ($paths as $path) {
            if (!File::exists($path)) {
                continue;
            }

            $classes = collect(File::allFiles($path))
                ->map(function (SplFileInfo $file) use ($path) {
                    $appPath = app_path();
                    $classPath = $path;

                    $simpleName = Str::of($file->getFilename())
                        ->after($classPath.'/')
                        ->replace(['/', '.php'], ['\\', ''])
                        ->__toString();
                    $fullName = Str::of($file->getPathname())
                        ->after($appPath.'/')
                        ->replace(['/', '.php'], ['\\', ''])
                        ->__toString();
                    $parent = Str::of($file->getPathname())
                        ->after($classPath.'/')
                        ->before('/'.$simpleName)
                        ->replace(['/', '.php', '\\'], ['.', '', '.'])
                        ->__toString();

                    $parent = explode('.', $parent);
                    $parent = array_map(function ($string) {
                        return Str::camel($string);
                    }, $parent);
                    $parent = implode('.', $parent);

                    $name = Str::camel($simpleName);

                    if ($parent == $name) {
                        $parent = '';
                    }
                    
                    if (!empty($parent)) {
                        $name = $parent.'.'.$name;
                    }

                    $class = app()->getNamespace().$fullName;

                    return [
                        'class' => $class,
                        'name'  => $name,
                    ];
                })
                ->keyBy('name')
                ->map(function($action) {
                    return $action['class'];
                })
                ->toArray();

            static::$cache[$type] = array_merge(static::$cache[$type], $classes);
        }
    }

    /**
     * Get the name of the registered type.
     *
     * @param string $type  type name
     * @param string $class class name
     * @return string|null
     */
    public static function name(string $type, string $class): ?string
    {
        foreach (static::$defined[$type] ?? [] as $name => $defined) {
            if ($defined == $class) {
                return $name;
            }
        }

        if (empty(static::$cache[$type])) {
            static::lookup($type);
        }

        foreach (static::$cache[$type] ?? [] as $name => $cache) {
            if ($cache == $class) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Find a class of the requested type and name.
     *
     * @param string    $type   type
     * @param string    $name   name
     * @return string|null
     */
    public static function find(string $type, string $name): ?string
    {
        if (isset(static::$defined[$type][$name])) {
            return static::$defined[$type][$name];
        }

        if (empty(static::$cache[$type])) {
            static::lookup($type);
        }

        return static::$cache[$type][$name] ?? null;
    }

    /**
     * Make a new intance.
     *
     * @param string $type type
     * @param string $name name
     * @return object|null
     */
    public static function make(string $type, string $name): ?object
    {
        $class = static::find($type, $name);

        if ($class === null) {
            return null;
        }

        return app()->make($class);
    }

    /**
     * Make a new instance or throw an error if not found.
     *
     * @param string $type type
     * @param string $name name
     * @return object
     */
    public static function makeOrThrow(string $type, string $name): object
    {
        $instance = static::make($type, $name);

        if ($instance === null) {
            throw new TypeFinderException("Type not found: $name ($type)");
        }

        return $instance;
    }

    /**
     * Gets all the classes of the given type.
     *
     * @param string $type type
     * @return array
     */
    public static function all(string $type): array
    {
        if (empty(static::$cache[$type])) {
            static::lookup($type);
        }

        return array_merge(static::$cache[$type], static::$defined[$type]);
    }

    /**
     * Register a type.
     *
     * @param string $type  type
     * @param string $name  name
     * @param string $class class
     * @return void
     */
    public static function register(string $type, string $name, string $class): void
    {
        static::$defined[$type][$name] = $class;
    }

    /**
     * Clears the cache, if type is null then all cache will be cleared.
     *
     * @param string|null $type type
     * @return void
     */
    public static function clear(?string $type = null): void
    {
        if ($type === null) {
            static::$cache = [];
        } else {
            static::$cache[$type] = [];
        }
    }
}
