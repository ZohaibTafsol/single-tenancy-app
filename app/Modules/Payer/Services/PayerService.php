<?php

namespace App\Modules\Payer\Services;

use App\Modules\Payer\Models\Payer;
use App\Modules\Payer\Actions\CreatePayerAction;
use App\Modules\Payer\Actions\UpdatePayerAction;
use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\DTOs\PayerDTO;
use Illuminate\Support\Facades\DB;
use App\Modules\Payer\Exceptions\PayerNotFoundException;

class PayerService
{
    public function __construct(
        private readonly CreatePayerAction  $createPayerAction,
        private readonly UpdatePayerAction $updatePayerAction,
        private readonly PayerRepositoryContract $payerRepository,
    ) {}
    public function getPayers(array $filter_params = []) 
    {
        return $this->payerRepository->getPayers($filter_params);
    }
    public function createPayer(PayerDTO $dto): Payer
    {
        return $this->createPayerAction->execute($dto);
    }

    public function getPayerByUuid(string $uuid): Payer
    {
        $payer = $this->payerRepository->findByUuid($uuid);
        if (! $payer) {
            throw new PayerNotFoundException($uuid);
        }
        return $payer;
    }

    public function update(int $id, PayerDTO $dto): Payer
    {
        return DB::transaction(function () use ($id, $dto) {
            return $this->updatePayerAction->execute($id, $dto);
        });
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
