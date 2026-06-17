<?php

namespace App\Modules\Tenant\Services;

use App\Modules\Tenant\DTOs\{TenantDTO, DomainDTO};
use App\Modules\Tenant\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Modules\Tenant\Contracts\TenantRepositoryContract;
use Illuminate\Support\Facades\DB;

class TenantService
{
    public function __construct(
        private readonly TenantRepositoryContract $repository,
        private readonly DomainService $domainService,
    ) {}

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function findOrFail(int|string $id): Tenant
    {
        return $this->repository->findOrFail($id);
    }

    public function create(TenantDTO $tenantDTO, DomainDTO $domainDTO): Tenant
    {
        return DB::transaction(function () use ($tenantDTO, $domainDTO) {
            $tenant = $this->repository->create($tenantDTO);

            $this->domainService->create($tenant, $domainDTO);

            return $tenant->load('domains');
        });
    }

    public function update(Tenant $model, TenantDTO $dto): Tenant
    {
        return $this->repository->update($model, $dto);
    }

    public function delete(Tenant $model): void
    {
        $this->repository->delete($model);
    }
}
