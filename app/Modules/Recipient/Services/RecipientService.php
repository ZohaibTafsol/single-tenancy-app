<?php

namespace App\Modules\Recipient\Services;

use App\Modules\Recipient\Models\Recipient;
use App\Modules\Recipient\Actions\CreateRecipientAction;
use App\Modules\Recipient\Actions\UpdateRecipientAction;
use App\Modules\Recipient\Contracts\RecipientRepositoryContract;
use App\Modules\Recipient\DTOs\RecipientDTO;
use App\Modules\Recipient\Exceptions\RecipientNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RecipientService
{
    public function __construct(
        private readonly CreateRecipientAction  $createRecipientAction,
        private readonly UpdateRecipientAction $updateRecipientAction,
        private readonly RecipientRepositoryContract $recipientRepository,
    ) {}

    public function getRecipients(array $filterData): LengthAwarePaginator
    {
        $filterData['user_id'] = auth()->id();
        return $this->recipientRepository->getRecipients($filterData);
    }
    public function store(RecipientDTO $dto): Recipient
    {
        return $this->createRecipientAction->execute($dto);
    }

   public function update(int $id, RecipientDTO $dto): Recipient
    {
        return $this->updateRecipientAction->execute($id, $dto);
    }

   public function delete(int $id): void
    {
        $recipient = $this->recipientRepository->findById($id);

        if (! $recipient) {
            throw new RecipientNotFoundException($id);
        }
        $this->recipientRepository->delete($recipient);
    }
}