<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    private array $modules = [
        \App\Modules\Auth\AuthServiceProvider::class,
        \App\Modules\Payer\PayerServiceProvider::class,
        \App\Modules\Recipient\RecipientServiceProvider::class,
        \App\Modules\Tenant\TenantServiceProvider::class,
        \App\Modules\User\UserServiceProvider::class,
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->modules as $module) {
            $this->app->register($module);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
