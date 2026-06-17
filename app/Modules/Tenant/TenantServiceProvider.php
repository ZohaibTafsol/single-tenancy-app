<?php

namespace App\Modules\Tenant;

use App\Modules\Tenant\Contracts\{TenantRepositoryContract, DomainRepositoryContract};
use App\Modules\Tenant\Repositories\{TenantRepository, DomainRepository};
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TenantRepositoryContract::class, TenantRepository::class);
        $this->app->bind(DomainRepositoryContract::class, DomainRepository::class);
    }
}
