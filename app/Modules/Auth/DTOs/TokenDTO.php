<?php

namespace App\Modules\Auth\DTOs;

final readonly class TokenDTO
{
    public function __construct(
        public string  $accessToken,
        public ?string $refreshToken,
        public int     $expiresIn,
        public string  $tokenType = 'Bearer',
        public bool    $mfaRequired = false,
        public ?string $tempToken = null,
    ) {}

    public static function mfaPending(string $tempToken): self
    {
        return new self(
            accessToken:  '',
            refreshToken: null,
            expiresIn:    300,
            mfaRequired:  true,
            tempToken:    $tempToken,
        );
    }

    public static function fullAccess(string $accessToken, string $refreshToken): self
    {
        return new self(
            accessToken:  $accessToken,
            refreshToken: $refreshToken,
            expiresIn:    3600,
        );
    }

    public function toArray(): array
    {
        if ($this->mfaRequired) {
            return [
                'mfa_required' => true,
                'temp_token'   => $this->tempToken,
                'message'      => 'Enter your 6-digit authenticator code.',
            ];
        }

        return [
            'mfa_required'  => false,
            'access_token'  => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'token_type'    => $this->tokenType,
            'expires_in'    => $this->expiresIn,
        ];
    }
}
