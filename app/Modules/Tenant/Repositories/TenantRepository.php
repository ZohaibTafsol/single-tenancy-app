<?php

namespace App\Modules\Tenant\Repositories;

use App\Modules\Tenant\Contracts\TenantRepositoryContract;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Modules\Tenant\Models\Tenant;
use App\Modules\Tenant\DTOs\TenantDTO;

class TenantRepository implements TenantRepositoryContract
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Tenant::latest()
            ->when(!empty($filters['name']), fn($q) => $q->where('name', 'LIKE', "%{$filters['name']}%"))
            ->paginate($perPage);
    }

    public function findOrFail(int|string $id): Tenant
    {
        return Tenant::findOrFail($id);
    }
    public function findByIdOrUuid(int|string $id): Tenant
    {
        return Tenant::where('id', $id)->orWhere('uuid', $id)->firstOrFail();
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
