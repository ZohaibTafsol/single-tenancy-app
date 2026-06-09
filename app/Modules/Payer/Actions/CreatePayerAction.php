<?php

namespace App\Modules\Payer\Actions;

use App\Modules\Payer\Models\Payer;
use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\DTOs\PayerDTO;
use Illuminate\Support\Facades\DB;

class CreatePayerAction
{
    public function __construct(
        private readonly PayerRepositoryContract $payerRepository,
    ) {
    }
    public function execute(PayerDTO $dto): Payer
    {
        return DB::transaction(function () use ($dto) {
            return $this->payerRepository->create($dto);
        });
    }
}