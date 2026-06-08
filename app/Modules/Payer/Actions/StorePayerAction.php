<?php

namespace App\Modules\Payer\Actions;

use App\Models\Payer;
use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\DTOs\PayerDTO;

class StorePayerAction
{
    public function __construct(
        private readonly PayerRepositoryContract $payerRepository,
    ) {
    }
    public function execute(PayerDTO $dto): Payer
    {
        return $this->payerRepository->store($dto);
    }
}