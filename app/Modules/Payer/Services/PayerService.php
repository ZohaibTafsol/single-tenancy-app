<?php

namespace App\Modules\Payer\Services;

use App\Models\Payer;
use App\Modules\Payer\Actions\StorePayerAction;
use App\Modules\Payer\Actions\UpdatePayerAction;
use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\DTOs\PayerDTO;
use App\Modules\Payer\Exceptions\PayerNotFoundException;

class PayerService
{
    public function __construct(
        private readonly StorePayerAction  $storePayerAction,
        private readonly UpdatePayerAction $updatePayerAction,
        private readonly PayerRepositoryContract $payerRepository,
    ) {}
    public function getPayers(array $filter_params = []): array
    {
        return $this->payerRepository->getPayers($filter_params);
    }
    public function store(PayerDTO $dto): Payer
    {
        return $this->storePayerAction->execute($dto);
    }

   public function update(int $id, PayerDTO $dto): Payer
    {
        return $this->updatePayerAction->execute($id, $dto);
    }

   public function delete(int $id): void
    {
        $payer = $this->payerRepository->findById($id);

        if (! $payer) {
            throw new PayerNotFoundException($id);
        }

        $this->payerRepository->delete($payer);
    }
}