<?php

namespace App\Modules\Recipient\Contracts;

use App\Modules\Recipient\Models\Recipient;
use App\Modules\Recipient\DTOs\RecipientDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RecipientRepositoryContract
{
    public function getRecipients(array $filterData): LengthAwarePaginator;
    public function findById(int $id): ?Recipient;
    public function store(RecipientDTO $dto): Recipient;
    public function update(Recipient $Recipient, RecipientDTO $dto): Recipient;
    public function delete(Recipient $Recipient): void;
}