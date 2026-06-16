<?php

namespace App\Modules\Tenant\Services;

use Stancl\Tenancy\Database\Models\Domain;
use App\Modules\Tenant\Contracts\DomainRepositoryContract;
use App\Modules\Tenant\Models\Tenant;
use App\Modules\Tenant\DTOs\DomainDTO;

class DomainService
{
    public function __construct(
        private readonly DomainRepositoryContract $repository
    ) {}

    public function create(Tenant $tenant, DomainDTO $dto): Domain
    {
        return $this->repository->create($tenant, $dto);
    }

    public function findOrFail(int|string $id): Domain
    {
        return $this->repository->findOrFail($id);
    }

    public function delete(Domain $domain): void
    {
        $this->repository->delete($domain);
    }
}