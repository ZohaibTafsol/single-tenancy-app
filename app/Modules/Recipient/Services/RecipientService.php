<?php

namespace App\Modules\Recipient\Services;

use App\Modules\Recipient\Models\Recipient;
use App\Modules\Recipient\Actions\StoreRecipientAction;
use App\Modules\Recipient\Actions\UpdateRecipientAction;
use App\Modules\Recipient\Contracts\RecipientRepositoryContract;
use App\Modules\Recipient\DTOs\RecipientDTO;
use App\Modules\Recipient\Exceptions\RecipientNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RecipientService
{
    public function __construct(
        private readonly StoreRecipientAction  $storeRecipientAction,
        private readonly UpdateRecipientAction $updateRecipientAction,
        private readonly RecipientRepositoryContract $RecipientRepository,
    ) {}

    public function getRecipients(array $filterData): LengthAwarePaginator
    {
        $filterData['user_id'] = auth()->id();
        return $this->RecipientRepository->getRecipients($filterData);
    }
    public function store(RecipientDTO $dto): Recipient
    {
        return $this->storeRecipientAction->execute($dto);
    }

   public function update(int $id, RecipientDTO $dto): Recipient
    {
        return $this->updateRecipientAction->execute($id, $dto);
    }

   public function delete(int $id): void
    {
        $recipient = $this->RecipientRepository->findById($id);

        if (! $recipient) {
            throw new RecipientNotFoundException($id);
        }
        $this->RecipientRepository->delete($recipient);
    }
}