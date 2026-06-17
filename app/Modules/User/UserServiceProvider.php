<?php

namespace App\Modules\User;

use App\Modules\User\Contracts\{UserRepositoryContract};
use App\Modules\User\Repositories\{UserRepository};
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryContract::class, UserRepository::class);
    }
}
