<?php

namespace App\Modules\Payer\Actions;

use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\Exceptions\PayerNotFoundException;
use App\Modules\Payer\Models\Payer;

class UpdatePayerStatusAction
{
    public function __construct(
        private readonly PayerRepositoryContract $payerRepository
    ) {}

    public function execute(string $uuid, bool $isActive): Payer
    {
        $payer = $this->payerRepository->findByUuid($uuid);

        if (! $payer) {
            throw new PayerNotFoundException($uuid);
        }
        return $this->payerRepository->updateStatus($payer, $isActive);
    }
}
