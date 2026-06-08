<?php

namespace App\Modules\Recipient\Actions;

use App\Models\Recipient;
use App\Modules\Recipient\Contracts\RecipientRepositoryContract;
use App\Modules\Recipient\DTOs\RecipientDTO;

class StoreRecipientAction
{
    public function __construct(
        private readonly RecipientRepositoryContract $RecipientRepository,
    ) {
    }
    public function execute(RecipientDTO $dto): Recipient
    {
        return $this->RecipientRepository->store($dto);
    }
}