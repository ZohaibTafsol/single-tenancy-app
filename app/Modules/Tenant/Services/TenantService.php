<?php

namespace App\Modules\Tenant\Services;

use App\Modules\Tenant\DTOs\TenantDTO;
use App\Modules\Tenant\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantService 
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Tenant::latest()->paginate($perPage);
    }

    public function findOrFail(int|string $id): Tenant
    {
        return Tenant::findOrFail($id);
    }

    public function create(TenantDTO $dto): Tenant
    {
        return Tenant::create($dto->toArray());
    }

    public function update(Tenant $model, TenantDTO $dto): Tenant
    {
        $model->update($dto->toArray());

        return $model->fresh();
    }

    public function delete(Tenant $model): void
    {
        $model->delete();
    }
}
