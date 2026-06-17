<?php

namespace App\Modules\User\DTOs;

use App\Modules\Tenant\Models\Tenant;

class TenantUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly int    $tenantId,
    ) {}

    public static function fromTenant(Tenant $tenant, string $password): self
    {
        return new self(
            name:      $tenant->name,
            email:     $tenant->email,
            password:  $password,
            tenantId: $tenant->id,
        );
    }

    public function toArray(): array
    {
        return [
            'name'      => $this->name,
            'email'     => $this->email,
            'password'  => $this->password,
            'tenant_id' => $this->tenantId,
        ];
    }
}