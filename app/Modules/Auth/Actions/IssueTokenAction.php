<?php

namespace App\Modules\Auth\Actions;

use App\Models\User;
use App\Modules\Auth\DTOs\TokenDTO;

class IssueTokenAction
{
    private const ACCESS_ABILITY   = 'taxpayer';
    private const REFRESH_ABILITY  = 'refresh';
    private const MFA_ABILITY      = 'mfa:pending';
    private const ACCESS_MINUTES   = 60;
    private const REFRESH_DAYS     = 7;
    private const MFA_PENDING_MINS = 5;

    public function executeMfaPending(User $user): TokenDTO
    {
        $tempToken = $user->createToken(
            name:           'mfa-pending',
            abilities:      [self::MFA_ABILITY],
            expiresAt:      now()->addMinutes(self::MFA_PENDING_MINS)
        )->plainTextToken;

        return TokenDTO::mfaPending($tempToken);
    }

    public function executeFullAccess(User $user): TokenDTO
    {
        $accessToken = $user->createToken(
            name:      'api-access',
            abilities: [self::ACCESS_ABILITY],
            expiresAt: now()->addMinutes(self::ACCESS_MINUTES)
        )->plainTextToken;

        $refreshToken = $user->createToken(
            name:      'refresh',
            abilities: [self::REFRESH_ABILITY],
            expiresAt: now()->addDays(self::REFRESH_DAYS)
        )->plainTextToken;

        return TokenDTO::fullAccess($accessToken, $refreshToken);
    }

    public function executeRefresh(User $user): TokenDTO
    {
        // Revoke old refresh token before issuing new pair
        $user->currentAccessToken()->delete();

        return $this->executeFullAccess($user);
    }
}
