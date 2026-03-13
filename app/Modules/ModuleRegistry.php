<?php

namespace App\Modules;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleRegistry
{
    public function enabled(): array
    {
        return config('api.modules', []);
    }

    public function all(): array
    {
        return collect($this->enabled())
            ->map(fn (string $module) => $this->moduleDefinition($module))
            ->filter()
            ->values()
            ->all();
    }

    public function moduleDefinition(string $module): ?array
    {
        $studly = Str::studly($module);

        $basePath = base_path("modules/{$studly}");

        $providerClass = "Modules\\{$studly}\\Providers\\{$studly}ServiceProvider";
        $providerPath = "{$basePath}/src/Providers/{$studly}ServiceProvider.php";

        if (! File::exists($providerPath)) {
            return null;
        }

        return [
            'key' => $module,
            'studly' => $studly,
            'base_path' => $basePath,
            'provider_class' => $providerClass,
            'provider_path' => $providerPath,
        ];
    }

    public function permissionConfigPaths(): array
    {
        return collect($this->enabled())
            ->map(fn (string $module) => base_path("modules/{$module}/config/permissions.php"))
            ->filter(fn (string $path) => File::exists($path))
            ->values()
            ->all();
    }
}
