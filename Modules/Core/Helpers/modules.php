<?php

declare(strict_types=1);

use Illuminate\Support\Str;

if (! function_exists('module_path')) {
    /**
     * Return the absolute path for a given module.
     *
     * @param  string  $module  The module name (e.g. "Core", "Business").
     * @param  string|null  $path  Optional path suffix relative to the module root.
     */
    function module_path(string $module, ?string $path = null): string
    {
        $base = base_path('Modules'.DIRECTORY_SEPARATOR.Str::studly($module));

        return $path === null ? $base : $base.DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR);
    }
}

if (! function_exists('module_class')) {
    /**
     * Build a fully-qualified class name for a given module.
     *
     * @param  string  $module  The module name.
     * @param  string  $class  The class name (may include a sub-namespace).
     */
    function module_class(string $module, string $class): string
    {
        return sprintf('Modules\\%s\\%s', Str::studly($module), ltrim($class, '\\'));
    }
}
