<?php

namespace App\Modules\Auth\Contracts;

use App\Models\User;

interface AuthRepositoryContract
{
    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    public function incrementFailedAttempts(User $user): void;

    public function resetFailedAttempts(User $user): void;

    public function lockAccount(User $user, int $minutes): void;

    public function enableMfa(User $user, string $encryptedSecret): void;

    public function disableMfa(User $user): void;

    public function revokeToken(User $user, int $tokenId): void;

    public function revokeAllTokens(User $user): void;
}
