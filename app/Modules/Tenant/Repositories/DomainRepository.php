<?php

namespace App\Modules\Tenant\Repositories;

use App\Modules\Tenant\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use App\Modules\Tenant\DTOs\DomainDTO;
use App\Modules\Tenant\Contracts\DomainRepositoryContract;

class DomainRepository implements DomainRepositoryContract
{
    public function create(Tenant $tenant, DomainDTO $dto): Domain
    {
        return $tenant->domains()->create($dto->toArray());
    }

    public function findOrFail(int|string $id): Domain
    {
        return Domain::findOrFail($id);
    }

    public function delete(Domain $domain): void
    {
        $domain->delete();
    }
}