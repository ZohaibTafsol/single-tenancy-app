<?php

namespace App\Modules\Tenant\DTOs;

class TenantDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['email']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email
        ];
    }
}
