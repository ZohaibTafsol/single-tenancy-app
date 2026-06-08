<?php
namespace App\Modules\Payer;

use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\Repositories\PayerRepository;
use Illuminate\Support\ServiceProvider;

class PayerServiceProvider extends ServiceProvider{
    public function register(): void{
        $this->app->bind(PayerRepositoryContract::class, PayerRepository::class);

    }
}