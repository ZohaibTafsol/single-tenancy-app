<?php
namespace App\Modules\Recipient\Repositories;
use App\Models\Recipient;
use App\Modules\Recipient\Contracts\RecipientRepositoryContract;
use App\Modules\Recipient\DTOs\RecipientDTO;

class RecipientRepository implements RecipientRepositoryContract
{
    public function findById(int $id): ?Recipient
    {
        return Recipient::find($id);
    }
    public function store(RecipientDTO $dto): Recipient
    {
        return Recipient::create($this->toArray($dto));
    }
    public function update(Recipient $recipient, RecipientDTO $dto): Recipient
    {
        $recipient->update($this->toArray($dto));
        return $recipient->fresh();
    }

    public function delete(Recipient $recipient): void
    {
        $recipient->delete();
    }
    private function toArray(RecipientDTO $dto): array
    {
        return [
            'payer_id'=>$dto->payer_id,
            'first_name' => $dto->first_name,
            'middle_name' => $dto->middle_name,
            'suffix' => $dto->suffix,
            'business_name' => $dto->business_name,
            'last_name' => $dto->last_name,
            'attention_to' => $dto->attention_to,
            'ssn' => $dto->ssn,
            'ein' => $dto->ein,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'address1' => $dto->address1,
            'address2' => $dto->address2,
            'city' => $dto->city,
            'state' => $dto->state,
            'zipcode' => $dto->zipcode,
            'country' => $dto->country,
        ];
    }

}