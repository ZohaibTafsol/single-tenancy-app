<?php

namespace App\Modules\Auth\Repositories;

use App\Models\User;
use App\Modules\Auth\Contracts\AuthRepositoryContract;
use Illuminate\Support\Facades\DB;

class AuthRepository implements AuthRepositoryContract
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function incrementFailedAttempts(User $user): void
    {
        DB::table('users')
            ->where('id', $user->id)
            ->increment('failed_login_attempts');
    }

    public function resetFailedAttempts(User $user): void
    {
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'failed_login_attempts' => 0,
                'locked_until'          => null,
            ]);
    }

    public function lockAccount(User $user, int $minutes = 30): void
    {
        DB::table('users')
            ->where('id', $user->id)
            ->update(['locked_until' => now()->addMinutes($minutes)]);
    }

    public function enableMfa(User $user, string $encryptedSecret): void
    {
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'google2fa_secret' => $encryptedSecret,
                'mfa_enabled'      => true,
            ]);
    }

    public function disableMfa(User $user): void
    {
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'google2fa_secret' => null,
                'mfa_enabled'      => false,
            ]);
    }

    public function revokeToken(User $user, int $tokenId): void
    {
        $user->tokens()->where('id', $tokenId)->delete();
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
