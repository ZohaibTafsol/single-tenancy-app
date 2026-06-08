<?php

namespace App\Modules\Payer\Contracts;

use App\Models\Payer;
use App\Modules\Payer\DTOs\PayerDTO;

interface PayerRepositoryContract
{
    public function findByUuid(string $uuid);
    public function findById(int $id): ?Payer;
    public function store(PayerDTO $dto): Payer;
    public function updateStatus(): Payer;
    public function update(Payer $payer, PayerDTO $dto): Payer;
    public function delete(Payer $payer): void;
}