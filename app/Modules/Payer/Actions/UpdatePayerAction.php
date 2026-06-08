<?php

namespace App\Modules\Payer\Actions;

use App\Models\Payer;
use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\DTOs\PayerDTO;
use App\Modules\Payer\Exceptions\PayerNotFoundException;

class UpdatePayerAction
{
    public function __construct( private readonly PayerRepositoryContract $payerRepository){

    }
    public function execute(int $id, PayerDTO $dto): Payer
    {
        $payer = $this->payerRepository->findById($id);

        if (! $payer) {
            throw new PayerNotFoundException($id);
        }

        return $this->payerRepository->update($payer, $dto);
    }
}