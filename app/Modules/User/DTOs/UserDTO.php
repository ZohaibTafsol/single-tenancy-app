<?php

namespace App\Modules\User\DTOs;

use Illuminate\Support\Facades\Hash;

class UserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $tenantId
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data["name"],
            email: $data["email"],
            password: $data["password"],
            tenantId: $data["tenant_id"] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            "tenant_id" => $this->tenantId
        ];
    }
}
