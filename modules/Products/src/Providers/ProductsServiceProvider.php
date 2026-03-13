<?php

namespace Modules\Products\Providers;

use App\Modules\ModuleServiceProvider;

class ProductsServiceProvider extends ModuleServiceProvider
{
    public static function moduleKey(): string
    {
        return 'products';
    }

    public function register(): void
    {
        $this->mergeModuleConfig('module.php', 'api.modules.products');
    }

    public function boot(): void
    {
        $this->bootModuleRoutes();
        $this->bootModuleMigrations();
    }
}
