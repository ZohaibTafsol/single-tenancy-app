<?php

namespace App\Modules\Payer\Repositories;

use App\Modules\Payer\Models\Payer;
use App\Modules\Payer\Contracts\PayerRepositoryContract;
use App\Modules\Payer\DTOs\PayerDTO;
use App\Modules\Payer\Constants\PayerConstants;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
    public function getPayers(array $filter_params = []): LengthAwarePaginator
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

        return $query->paginate(PayerConstants::PER_PAGE);
    }
    public function updateStatus(Payer $payer, bool $isActive): Payer
    {
        // Implement status update logic if needed
        $payer->is_active = $isActive;
        $payer->save();
        return $payer->fresh();
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
