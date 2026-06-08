<?php

namespace App\Modules\Auth\DTOs;

final readonly class MfaVerifyDTO
{
    public function __construct(
        public string $otp,
        public int    $userId,
        public string $ipAddress,
    ) {}

    public static function fromRequest(array $data, int $userId, string $ip): self
    {
        return new self(
            otp:       $data['otp'],
            userId:    $userId,
            ipAddress: $ip,
        );
    }
}
