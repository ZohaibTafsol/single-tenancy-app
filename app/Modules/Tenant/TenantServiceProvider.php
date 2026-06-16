<?php

namespace App\Modules\Tenant;

use App\Modules\Tenant\Contracts\TenantRepositoryContract;
use App\Modules\Tenant\Repositories\TenantRepository;
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TenantRepositoryContract::class, TenantRepository::class);
    }
}
