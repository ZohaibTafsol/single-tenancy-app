<?php

namespace App\Modules\Payer\Actions;

use App\Models\Payer;
use App\Modules\Payer\Contracts\PayerRepositoryContract;

class ChangePayerStatusAction
{
    public function __construct(
        private readonly PayerRepositoryContract $payerRepository
    ){}

    public function execute(string $uuid, string $newStatus):Payer
    {
        $payer = $this->payerRepository->findByUuid($uuid);

        return $payer;        
    }
}