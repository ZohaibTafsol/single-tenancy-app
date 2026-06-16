<?php

namespace App\Modules\Tenant\Contracts;

use App\Modules\Tenant\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use App\Modules\Tenant\DTOs\DomainDTO;

interface DomainRepositoryContract
{
    public function create(Tenant $tenant, DomainDTO $dto): Domain;
    public function findOrFail(int|string $id): Domain;
    public function delete(Domain $domain): void;
}
