<?php

namespace App\Modules\Tenant\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Modules\Tenant\DTOs\TenantDTO;
use App\Modules\Tenant\Models\Tenant;

interface TenantRepositoryContract
{
    public function paginate(int $perPage, array $filters): LengthAwarePaginator;
    public function findOrFail(int|string $id): Tenant;
    public function create(TenantDTO $dto): Tenant;
    public function update(Tenant $model, TenantDTO $dto): Tenant;
    public function delete(Tenant $model): void;
}
