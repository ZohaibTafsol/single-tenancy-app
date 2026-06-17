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
        $data = $dto->toArray();
        return $tenant->domains()->create([
            "domain" => $data["domain_name"] . "." . config("app.domain")
        ]);
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