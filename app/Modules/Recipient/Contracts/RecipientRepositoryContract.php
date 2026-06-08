<?php

namespace App\Modules\Recipient\Contracts;

use App\Models\Recipient;
use App\Modules\Recipient\DTOs\RecipientDTO;

interface RecipientRepositoryContract
{
    public function findById(int $id): ?Recipient;
    public function store(RecipientDTO $dto): Recipient;
    public function update(Recipient $Recipient, RecipientDTO $dto): Recipient;
    public function delete(Recipient $Recipient): void;
}