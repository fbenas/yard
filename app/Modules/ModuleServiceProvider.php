<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    abstract public static function moduleKey(): string;

    public function modulePath(): string
    {
        return base_path('modules/' . ucfirst(static::moduleKey()));
    }

    public function moduleConfigPath(string $file): string
    {
        return $this->modulePath() . '/config/' . $file;
    }

    public function moduleRoutesPath(string $file): string
    {
        return $this->modulePath() . '/routes/' . $file;
    }

    public function moduleDatabasePath(string $path = ''): string
    {
        $base = $this->modulePath() . '/database';

        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }

    protected function bootModuleRoutes(): void
    {
        $apiRoutes = $this->moduleRoutesPath('api.php');

        if (file_exists($apiRoutes)) {
            $this->loadRoutesFrom($apiRoutes);
        }
    }

    protected function bootModuleMigrations(): void
    {
        $migrationsPath = $this->moduleDatabasePath('migrations');

        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    protected function mergeModuleConfig(string $file, string $key): void
    {
        $path = $this->moduleConfigPath($file);

        if (file_exists($path)) {
            $this->mergeConfigFrom($path, $key);
        }
    }
}
