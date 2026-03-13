<?php

namespace App\Providers;

use App\Modules\ModuleRegistry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $registry = $this->app->make(ModuleRegistry::class);

        foreach ($registry->all() as $module) {
            $providerPath = $module['provider_path'];
            $providerClass = $module['provider_class'];

            if (! class_exists($providerClass, false)) {
                require_once $providerPath;
            }

            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }

    public function boot(): void
    {
        //
    }
}
