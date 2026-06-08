<?php

namespace App\Modules\Auth\DTOs;

final readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public string $ipAddress,
        public string $userAgent,
    ) {}

    public static function fromRequest(array $data, string $ip, string $userAgent): self
    {
        return new self(
            email:     $data['email'],
            password:  $data['password'],
            ipAddress: $ip,
            userAgent: $userAgent,
        );
    }
}
