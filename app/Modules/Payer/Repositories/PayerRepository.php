<?php

namespace App\Modules\Payer\Repositories;

use App\Modules\Payer\Models\Payer;
use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\DTOs\PayerDTO;

class PayerRepository implements PayerRepositoryContract
{
    public function findById(int $id): ?Payer
    {
        return Payer::find($id);
    }
    public function create(PayerDTO $dto): Payer
    {
        return Payer::create($dto->toArray());
    }
    public function update(Payer $payer, PayerDTO $dto): Payer
    {
        $payer->update($dto->toArray());
        return $payer->fresh();
    }
    public function getPayers(array $filter_params = []) : \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Payer::query();

        if (isset($filter_params['name'])) {
            $query->where('name', 'like', '%' . $filter_params['name'] . '%');
        }

        if (isset($filter_params['email'])) {
            $query->where('email', 'like', '%' . $filter_params['email'] . '%');
        }

        if (isset($filter_params['user_id'])) {
            $query->where('user_id', $filter_params['user_id']);
        }

        return $query->paginate(10);
    }
    public function updateStatus(): Payer
    {
        // Implement status update logic if needed
        return new Payer(); // Placeholder return
    }
    public function delete(Payer $payer): void
    {
        $payer->delete();
    }
    public function findByUuid(string $uuid): ?Payer
    {
        return Payer::where("uuid", $uuid)->first();
    }
}