<?php
namespace App\Modules\Recipient;

use App\Modules\Recipient\Contracts\RecipientRepositoryContract;
use App\Modules\Recipient\Repositories\RecipientRepository;
use Illuminate\Support\ServiceProvider;

class RecipientServiceProvider extends ServiceProvider{
    public function register(): void{
        $this->app->bind(RecipientRepositoryContract::class, RecipientRepository::class);

    }
}