<?php
namespace App\Modules\Payer\Repositories;
use App\Models\Payer;
use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\DTOs\PayerDTO;

class PayerRepository implements PayerRepositoryContract
{
    public function findById(int $id): ?Payer
    {
        return Payer::find($id);
    }
    public function store(PayerDTO $dto): Payer
    {
        return Payer::create($this->toArray($dto));
    }
    public function update(Payer $payer, PayerDTO $dto): Payer
    {
        $payer->update($this->toArray($dto));
        return $payer->fresh();
    }

    public function delete(Payer $payer): void
    {
        $payer->delete();
    }
    public function findByUuid(string $uuid): Payer
    {
        return Payer::where("uuid", $uuid)->first();
    }
    private function toArray(PayerDTO $dto): array
    {
        return [
            'first_name' => $dto->first_name,
            'middle_name' => $dto->middle_name,
            'suffix' => $dto->suffix,
            'business_name' => $dto->business_name,
            'last_name' => $dto->last_name,
            'ssn' => $dto->ssn,
            'ein' => $dto->ein,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'disregarded_entity' => $dto->disregarded_entity,
            'address1' => $dto->address1,
            'address2' => $dto->address2,
            'city' => $dto->city,
            'state' => $dto->state,
            'zipcode' => $dto->zipcode,
            'country' => $dto->country,
        ];
    }

}