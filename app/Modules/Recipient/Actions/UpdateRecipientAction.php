<?php

namespace App\Modules\Recipient\Actions;

use App\Models\Recipient;
use App\Modules\Recipient\Contracts\RecipientRepositoryContract;
use App\Modules\Recipient\DTOs\RecipientDTO;
use App\Modules\Recipient\Exceptions\RecipientNotFoundException;

class UpdateRecipientAction
{
    public function __construct( private readonly RecipientRepositoryContract $RecipientRepository){

    }
    public function execute(int $id, RecipientDTO $dto): Recipient
    {
        $Recipient = $this->RecipientRepository->findById($id);

        if (! $Recipient) {
            throw new RecipientNotFoundException($id);
        }

        return $this->RecipientRepository->update($Recipient, $dto);
    }
}